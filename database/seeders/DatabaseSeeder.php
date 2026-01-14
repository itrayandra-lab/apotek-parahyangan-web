<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\DatabaseSeeder as IndonesiaDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            IndonesiaDatabaseSeeder::class,
            UserSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            ChatbotConfigurationSeeder::class,
            ExpertQuoteSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
