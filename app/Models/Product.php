<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Sluggable;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::deleted(function (Product $product) {
            // Auto-remove from carts when product is soft-deleted
            CartItem::where('product_id', $product->id)->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'price',
        'discount_price',
        'stock',
        'weight',
        'status',
        'is_featured',
        'description',
        'lynk_id_link',
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
        $this->addMediaCollection('product_images')
            ->singleFile();
    }

    /**
     * Define media conversions (image transformations)
     * Using Spatie's built-in responsive images - no manual sizing needed
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // No conversions needed - Spatie will auto-generate responsive sizes
        // The original image will be served with responsive srcset
    }

    /**
     * Get main product image URL
     *
     * @param  string  $conversion  Optional conversion name
     */
    public function getImageUrl(string $conversion = ''): ?string
    {
        $url = $this->getFirstMediaUrl('product_images', $conversion);
        
        if ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path ? '/' . ltrim($path, '/') : null;
        }
        
        return null;
    }

    /**
     * Get product image HTML for responsive display
     */
    public function getImage(): ?string
    {
        if ($this->hasImage()) {
            return '<img src="' . $this->getImageUrl() . '" alt="' . e($this->name) . '" class="w-full h-full object-cover">';
        }
        return null;
    }

    /**
     * Check if product has image
     */
    public function hasImage(): bool
    {
        return $this->hasMedia('product_images');
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => false,
            ],
        ];
    }

    /**
     * Scope a query to order products by creation date (newest first).
     */
    public function scopeOrderedByNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include published products.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    public function getCurrentPrice(): int
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentage(): ?int
    {
        if ($this->discount_price === null || $this->discount_price >= $this->price) {
            return null;
        }

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }
}
