<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrescriptionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prescription_id',
        'order_number',
        'total_price',
        'payment_status',
        'payment_gateway',
        'payment_type',
        'payment_url',
        'snap_token',
        'payment_expired_at',
        'payment_callback_data',
        'payment_external_id',
        'pickup_status',
        'qr_code_token',
        'paid_at',
        'ready_at',
        'picked_up_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'ready_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'payment_expired_at' => 'datetime',
        'payment_callback_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->qr_code_token)) {
                $order->qr_code_token = Str::random(32);
            }
            if (empty($order->order_number)) {
                $order->order_number = $order->generateOrderNumber();
            }
        });
    }

    /**
     * Get the user who owns this order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the prescription this order is based on
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the items in this order
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionOrderItem::class);
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if order is ready for pickup
     */
    public function isReady(): bool
    {
        return $this->pickup_status === 'ready';
    }

    /**
     * Check if order has been picked up
     */
    public function isPickedUp(): bool
    {
        return $this->pickup_status === 'picked_up';
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        \App\Models\Sale::recordFromOrder($this);
    }

    /**
     * Mark order as ready for pickup
     */
    public function markAsReady(): void
    {
        $this->update([
            'pickup_status' => 'ready',
            'ready_at' => now(),
        ]);
    }

    /**
     * Mark order as picked up
     */
    public function markAsPickedUp(): void
    {
        $this->update([
            'pickup_status' => 'picked_up',
            'picked_up_at' => now(),
        ]);
    }

    /**
     * Calculate total price from items
     */
    public function calculateTotal(): void
    {
        $this->total_price = $this->items->sum(function ($item) {
            return $item->quantity * $item->price_at_purchase;
        });
        $this->save();
    }

    /**
     * Get QR code data URL
     */
    public function getQrCodeDataUrlAttribute(): string
    {
        // Generate QR code using SimpleSoftwareIO/simple-qrcode
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->generate($this->qr_code_token);
    }

    /**
     * Alias for Midtrans compatibility
     */
    public function getTotalAttribute()
    {
        return (int) $this->total_price;
    }

    /**
     * Generate unique order number for prescription
     */
    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        return sprintf('INV/%s/%04d', $date, $count);
    }

    /**
     * Phone number for Midtrans
     */
    public function getPhoneAttribute()
    {
        return $this->user?->whatsapp ?? $this->user?->phone ?? '-';
    }

    /**
     * Shipping address for Midtrans (Prescription orders are usually pickup, but Midtrans might need it)
     */
    public function getShippingAddressAttribute()
    {
        return 'Ambil di Apotek Parahyangan PVJ';
    }

    public function getShippingCityAttribute() { return 'Bandung'; }
    public function getShippingPostalCodeAttribute() { return '40161'; }
    public function getShippingCostAttribute() { return 0; }
    public function getVoucherDiscountAttribute() { return 0; }
}
