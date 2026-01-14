<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'status',
        'position',
        'order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Define media collections for this model
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slider_images')
            ->singleFile();
    }

    /**
     * Define media conversions
     * Using Spatie's built-in responsive images
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // No conversions needed - Spatie will auto-generate responsive sizes
        // The original image will be served with responsive srcset
    }

    /**
     * Get slider image URL
     */
    public function getImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('slider_images');
        
        if ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path ? '/' . ltrim($path, '/') : null;
        }
        
        return null;
    }

    /**
     * Get slider image for responsive display
     */
    public function getImage()
    {
        return $this->getFirstMedia('slider_images');
    }

    /**
     * Check if slider has image
     */
    public function hasImage(): bool
    {
        return $this->hasMedia('slider_images');
    }

    /**
     * Scope a query to order sliders by order field.
     */
    public function scopeOrderedByOrder($query)
    {
        return $query->orderBy('position', 'asc')->orderBy('created_at', 'asc');
    }

    /**
     * Scope active sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
