<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:json-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from JSON files to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data migration from JSON files...');

        // Define the source directory (relative to the main project)
        $sourceDir = '../src/data/';

        // Migrate categories
        $this->migrateCategories($sourceDir);

        // Migrate products
        $this->migrateProducts($sourceDir);

        // Migrate sliders
        $this->migrateSliders($sourceDir);

        // Migrate admins (if needed, though we already have a seeder)
        $this->migrateAdmins($sourceDir);

        $this->info('Data migration completed successfully!');
    }

    private function migrateCategories($sourceDir)
    {
        $this->info('Migrating categories...');

        if (! file_exists($sourceDir.'categories.json')) {
            $this->error('categories.json not found in '.$sourceDir);

            return;
        }

        $categoriesData = json_decode(file_get_contents($sourceDir.'categories.json'), true);

        foreach ($categoriesData as $categoryData) {
            // Check if category already exists
            $existingCategory = Category::where('id', $categoryData['id'])->first();

            if (! $existingCategory) {
                Category::create([
                    'id' => $categoryData['id'],
                    'name' => $categoryData['name'],
                    'slug' => $categoryData['slug'],
                    'created_at' => $categoryData['created_at'],
                    'updated_at' => $categoryData['updated_at'],
                ]);

                $this->info("Category '{$categoryData['name']}' migrated.");
            } else {
                $this->info("Category '{$categoryData['name']}' already exists, skipping.");
            }
        }
    }

    private function migrateProducts($sourceDir)
    {
        $this->info('Migrating products...');

        if (! file_exists($sourceDir.'products.json')) {
            $this->error('products.json not found in '.$sourceDir);

            return;
        }

        $productsData = json_decode(file_get_contents($sourceDir.'products.json'), true);

        foreach ($productsData as $productData) {
            // Check if product already exists
            $existingProduct = Product::where('id', $productData['id'])->first();

            if (! $existingProduct) {
                // Check if the category exists
                $categoryExists = Category::where('id', $productData['category'])->exists();

                if (! $categoryExists) {
                    $this->error("Category {$productData['category']} not found for product {$productData['name']}");

                    continue;
                }

                // Copy image file if it exists
                $imagePath = null;
                if (! empty($productData['image'])) {
                    $sourceImagePath = '../src/public/assets/images/'.$productData['image'];
                    if (file_exists($sourceImagePath)) {
                        // Store in Laravel's storage
                        $imagePath = 'products/'.$productData['image'];
                        $destinationPath = storage_path('app/public/'.$imagePath);

                        // Ensure directory exists
                        $destinationDir = dirname($destinationPath);
                        if (! is_dir($destinationDir)) {
                            mkdir($destinationDir, 0755, true);
                        }

                        // Copy the file
                        copy($sourceImagePath, $destinationPath);
                    }
                }

                Product::create([
                    'id' => $productData['id'],
                    'name' => $productData['name'],
                    'category_id' => $productData['category'],
                    'price' => $productData['price'],
                    'description' => $productData['description'],
                    'image' => $imagePath,
                    'lynk_id_link' => $productData['lynk_id_link'],
                    'created_at' => $productData['created_at'],
                    'updated_at' => $productData['updated_at'],
                ]);

                $this->info("Product '{$productData['name']}' migrated.");
            } else {
                $this->info("Product '{$productData['name']}' already exists, skipping.");
            }
        }
    }

    private function migrateSliders($sourceDir)
    {
        $this->info('Migrating sliders...');

        if (! file_exists($sourceDir.'slider.json')) {
            $this->error('slider.json not found in '.$sourceDir);

            return;
        }

        $slidersData = json_decode(file_get_contents($sourceDir.'slider.json'), true);

        foreach ($slidersData as $sliderData) {
            // Check if slider already exists
            $existingSlider = Slider::where('id', $sliderData['id'])->first();

            if (! $existingSlider) {
                // Copy image file if it exists
                $imagePath = null;
                if (! empty($sliderData['image'])) {
                    $sourceImagePath = '../src/public/assets/images/'.$sliderData['image'];
                    if (file_exists($sourceImagePath)) {
                        // Store in Laravel's storage
                        $imagePath = 'sliders/'.$sliderData['image'];
                        $destinationPath = storage_path('app/public/'.$imagePath);

                        // Ensure directory exists
                        $destinationDir = dirname($destinationPath);
                        if (! is_dir($destinationDir)) {
                            mkdir($destinationDir, 0755, true);
                        }

                        // Copy the file
                        copy($sourceImagePath, $destinationPath);
                    }
                }

                Slider::create([
                    'id' => $sliderData['id'],
                    'image' => $imagePath,
                    'order' => $sliderData['order'],
                    'created_at' => $sliderData['created_at'],
                    'updated_at' => $sliderData['updated_at'],
                ]);

                $this->info("Slider item with order {$sliderData['order']} migrated.");
            } else {
                $this->info("Slider item with order {$sliderData['order']} already exists, skipping.");
            }
        }
    }

    private function migrateAdmins($sourceDir)
    {
        $this->info('Migrating admins to users...');

        if (! file_exists($sourceDir.'admins.json')) {
            $this->error('admins.json not found in '.$sourceDir);

            return;
        }

        $adminsData = json_decode(file_get_contents($sourceDir.'admins.json'), true);

        foreach ($adminsData as $adminData) {
            // Check if user already exists
            $existingUser = User::where('id', $adminData['id'])->first();

            if (! $existingUser) {
                User::create([
                    'id' => $adminData['id'],
                    'name' => $adminData['username'],
                    'username' => $adminData['username'],
                    'email' => $adminData['username'].'@beautylatory.local',
                    'password' => $adminData['password_hash'], // Already hashed
                    'role' => 'admin',
                    'created_at' => $adminData['created_at'],
                ]);

                $this->info("Admin '{$adminData['username']}' migrated to users.");
            } else {
                $this->info("User '{$adminData['username']}' already exists, skipping.");
            }
        }
    }
}
