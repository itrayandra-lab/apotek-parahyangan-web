<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineUnit extends Model
{
    protected $table = 'medicine_units';
    
    protected $fillable = [
        'medicine_id',
        'unit_name',
        'conversion_quantity',
        'is_base',
        'profit_margin_general',
        'profit_margin_prescription',
        'selling_price_general',
        'selling_price_prescription',
        'barcode',
    ];

    protected $casts = [
        'is_base' => 'boolean',
        'conversion_quantity' => 'integer',
        'profit_margin_general' => 'decimal:2',
        'profit_margin_prescription' => 'decimal:2',
        'selling_price_general' => 'integer',
        'selling_price_prescription' => 'integer',
    ];

    /**
     * Get the medicine that owns the unit
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get formatted selling price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price_general, 0, ',', '.');
    }
}
