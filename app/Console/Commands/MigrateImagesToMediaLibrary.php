<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Slider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateImagesToMediaLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-images
                          {--dry-run : Run without making changes}
                          {--model= : Only migrate specific model (product or slider)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing product and slider images to Spatie Media Library';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $modelFilter = $this->option('model');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting image migration...');
        $this->newLine();

        // Migrate products
        if (! $modelFilter || $modelFilter === 'product') {
            $this->migrateProducts($isDryRun);
        }

        // Migrate sliders
        if (! $modelFilter || $modelFilter === 'slider') {
            $this->migrateSliders($isDryRun);
        }

        $this->newLine();
        $this->info('Migration completed!');

        return self::SUCCESS;
    }

    private function migrateProducts(bool $isDryRun): void
    {
        $this->info('Migrating product images...');

        $products = Product::whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        $this->info("Found {$products->count()} products with images");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($products as $product) {
            // Build full path - try both with and without 'storage/' prefix
            $possiblePaths = [
                public_path($product->image),
                public_path('storage/'.$product->image),
                storage_path('app/public/'.str_replace('storage/', '', $product->image)),
            ];

            $oldImagePath = null;
            foreach ($possiblePaths as $path) {
                if (File::exists($path)) {
                    $oldImagePath = $path;
                    break;
                }
            }

            if (! $oldImagePath) {
                $errors[] = "Product #{$product->id}: Image file not found (tried multiple paths for {$product->image})";
                $errorCount++;
                $bar->advance();

                continue;
            }

            if (! $isDryRun) {
                try {
                    // Check if media already exists
                    if ($product->hasMedia('product_images')) {
                        $bar->advance();

                        continue;
                    }

                    // Add media to library
                    $product->addMedia($oldImagePath)
                        ->preservingOriginal() // Keep original file for now
                        ->toMediaCollection('product_images');

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Product #{$product->id}: {$e->getMessage()}";
                    $errorCount++;
                }
            } else {
                $successCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Products migrated: {$successCount}");
        if ($errorCount > 0) {
            $this->error("Products failed: {$errorCount}");
            foreach ($errors as $error) {
                $this->warn("  - {$error}");
            }
        }
    }

    private function migrateSliders(bool $isDryRun): void
    {
        $this->info('Migrating slider images...');

        $sliders = Slider::whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        $this->info("Found {$sliders->count()} sliders with images");

        $bar = $this->output->createProgressBar($sliders->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($sliders as $slider) {
            // Build full path - try both with and without 'storage/' prefix
            $possiblePaths = [
                public_path($slider->image),
                public_path('storage/'.$slider->image),
                storage_path('app/public/'.str_replace('storage/', '', $slider->image)),
            ];

            $oldImagePath = null;
            foreach ($possiblePaths as $path) {
                if (File::exists($path)) {
                    $oldImagePath = $path;
                    break;
                }
            }

            if (! $oldImagePath) {
                $errors[] = "Slider #{$slider->id}: Image file not found (tried multiple paths for {$slider->image})";
                $errorCount++;
                $bar->advance();

                continue;
            }

            if (! $isDryRun) {
                try {
                    // Check if media already exists
                    if ($slider->hasMedia('slider_images')) {
                        $bar->advance();

                        continue;
                    }

                    // Add media to library
                    $slider->addMedia($oldImagePath)
                        ->preservingOriginal() // Keep original file for now
                        ->toMediaCollection('slider_images');

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Slider #{$slider->id}: {$e->getMessage()}";
                    $errorCount++;
                }
            } else {
                $successCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sliders migrated: {$successCount}");
        if ($errorCount > 0) {
            $this->error("Sliders failed: {$errorCount}");
            foreach ($errors as $error) {
                $this->warn("  - {$error}");
            }
        }
    }
}
