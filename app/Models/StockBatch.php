<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    protected $table = 'stock_batches';
    
    protected $fillable = [
        'medicine_id',
        'supplier_id',
        'batch_number',
        'expiration_date',
        'stock_in',
        'current_stock',
        'purchase_price_per_unit',
        'entry_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'entry_date' => 'date',
        'stock_in' => 'integer',
        'current_stock' => 'integer',
        'purchase_price_per_unit' => 'integer',
    ];

    /**
     * Get the medicine for this stock batch
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the supplier for this stock batch
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Check if batch is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if batch is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
    }
}
