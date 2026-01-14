<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;

class VoucherService
{
    /**
     * Validate voucher (without lock - for preview/UI).
     *
     * @return array{valid: bool, message: string, voucher?: Voucher, discount?: int}
     */
    public function validate(string $code, User $user, int $subtotal, int $shippingCost = 0): array
    {
        return $this->doValidate($code, $user, $subtotal, $shippingCost, lock: false);
    }

    /**
     * Validate voucher with pessimistic lock (use inside DB::transaction).
     *
     * @return array{valid: bool, message: string, voucher?: Voucher, discount?: int}
     */
    public function validateWithLock(string $code, User $user, int $subtotal, int $shippingCost = 0): array
    {
        return $this->doValidate($code, $user, $subtotal, $shippingCost, lock: true);
    }

    /**
     * @return array{valid: bool, message: string, voucher?: Voucher, discount?: int}
     */
    private function doValidate(string $code, User $user, int $subtotal, int $shippingCost, bool $lock): array
    {
        $query = Voucher::query()->where('code', strtoupper($code));

        $voucher = $lock ? $query->lockForUpdate()->first() : $query->first();

        if (! $voucher) {
            return ['valid' => false, 'message' => 'Kode voucher tidak ditemukan.'];
        }

        if (! $voucher->isValid()) {
            return ['valid' => false, 'message' => 'Voucher sudah tidak berlaku.'];
        }

        if ($voucher->isUsageLimitReached()) {
            return ['valid' => false, 'message' => 'Voucher sudah mencapai batas penggunaan.'];
        }

        if ($voucher->hasUserReachedLimit($user)) {
            return ['valid' => false, 'message' => 'Kamu sudah menggunakan voucher ini.'];
        }

        if (! $voucher->meetsMinPurchase($subtotal)) {
            $minFormatted = 'Rp '.number_format($voucher->min_purchase, 0, ',', '.');

            return ['valid' => false, 'message' => "Minimum pembelian {$minFormatted} untuk voucher ini."];
        }

        $discount = $voucher->calculateDiscount($subtotal, $shippingCost);

        return [
            'valid' => true,
            'message' => 'Voucher berhasil diterapkan.',
            'voucher' => $voucher,
            'discount' => $discount,
        ];
    }

    public function apply(Voucher $voucher, Order $order, User $user, int $discountAmount): VoucherUsage
    {
        $voucher->incrementUsage();

        return VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);
    }
}
