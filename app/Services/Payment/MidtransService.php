<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService implements PaymentGatewayInterface
{
    public function __construct()
    {
        $this->configure();
    }

    private function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.environment') === 'production';
        Config::$isSanitized = (bool) config('midtrans.sanitize', true);
        Config::$is3ds = (bool) config('midtrans.enable_3ds', true);
    }

    public function createTransaction(Order $order): array
    {
        $order->loadMissing(['items', 'user']);

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user?->name,
                'email' => $order->user?->email,
                'phone' => $order->phone,
                'shipping_address' => [
                    'first_name' => $order->user?->name,
                    'phone' => $order->phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $this->mapItems($order),
            'enabled_payments' => config('midtrans.snap.enabled_payments', []),
            'credit_card' => config('midtrans.snap.credit_card', []),
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => config('midtrans.snap.custom_expiry.unit', 'hour'),
                'duration' => config('midtrans.snap.custom_expiry.duration', 24),
            ],
            'callbacks' => [
                'finish' => config('midtrans.finish_url') ?: route('checkout.confirmation', ['order' => $order->id]),
                'unfinish' => config('midtrans.unfinish_url') ?: route('checkout.pending', ['order' => $order->id]),
                'error' => config('midtrans.error_url') ?: route('checkout.error'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return [
                'snap_token' => $snapToken,
                'redirect_url' => null,
                'token' => $snapToken,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans createTransaction failed', [
                'order_number' => $order->order_number,
                'message' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal membuat transaksi pembayaran.');
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapItems(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => $item->product_price,
                'quantity' => $item->quantity,
                'name' => $item->product_name,
            ];
        }

        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Shipping Cost',
            ];
        }

        // Add voucher discount as negative item
        if ($order->voucher_discount > 0) {
            $items[] = [
                'id' => 'VOUCHER',
                'price' => -$order->voucher_discount,
                'quantity' => 1,
                'name' => 'Voucher Discount ('.($order->voucher_code ?? 'VOUCHER').')',
            ];
        }

        return $items;
    }

    public function verifyNotification(array $data): bool
    {
        $serverKey = config('midtrans.server_key');
        $orderId = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return $signatureKey === ($data['signature_key'] ?? '');
    }

    public function parseNotification(array $data): array
    {
        return [
            'order_id' => $data['order_id'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? null,
            'payment_type' => $data['payment_type'] ?? null,
            'fraud_status' => $data['fraud_status'] ?? null,
            'status_code' => $data['status_code'] ?? null,
            'gross_amount' => $data['gross_amount'] ?? null,
            'signature_key' => $data['signature_key'] ?? null,
        ];
    }

    public function getTransactionStatus(string $orderId): ?array
    {
        try {
            $status = Transaction::status($orderId);

            return [
                'order_id' => $status->order_id ?? null,
                'transaction_id' => $status->transaction_id ?? null,
                'transaction_status' => $status->transaction_status ?? null,
                'payment_type' => $status->payment_type ?? null,
                'fraud_status' => $status->fraud_status ?? null,
                'status_code' => $status->status_code ?? null,
                'gross_amount' => $status->gross_amount ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans getTransactionStatus failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
