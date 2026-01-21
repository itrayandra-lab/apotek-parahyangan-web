<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'user_id',
        'total_amount',
        'payment_status',
        'payment_method',
        'order_type',
        'reference_id',
        'reference_number',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Generate a unique sale number
     */
    public static function generateSaleNumber(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        return 'INV/' . $date . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a sale from an Order or PrescriptionOrder
     */
    public static function recordFromOrder($order): ?self
    {
        $type = $order instanceof Order ? 'shop' : 'prescription';
        
        // Find or create sale
        $sale = self::updateOrCreate(
            [
                'order_type' => $type,
                'reference_id' => $order->id,
            ],
            [
                'sale_number' => self::generateSaleNumber(),
                'user_id' => $order->user_id,
                'total_amount' => $order->total_price ?? $order->total,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_type ?? $order->payment_gateway,
                'reference_number' => $order->order_number,
                'paid_at' => $order->paid_at,
            ]
        );

        // Sync details
        $sale->details()->delete();
        
        foreach ($order->items as $item) {
            $sale->details()->create([
                'product_id' => $item->product_id,
                'medicine_id' => $item->medicine_id ?? null,
                'item_name' => $item->product_name ?? $item->name,
                'quantity' => $item->quantity,
                'price' => $item->product_price ?? $item->price_at_purchase,
                'subtotal' => $item->subtotal,
            ]);
        }

        return $sale;
    }
}
