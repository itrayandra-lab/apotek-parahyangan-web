<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_purchase' => 'integer',
            'max_discount' => 'integer',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'usage_limit_per_user' => 'integer',
            'is_active' => 'boolean',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Voucher $voucher): void {
            $voucher->code = strtoupper($voucher->code);
        });

        static::updating(function (Voucher $voucher): void {
            $voucher->code = strtoupper($voucher->code);
        });
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
            });
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function hasUsageLimit(): bool
    {
        return $this->usage_limit !== null;
    }

    public function isUsageLimitReached(): bool
    {
        if (! $this->hasUsageLimit()) {
            return false;
        }

        return $this->usage_count >= $this->usage_limit;
    }

    public function hasUserReachedLimit(User $user): bool
    {
        $userUsageCount = $this->usages()->where('user_id', $user->id)->count();

        return $userUsageCount >= $this->usage_limit_per_user;
    }

    public function meetsMinPurchase(int $subtotal): bool
    {
        return $subtotal >= $this->min_purchase;
    }

    public function calculateDiscount(int $subtotal, int $shippingCost = 0): int
    {
        $discount = match ($this->type) {
            'percentage' => $this->calculatePercentageDiscount($subtotal),
            'fixed' => $this->calculateFixedDiscount($subtotal),
            'free_shipping' => $shippingCost,
            default => 0,
        };

        // Ensure discount never exceeds subtotal (prevent negative totals)
        // For free_shipping, discount is applied to shipping, not subtotal
        if ($this->type !== 'free_shipping') {
            $discount = min($discount, $subtotal);
        }

        return max(0, $discount);
    }

    private function calculatePercentageDiscount(int $subtotal): int
    {
        $discount = (int) floor($subtotal * ($this->value / 100));

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $subtotal);
    }

    private function calculateFixedDiscount(int $subtotal): int
    {
        return min($this->value, $subtotal);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'percentage' => 'Persentase',
            'fixed' => 'Nominal',
            'free_shipping' => 'Gratis Ongkir',
            default => $this->type,
        };
    }

    public function getValueFormatted(): string
    {
        return match ($this->type) {
            'percentage' => $this->value.'%',
            'fixed' => 'Rp '.number_format($this->value, 0, ',', '.'),
            'free_shipping' => 'Gratis Ongkir',
            default => (string) $this->value,
        };
    }
}
