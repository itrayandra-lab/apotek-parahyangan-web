<?php

namespace Database\Seeders;

use App\Models\ChatbotConfiguration;
use Illuminate\Database\Seeder;

class ChatbotConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'key' => 'chatbot_active',
                'value' => 'true',
                'description' => 'Enable or disable the chatbot globally',
                'is_active' => true,
            ],
            [
                'key' => 'webhook_enabled',
                'value' => 'true',
                'description' => 'Enable or disable webhook integration',
                'is_active' => true,
            ],
            [
                'key' => 'webhook_url',
                'value' => '',
                'description' => 'n8n webhook URL for AI processing',
                'is_active' => true,
            ],
            [
                'key' => 'webhook_timeout',
                'value' => '30',
                'description' => 'Webhook request timeout in seconds',
                'is_active' => true,
            ],
            [
                'key' => 'webhook_retry_attempts',
                'value' => '3',
                'description' => 'Number of retry attempts for failed webhook requests',
                'is_active' => true,
            ],
            [
                'key' => 'guest_session_expiry_days',
                'value' => '7',
                'description' => 'Number of days before guest sessions expire',
                'is_active' => true,
            ],
            [
                'key' => 'rate_limit_per_minute',
                'value' => '30',
                'description' => 'Maximum messages per minute per user',
                'is_active' => true,
            ],
        ];

        foreach ($configurations as $config) {
            ChatbotConfiguration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
