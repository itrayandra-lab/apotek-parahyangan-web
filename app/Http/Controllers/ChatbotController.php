<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotHistoryRequest;
use App\Http\Requests\ChatbotResetRequest;
use App\Http\Requests\ChatbotSendRequest;
use App\Models\ChatbotConfiguration;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatbotWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function status(): JsonResponse
    {
        $activeConfig = ChatbotConfiguration::getValue('chatbot_active', config('services.chatbot.active', true));
        $isActive = filter_var($activeConfig, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return response()->json([
            'active' => $isActive !== false,
            'message' => $isActive === false ? 'Chatbot is disabled' : 'Chatbot is available',
        ]);
    }

    public function session(Request $request): JsonResponse
    {
        $requestedSessionId = (string) $request->string('session_id')->trim()->value();
        $session = $this->findSession($request, $requestedSessionId);

        if ($session === null || $session->isExpired()) {
            if ($session !== null) {
                $session->update(['status' => 'expired']);
            }

            $session = $this->createSession($request);
        }

        $session->updateActivity();

        return response()->json($this->sessionPayload($session));
    }

    public function history(ChatbotHistoryRequest $request): JsonResponse
    {
        $session = $this->findSessionById((string) $request->string('session_id')->value(), $request);

        if ($session === null) {
            return response()->json(['message' => 'Session not found.'], 404);
        }

        if ($session->isExpired()) {
            return response()->json(['message' => 'Session expired. Please start a new chat.'], 410);
        }

        $limit = $request->integer('limit', 50);

        $messages = $session->messages()
            ->orderBy('sent_at')
            ->limit($limit)
            ->get()
            ->map(fn (ChatMessage $message): array => [
                'id' => $message->id,
                'type' => $message->type,
                'content' => $message->content,
                'metadata' => $message->metadata,
                'sent_at' => $message->sent_at,
            ]);

        $session->updateActivity();

        return response()->json([
            'session' => $this->sessionPayload($session),
            'messages' => $messages,
        ]);
    }

    public function send(ChatbotSendRequest $request, ChatbotWebhookService $webhookService): JsonResponse
    {
        $session = $this->findSessionById((string) $request->string('session_id')->value(), $request);

        if ($session === null) {
            return response()->json(['message' => 'Session not found.'], 404);
        }

        if ($session->isExpired()) {
            return response()->json(['message' => 'Session expired. Please start a new chat.'], 410);
        }

        $session->updateActivity();

        $userMessage = $session->messages()->create([
            'user_id' => auth()->id(),
            'type' => 'user',
            'content' => (string) $request->string('message')->value(),
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'sent_at' => now(),
        ]);

        $botReply = $webhookService->sendMessage(
            $session->session_id,
            $userMessage->content,
            auth()->id()
        );

        $botMessage = $session->messages()->create([
            'user_id' => null,
            'type' => 'bot',
            'content' => $botReply,
            'metadata' => [],
            'sent_at' => now(),
        ]);

        return response()->json([
            'session' => $this->sessionPayload($session),
            'messages' => [
                [
                    'id' => $userMessage->id,
                    'type' => $userMessage->type,
                    'content' => $userMessage->content,
                    'sent_at' => $userMessage->sent_at,
                ],
                [
                    'id' => $botMessage->id,
                    'type' => $botMessage->type,
                    'content' => $botMessage->content,
                    'sent_at' => $botMessage->sent_at,
                ],
            ],
        ]);
    }

    public function reset(ChatbotResetRequest $request): JsonResponse
    {
        $existingSession = $this->findSessionById((string) $request->string('session_id')->value(), $request);

        if ($existingSession !== null) {
            $existingSession->close();
        }

        $newSession = $this->createSession($request);

        return response()->json($this->sessionPayload($newSession));
    }

    private function findSession(Request $request, ?string $sessionId = null): ?ChatSession
    {
        if ($request->user() !== null) {
            return ChatSession::where('user_id', $request->user()->id)
                ->when($sessionId, fn ($query) => $query->where('session_id', $sessionId))
                ->latest('last_activity_at')
                ->first();
        }

        if ($sessionId !== null && $sessionId !== '') {
            return ChatSession::where('session_id', $sessionId)
                ->where('is_guest', true)
                ->first();
        }

        return null;
    }

    private function findSessionById(string $sessionId, Request $request): ?ChatSession
    {
        $sessionId = trim($sessionId);

        if ($sessionId === '') {
            return null;
        }

        $session = ChatSession::where('session_id', $sessionId)->first();

        if ($session === null) {
            return null;
        }

        if ($request->user() !== null && $session->user_id !== $request->user()->id) {
            return null;
        }

        if ($request->user() === null && $session->is_guest !== true) {
            return null;
        }

        return $session;
    }

    private function createSession(Request $request): ChatSession
    {
        $expiresInDays = (int) ChatbotConfiguration::getValue(
            'guest_session_expiry_days',
            config('services.chatbot.guest_expiry_days', 7)
        );

        if ($request->user() !== null) {
            return ChatSession::create([
                'user_id' => $request->user()->id,
                'session_id' => 'user_'.Str::uuid(),
                'status' => 'active',
                'last_activity_at' => now(),
                'is_guest' => false,
                'ip_address' => $request->ip(),
                'expires_at' => null,
                'metadata' => [],
            ]);
        }

        return ChatSession::create([
            'user_id' => null,
            'session_id' => 'guest_'.Str::uuid(),
            'status' => 'active',
            'last_activity_at' => now(),
            'is_guest' => true,
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays($expiresInDays > 0 ? $expiresInDays : 7),
            'metadata' => [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function sessionPayload(ChatSession $session): array
    {
        return [
            'session_id' => $session->session_id,
            'status' => $session->status,
            'is_guest' => $session->is_guest,
            'expires_at' => $session->expires_at,
        ];
    }
}
