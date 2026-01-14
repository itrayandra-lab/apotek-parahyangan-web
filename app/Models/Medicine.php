<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Medicine extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'medicines';
    
    protected $fillable = [
        'category_id',
        'main_supplier_id',
        'code',
        'name',
        'manufacturer',
        'indication',
        'composition',
        'classification',
        'dosage',
        'side_effects',
        'bpom_number',
        'shelf_location',
        'total_stock_unit',
        'min_stock_alert',
        'base_unit',
        'last_purchase_price',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'total_stock_unit' => 'integer',
        'min_stock_alert' => 'integer',
    ];

    /**
     * Get the code as slug for compatibility with Shop views.
     */
    public function getSlugAttribute(): string
    {
        return $this->code;
    }

    /**
     * Get price for compatibility with Shop views.
     */
    public function getPriceAttribute(): int
    {
        // Use the highest unit price as the base price or default to 0
        return $this->medicineUnits->sortByDesc('selling_price_general')->first()?->selling_price_general ?? 0;
    }

    /**
     * Get discount price for compatibility with Shop views.
     */
    public function getDiscountPriceAttribute(): ?int
    {
        return null; // POS doesn't seem to have direct discounts in the same schema
    }

    /**
     * Define media collections for this model
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('medicine_images');
    }

    /**
     * Check if medicine has image.
     */
    public function hasImage(): bool
    {
        return $this->hasMedia('medicine_images');
    }

    /**
     * Get image HTML placeholder or HTML component.
     */
    public function getImage(): string
    {
        if ($this->hasImage()) {
            return '<img src="' . $this->getImageUrl() . '" alt="' . e($this->name) . '" class="w-full h-full object-cover">';
        }

        return '<div class="w-full h-full bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>';
    }

    /**
     * Get main product image URL
     */
    public function getImageUrl(string $conversion = ''): ?string
    {
        $url = $this->getFirstMediaUrl('medicine_images', $conversion);
        
        if ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path ? '/' . ltrim($path, '/') : null;
        }
        
        return null;
    }

    /**
     * Get the category that owns the medicine
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    /**
     * Get the main supplier for the medicine
     */
    public function mainSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'main_supplier_id');
    }

    /**
     * Get the medicine units for the medicine
     */
    public function medicineUnits(): HasMany
    {
        return $this->hasMany(MedicineUnit::class);
    }

    /**
     * Get the stock batches for the medicine
     */
    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    /**
     * Helper to get total stock based on smallest unit
     */
    public function getTotalStockUnitAttribute(): int
    {
        return $this->stockBatches()->sum('current_stock');
    }

    /**
     * Check if medicine is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->total_stock_unit <= 10;
    }

    /**
     * Check if medicine is in stock
     */
    public function inStock(): bool
    {
        return $this->total_stock_unit > 0;
    }

    /**
     * Get classification color class
     */
    public function getClassificationColorAttribute(): string
    {
        return match ($this->classification) {
            'Bebas' => 'bg-green-100 text-green-700',
            'Bebas Terbatas' => 'bg-blue-100 text-blue-700',
            'Obat Keras' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get classification dot color
     */
    public function getClassificationDotColorAttribute(): string
    {
        return match ($this->classification) {
            'Bebas' => 'bg-green-500',
            'Bebas Terbatas' => 'bg-blue-500',
            'Obat Keras' => 'bg-red-500',
            default => 'bg-gray-400',
        };
    }

    /**
     * Format price to IDR
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->price, 0, ',', '.');
    }

    /**
     * Scope to get medicines in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('total_stock_unit', '>', 0);
    }

    /**
     * Scope to get low stock medicines
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('total_stock_unit', '<=', 'min_stock_alert');
    }
}
