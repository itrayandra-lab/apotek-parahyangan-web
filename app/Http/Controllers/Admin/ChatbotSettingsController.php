<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatbotConfigurationRequest;
use App\Models\ChatbotConfiguration;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatbotWebhookService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatbotSettingsController extends Controller
{
    public function index(): View
    {
        $rawConfigs = ChatbotConfiguration::getAllActive();
        $configs = $this->typedConfigs($rawConfigs);

        $stats = [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'today_messages' => ChatMessage::whereDate('sent_at', today())->count(),
            'chatbot_status' => $configs['chatbot_active'] ?? false,
        ];

        return view('admin.chatbot.settings', [
            'configs' => $configs,
            'stats' => $stats,
        ]);
    }

    public function update(ChatbotConfigurationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            foreach ($validated as $key => $value) {
                ChatbotConfiguration::setValue(
                    $key,
                    $this->normalizeValue($key, $value),
                    ChatbotConfiguration::where('key', $key)->value('description')
                );
            }

            return redirect()
                ->route('admin.chatbot.settings')
                ->with('success', 'Konfigurasi chatbot berhasil diperbarui.');
        } catch (\Exception $exception) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui konfigurasi: '.$exception->getMessage());
        }
    }

    public function testWebhook(Request $request, ChatbotWebhookService $webhookService): JsonResponse
    {
        $configs = ChatbotConfiguration::getAllActive();

        $webhookUrl = (string) ($request->input('webhook_url') ?? $configs['webhook_url'] ?? '');
        $timeout = (int) ($request->input('webhook_timeout') ?? $configs['webhook_timeout'] ?? 30);

        if ($webhookUrl === '') {
            return response()->json([
                'success' => false,
                'message' => 'Webhook URL tidak boleh kosong.',
            ], 422);
        }

        $result = $webhookService->testConnectionWith($webhookUrl, $timeout);

        if ($result['success'] === true) {
            return response()->json([
                'success' => true,
                'message' => 'Webhook berhasil terhubung!',
                'latency' => $result['latency'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Webhook gagal: '.($result['error'] ?? 'Unknown error'),
        ], 400);
    }

    /**
     * @param  array<string, mixed>  $rawConfigs
     * @return array<string, mixed>
     */
    private function typedConfigs(array $rawConfigs): array
    {
        return [
            'chatbot_active' => $this->toBool($rawConfigs['chatbot_active'] ?? config('services.chatbot.active', true)),
            'webhook_enabled' => $this->toBool($rawConfigs['webhook_enabled'] ?? true),
            'webhook_url' => (string) ($rawConfigs['webhook_url'] ?? config('services.chatbot.webhook_url', '')),
            'webhook_timeout' => (int) ($rawConfigs['webhook_timeout'] ?? config('services.chatbot.timeout', 30)),
            'webhook_retry_attempts' => (int) ($rawConfigs['webhook_retry_attempts'] ?? config('services.chatbot.retry_attempts', 3)),
            'guest_session_expiry_days' => (int) ($rawConfigs['guest_session_expiry_days'] ?? config('services.chatbot.guest_expiry_days', 7)),
            'rate_limit_per_minute' => (int) ($rawConfigs['rate_limit_per_minute'] ?? 30),
        ];
    }

    private function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== false;
    }

    private function normalizeValue(string $key, mixed $value): string
    {
        $booleanKeys = ['chatbot_active', 'webhook_enabled'];
        $integerKeys = ['webhook_timeout', 'webhook_retry_attempts', 'guest_session_expiry_days', 'rate_limit_per_minute'];

        if (in_array($key, $booleanKeys, true)) {
            return $value ? 'true' : 'false';
        }

        if (in_array($key, $integerKeys, true)) {
            return (string) (int) $value;
        }

        return (string) $value;
    }
}
