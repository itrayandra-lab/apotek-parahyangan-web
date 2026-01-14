<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpertQuote>
 */
class ExpertQuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quote' => fake()->realText(180),
            'author_name' => fake()->name(),
            'author_title' => strtoupper(fake()->jobTitle()),
            'is_active' => fake()->boolean(90),
        ];
    }
}
