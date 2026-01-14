<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Apartemen', null]),
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->streetAddress(),
            'province_code' => '32',
            'province_name' => 'Jawa Barat',
            'city_code' => '3273',
            'city_name' => 'Kota Bandung',
            'district_code' => '3273010',
            'district_name' => 'Bandung Wetan',
            'village_code' => '3273010001',
            'village_name' => 'Cihapit',
            'postal_code' => fake()->numerify('#####'),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
