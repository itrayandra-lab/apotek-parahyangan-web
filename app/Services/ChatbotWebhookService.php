<?php

namespace App\Services;

use App\Models\ChatbotConfiguration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotWebhookService
{
    private string $webhookUrl;

    private int $timeout;

    private int $retryAttempts;

    public function __construct()
    {
        $this->webhookUrl = (string) ChatbotConfiguration::getValue(
            'webhook_url',
            config('services.chatbot.webhook_url', '')
        );

        $this->timeout = (int) ChatbotConfiguration::getValue(
            'webhook_timeout',
            config('services.chatbot.timeout', 30)
        );

        $this->retryAttempts = max(1, (int) ChatbotConfiguration::getValue(
            'webhook_retry_attempts',
            config('services.chatbot.retry_attempts', 3)
        ));
    }

    public function sendMessage(string $sessionId, string $message, ?int $userId = null): string
    {
        if ($this->webhookUrl === '') {
            Log::error('Chatbot webhook URL not configured');

            return "I'm currently unavailable. Please try again later.";
        }

        $payload = [
            'session_id' => $sessionId,
            'chatInput' => $message,
        ];

        try {
            $response = $this->makeRequest($payload);

            Log::info('Chatbot webhook response received', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'response_length' => strlen($response),
            ]);

            return $response;
        } catch (\Exception $exception) {
            Log::error('Chatbot webhook request failed', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);

            return "Sorry, I'm having trouble connecting right now. Please try again shortly.";
        }
    }

    private function makeRequest(array $payload, int $attempt = 1): string
    {
        try {
            $response = Http::timeout($this->timeout)->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                $data = $response->json() ?? [];

                return $data['output'] ?? $data['response'] ?? 'I received your message but had trouble understanding it.';
            }

            if ($response->status() >= 400 && $response->status() < 500) {
                throw new \Exception('Client error: '.$response->status());
            }

            if ($attempt < $this->retryAttempts) {
                sleep(2 ** ($attempt - 1));

                return $this->makeRequest($payload, $attempt + 1);
            }

            throw new \Exception('Max retry attempts reached');
        } catch (\Exception $exception) {
            if ($attempt < $this->retryAttempts) {
                sleep(2 ** ($attempt - 1));

                return $this->makeRequest($payload, $attempt + 1);
            }

            throw $exception;
        }
    }

    /**
     * @return array{success: bool, message: string, response?: mixed}
     */
    public function testConnection(): array
    {
        if ($this->webhookUrl === '') {
            return [
                'success' => false,
                'message' => 'Webhook URL not configured',
            ];
        }

        $result = $this->testConnectionWith($this->webhookUrl, $this->timeout);

        if ($result['success'] === true) {
            return [
                'success' => true,
                'message' => 'Webhook connection successful',
                'response' => [
                    'latency' => $result['latency'],
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'Connection failed: '.($result['error'] ?? 'Unknown error'),
        ];
    }

    /**
     * Test webhook connection with custom target and timeout.
     *
     * @return array{success: bool, error: string|null, latency: int|null}
     */
    public function testConnectionWith(string $url, int $timeout = 30): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($timeout)->post($url, [
                'session_id' => 'test_connection',
                'chatInput' => 'Hello, this is a test message.',
            ]);

            $latency = (int) round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'latency' => $latency,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'latency' => $latency,
                'error' => 'HTTP '.$response->status().': '.$response->body(),
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'latency' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
