<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderFailedMail;
use App\Mail\OrderPaidMail;
use App\Models\Order;
use App\Models\Cart;
use App\Models\SiteSetting;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private ActivityService $activityService
    ) {}

    public function midtrans(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->gateway->verifyNotification($payload)) {
            Log::warning('Midtrans notification rejected: invalid signature', ['payload' => $payload]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $data = $this->gateway->parseNotification($payload);
        $orderId = $data['order_id'] ?? null;
        $order = null;

        if (str_starts_with($orderId, 'PRX-')) {
            // For new invoice format, search by invoice_number first, then fallback to order_number
            $order = Order::where('invoice_number', $orderId)->first() 
                ?? \App\Models\PrescriptionOrder::where('order_number', $orderId)->first();
        } else {
            // For old format, search by order_number
            $order = Order::where('order_number', $orderId)->first();
        }

        if (! $order) {
            Log::warning('Midtrans notification order not found', ['order_id' => $orderId]);

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
                if ($order instanceof Order) $order->status = 'pending_payment';
            } else {
                $order->payment_status = 'paid';
                if ($order instanceof Order) {
                    $order->status = $order->status === 'pending_payment' ? 'confirmed' : $order->status;
                }
                $order->paid_at = now();
            }
        } elseif ($transactionStatus === 'pending') {
            $order->payment_status = 'unpaid';
            if ($order instanceof Order) $order->status = 'pending_payment';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $order->payment_status = 'failed';
            if ($order instanceof Order) {
                $order->status = 'cancelled';
                $order->cancelled_at = now();
                $shouldRestoreStock = in_array($previousStatus, ['pending_payment', 'confirmed'], true);
            }
        } elseif ($transactionStatus === 'expire') {
            $order->payment_status = 'expired';
            if ($order instanceof Order) {
                $order->status = 'expired';
                $order->cancelled_at = now();
                $shouldRestoreStock = $previousStatus === 'pending_payment';
            }
        }

        $order->payment_type = $data['payment_type'] ?? $order->payment_type;
        $order->payment_callback_data = $payload;
        $order->save();

        // Record to sales and sale_details if paid
        if ($order->payment_status === 'paid') {
            \App\Models\Sale::recordFromOrder($order);
            
            // Clean up cart items for online payment orders
            if ($order instanceof Order) {
                $this->cleanupCartItems($order);
                
                // Update activity
                $this->activityService->updateOrderActivity($order, 'paid');
            }
        }

        // Restore stock only if needed (prevents double restore)
        if ($shouldRestoreStock) {
            $order->restoreStock();
        }

        // Send email notifications based on payment status
        if ($order instanceof Order) {
            $this->sendEmailNotifications($order, $transactionStatus);
        } else {
            // For PrescriptionOrder, we might want to send different emails or just log
            Log::info('Prescription order payment status updated', ['order_id' => $order->id, 'status' => $order->payment_status]);
        }

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
    
    private function cleanupCartItems(Order $order): void
    {
        try {
            // Get selected cart item IDs from order metadata
            $metadata = $order->metadata ?? [];
            $selectedCartItemIds = $metadata['selected_cart_items'] ?? [];
            
            if (!empty($selectedCartItemIds)) {
                // Find user's cart
                $cart = Cart::where('user_id', $order->user_id)->first();
                
                if ($cart) {
                    // Remove the selected items from cart
                    $cart->items()->whereIn('id', $selectedCartItemIds)->delete();
                    
                    Log::info('Cleaned up cart items after payment', [
                        'order_id' => $order->id,
                        'cart_id' => $cart->id,
                        'removed_items' => $selectedCartItemIds,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup cart items', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
