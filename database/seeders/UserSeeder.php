<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@beautylatory.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create content manager user
        User::create([
            'name' => 'Content Manager',
            'username' => 'content',
            'email' => 'content@beautylatory.local',
            'password' => Hash::make('password'),
            'role' => 'content_manager',
            'email_verified_at' => now(),
        ]);

        // Create customer user
        User::create([
            'name' => 'Customer User',
            'username' => 'customer',
            'email' => 'customer@beautylatory.local',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create additional customers
        User::factory(5)->create(['role' => 'user']);
    }
}
