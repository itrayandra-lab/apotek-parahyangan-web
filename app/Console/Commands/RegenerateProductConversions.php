<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Slider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateProductConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:regenerate-conversions {--model=all : Model to regenerate (product, slider, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old conversions and regenerate with new sizes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $model = $this->option('model') ?? 'all';

        if (in_array($model, ['product', 'all'])) {
            $this->regenerateProductConversions();
        }

        if (in_array($model, ['slider', 'all'])) {
            $this->regenerateSliderConversions();
        }

        $this->info('✅ Conversion regeneration complete!');
    }

    /**
     * Regenerate product image conversions
     */
    private function regenerateProductConversions(): void
    {
        $products = Product::with('media')->get();

        if ($products->isEmpty()) {
            $this->warn('No products with media found.');

            return;
        }

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            if ($product->hasMedia('product_images')) {
                $media = $product->getFirstMedia('product_images');

                // Delete conversions directory
                $conversionsPath = "product_images/{$media->id}/conversions";
                if (Storage::disk('public')->exists($conversionsPath)) {
                    Storage::disk('public')->deleteDirectory($conversionsPath);
                }

                // Trigger regeneration by deleting and re-adding
                $originalPath = $media->getPath();

                if (file_exists($originalPath)) {
                    $product->addMedia($originalPath)
                        ->toMediaCollection('product_images');
                    $media->delete();
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Regenerated {$products->count()} product conversions");
    }

    /**
     * Regenerate slider image conversions
     */
    private function regenerateSliderConversions(): void
    {
        $sliders = Slider::with('media')->get();

        if ($sliders->isEmpty()) {
            $this->warn('No sliders with media found.');

            return;
        }

        $bar = $this->output->createProgressBar($sliders->count());
        $bar->start();

        foreach ($sliders as $slider) {
            if ($slider->hasMedia('slider_images')) {
                $media = $slider->getFirstMedia('slider_images');

                // Delete conversions directory
                $conversionsPath = "slider_images/{$media->id}/conversions";
                if (Storage::disk('public')->exists($conversionsPath)) {
                    Storage::disk('public')->deleteDirectory($conversionsPath);
                }

                // Trigger regeneration by deleting and re-adding
                $originalPath = $media->getPath();

                if (file_exists($originalPath)) {
                    $slider->addMedia($originalPath)
                        ->toMediaCollection('slider_images');
                    $media->delete();
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Regenerated {$sliders->count()} slider conversions");
    }
}
