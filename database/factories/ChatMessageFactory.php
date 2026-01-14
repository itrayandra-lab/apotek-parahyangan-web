<?php

namespace Database\Factories;

use App\Models\ChatSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_session_id' => ChatSession::factory(),
            'user_id' => null,
            'type' => 'user',
            'content' => $this->faker->sentence(8),
            'metadata' => [],
            'sent_at' => now(),
        ];
    }
}
