<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Sync new messages for a session.
     */
    public function sync(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');
        $lastId = $request->integer('last_id', 0);

        if (!$sessionId) {
            return response()->json(['message' => 'Session ID is required.'], 400);
        }

        $session = ChatSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return response()->json(['message' => 'Session not found.'], 404);
        }

        // Security check: if user is logged in, ensure they own the session
        if (auth()->check() && $session->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $newMessages = $session->messages()
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (ChatMessage $message) {
                $item = [
                    'id' => $message->id,
                    'type' => $message->type,
                    'content' => $message->content,
                    'metadata' => $message->metadata,
                    'sent_at' => $message->sent_at,
                ];

                if ($message->type === 'product' && isset($message->metadata['product_id'])) {
                    $modelType = $message->metadata['model_type'] ?? 'Medicine';
                    $product = null;

                    if ($modelType === 'Product') {
                        $product = \App\Models\Product::find($message->metadata['product_id']);
                    } else {
                        $product = \App\Models\Medicine::find($message->metadata['product_id']);
                    }

                    if ($product) {
                        $item['product'] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'formatted_price' => $product->formatted_price ?? 'Rp ' . number_format($product->price ?? 0, 0, ',', '.'),
                            'image_url' => method_exists($product, 'getImageUrl') ? $product->getImageUrl() : null,
                            'slug' => $modelType === 'Medicine' ? ($product->code ?? $product->slug) : $product->slug,
                        ];
                    } else {
                        // Fallback to metadata values if product disappeared from DB
                        $item['product'] = [
                            'id' => $message->metadata['product_id'] ?? 0,
                            'name' => $message->metadata['product_name'] ?? $message->content,
                            'price' => $message->metadata['price'] ?? 0,
                            'formatted_price' => 'Rp ' . number_format($message->metadata['price'] ?? 0, 0, ',', '.'),
                            'image_url' => $message->metadata['image_url'] ?? null,
                            'slug' => $message->metadata['slug'] ?? '',
                        ];
                    }
                }

                return $item;
            });

        $session->updateActivity();

        return response()->json([
            'new_messages' => $newMessages,
            'last_id' => $newMessages->last()['id'] ?? $lastId,
        ]);
    }

    /**
     * Send a message to the session.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $sessionId = $request->input('session_id');
        $session = ChatSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return response()->json(['message' => 'Session not found.'], 404);
        }

        // Security check
        if (auth()->check() && $session->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $message = $session->messages()->create([
            'user_id' => auth()->id(),
            'type' => 'user',
            'content' => $request->input('message'),
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'sent_at' => now(),
        ]);

        $session->updateActivity();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'type' => $message->type,
                'content' => $message->content,
                'sent_at' => $message->sent_at,
            ]
        ]);
    }
}
