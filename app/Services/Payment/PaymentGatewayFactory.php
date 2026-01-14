<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    public function make(?string $gateway = null): PaymentGatewayInterface
    {
        $selected = $gateway ?? config('cart.default_gateway', 'midtrans');

        return match ($selected) {
            'midtrans' => app(MidtransService::class),
            default => app(MidtransService::class),
        };
    }
}
