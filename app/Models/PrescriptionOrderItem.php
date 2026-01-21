<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_order_id',
        'product_id',
        'medicine_id',
        'custom_name',
        'quantity',
        'price_at_purchase',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_purchase' => 'decimal:2',
    ];

    /**
     * Get the order this item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PrescriptionOrder::class, 'prescription_order_id');
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the medicine
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the name of the item (either product or medicine)
     */
    public function getNameAttribute(): string
    {
        if ($this->custom_name) {
            return $this->custom_name;
        }
        if ($this->medicine_id) {
            return $this->medicine->name ?? 'Unknown Medicine';
        }
        return $this->product->name ?? 'Unknown Product';
    }

    /**
     * Get the subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price_at_purchase;
    }
}
