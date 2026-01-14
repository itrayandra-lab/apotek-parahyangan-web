<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderFailedMail;
use App\Mail\OrderPaidMail;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentWebhookController extends Controller
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    public function midtrans(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->gateway->verifyNotification($payload)) {
            Log::warning('Midtrans notification rejected: invalid signature', ['payload' => $payload]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $data = $this->gateway->parseNotification($payload);
        $order = Order::where('order_number', $data['order_id'] ?? null)->first();

        if (! $order) {
            Log::warning('Midtrans notification order not found', ['order_id' => $data['order_id'] ?? null]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        $grossAmount = $data['gross_amount'] ?? null;

        // Issue 2 Fix: Verify payment amount matches order total before marking as paid
        if (in_array($transactionStatus, ['capture', 'settlement'], true) && $grossAmount !== null) {
            if ((int) $grossAmount !== (int) $order->total) {
                Log::warning('Midtrans notification rejected: amount mismatch', [
                    'order_id' => $order->order_number,
                    'expected_amount' => $order->total,
                    'received_amount' => $grossAmount,
                ]);

                return response()->json(['message' => 'Amount mismatch'], 400);
            }
        }

        // Idempotency check: skip if already processed with same transaction_id
        $existingCallback = $order->payment_callback_data;
        if ($existingCallback !== null && ($existingCallback['transaction_id'] ?? null) === $transactionId) {
            $existingStatus = $existingCallback['transaction_status'] ?? null;
            if ($existingStatus === $transactionStatus) {
                Log::info('Midtrans notification skipped: duplicate', [
                    'order_id' => $order->order_number,
                    'transaction_id' => $transactionId,
                ]);

                return response()->json(['message' => 'OK']);
            }
        }

        // Track previous status for stock restore logic
        $previousStatus = $order->status;
        $shouldRestoreStock = false;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($fraudStatus === 'challenge') {
                $order->payment_status = 'unpaid';
                $order->status = 'pending_payment';
            } else {
                $order->payment_status = 'paid';
                $order->status = $order->status === 'pending_payment' ? 'confirmed' : $order->status;
                $order->paid_at = now();
            }
        } elseif ($transactionStatus === 'pending') {
            $order->payment_status = 'unpaid';
            $order->status = 'pending_payment';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $order->payment_status = 'failed';
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            // Only restore stock if transitioning from pending_payment (not already cancelled/expired)
            $shouldRestoreStock = in_array($previousStatus, ['pending_payment', 'confirmed'], true);
        } elseif ($transactionStatus === 'expire') {
            $order->payment_status = 'expired';
            $order->status = 'expired';
            $order->cancelled_at = now();
            // Only restore stock if transitioning from pending_payment
            $shouldRestoreStock = $previousStatus === 'pending_payment';
        }

        $order->payment_type = $data['payment_type'] ?? $order->payment_type;
        $order->payment_callback_data = $payload;
        $order->save();

        // Restore stock only if needed (prevents double restore)
        if ($shouldRestoreStock) {
            $order->restoreStock();
        }

        // Send email notifications based on payment status
        $this->sendEmailNotifications($order, $transactionStatus);

        Log::info('Midtrans notification processed', [
            'order_id' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);

        return response()->json(['message' => 'OK']);
    }

    private function sendEmailNotifications(Order $order, ?string $transactionStatus): void
    {
        try {
            $order->load('items', 'user');

            // Payment success - send to customer and admin
            if (in_array($transactionStatus, ['capture', 'settlement'], true) && $order->payment_status === 'paid') {
                // Email to customer
                if ($order->user?->email) {
                    Mail::to($order->user->email)->send(new OrderPaidMail($order));
                }

                // Email to admin
                $adminEmail = SiteSetting::getValue('contact.support_email');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new NewOrderNotificationMail($order));
                }
            }

            // Payment failed/expired - send to customer
            if (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) && $order->user?->email) {
                $reason = $transactionStatus === 'expire' ? 'expired' : 'failed';
                Mail::to($order->user->email)->send(new OrderFailedMail($order, $reason));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send order email notification', [
                'order_id' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
