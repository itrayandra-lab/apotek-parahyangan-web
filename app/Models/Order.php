<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'shipping_cost',
        'voucher_code',
        'voucher_discount',
        'shipping_courier',
        'shipping_service',
        'shipping_etd',
        'shipping_awb',
        'shipping_weight',
        'rajaongkir_destination_id',
        'total',
        'payment_status',
        'payment_gateway',
        'payment_type',
        'payment_external_id',
        'payment_url',
        'snap_token',
        'payment_expired_at',
        'payment_callback_data',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_district',
        'shipping_village',
        'shipping_postal_code',
        'phone',
        'notes',
        'province_code',
        'city_code',
        'district_code',
        'village_code',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'voucher_discount' => 'integer',
            'total' => 'integer',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_expired_at' => 'datetime',
            'payment_callback_data' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->order_number) {
                $order->order_number = $order->generateOrderNumber();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('payment_status', 'failed');
    }

    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        $lastOrder = self::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastOrder ? ((int) substr($lastOrder->order_number, -4)) + 1 : 1;

        return sprintf('ORD-%s-%04d', $date, $sequence);
    }

    public function markAsPaid(?CarbonInterface $paidAt = null): void
    {
        $this->payment_status = 'paid';
        $this->paid_at = $paidAt ?? now();
        $this->save();
    }

    public function markAsShipped(?CarbonInterface $shippedAt = null): void
    {
        $this->status = 'shipped';
        $this->shipped_at = $shippedAt ?? now();
        $this->save();
    }

    public function markAsDelivered(?CarbonInterface $deliveredAt = null): void
    {
        $this->status = 'delivered';
        $this->delivered_at = $deliveredAt ?? now();
        $this->save();
    }

    public function cancel(?string $reason = null): void
    {
        if (! $this->canBeCancelled()) {
            throw new \DomainException('Order cannot be cancelled at this stage');
        }

        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->save();

        foreach ($this->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isShipped(): bool
    {
        return in_array($this->status, ['shipped', 'delivered'], true);
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_payment', 'confirmed'], true);
    }

    public function getCustomerName(): string
    {
        return $this->user?->name ?? 'User';
    }

    public function getCustomerEmail(): string
    {
        return $this->user?->email ?? '';
    }

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function restoreStock(): void
    {
        $this->loadMissing('items.product');

        foreach ($this->items as $item) {
            $item->product?->increment('stock', $item->quantity);
        }
    }

    public function markPaid(): void
    {
        $this->payment_status = 'paid';
        $this->paid_at = now();
        $this->status = $this->status === 'pending_payment' ? 'confirmed' : $this->status;
    }

    public function expireIfDue(): bool
    {
        if (! $this->isPendingPayment()) {
            return false;
        }

        if ($this->payment_expired_at === null || $this->payment_expired_at->isFuture()) {
            return false;
        }

        $this->status = 'expired';
        $this->payment_status = 'expired';
        $this->cancelled_at = now();
        $this->restoreStock();
        $this->save();

        return true;
    }
}
