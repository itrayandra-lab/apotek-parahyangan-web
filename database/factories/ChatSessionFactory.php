<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatSession>
 */
class ChatSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isGuest = $this->faker->boolean();

        return [
            'user_id' => $isGuest ? null : User::factory(),
            'session_id' => $this->faker->uuid(),
            'status' => 'active',
            'last_activity_at' => now(),
            'is_guest' => $isGuest,
            'ip_address' => $this->faker->ipv4(),
            'expires_at' => $isGuest ? now()->addDays(7) : null,
            'metadata' => [],
        ];
    }

    public function guest(): self
    {
        return $this->state(fn (): array => [
            'user_id' => null,
            'is_guest' => true,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function forUser(User $user): self
    {
        return $this->state(fn (): array => [
            'user_id' => $user->id,
            'is_guest' => false,
            'expires_at' => null,
        ]);
    }
}
