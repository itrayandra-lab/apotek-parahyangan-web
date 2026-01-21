<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\ActivityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleExpiredPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(ActivityService $activityService): void
    {
        Log::info('Starting expired payments cleanup job');

        $expiredOrders = Order::where('payment_status', 'unpaid')
            ->where('status', 'pending_payment')
            ->where('payment_expired_at', '<', now())
            ->with(['user', 'items'])
            ->get();

        Log::info('Found expired orders', ['count' => $expiredOrders->count()]);

        foreach ($expiredOrders as $order) {
            try {
                DB::transaction(function () use ($order, $activityService) {
                    // Update order status
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'expired',
                    ]);

                    // Restore inventory
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            $item->product->increment('stock', $item->quantity);
                        } elseif ($item->medicine_id && method_exists($item->medicine, 'incrementStock')) {
                            $item->medicine->incrementStock($item->quantity);
                        }
                    }

                    // Restore cart items if metadata exists
                    $metadata = json_decode($order->metadata ?? '{}', true);
                    if (isset($metadata['selected_cart_items'])) {
                        $this->restoreCartItems($order, $metadata['selected_cart_items']);
                    }

                    // Update activity
                    $activityService->updateOrderActivity($order, 'expired');

                    Log::info('Processed expired order', [
                        'order_id' => $order->id,
                        'invoice_number' => $order->invoice_number,
                    ]);
                });
            } catch (\Exception $e) {
                Log::error('Failed to process expired order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Completed expired payments cleanup job');
    }

    private function restoreCartItems(Order $order, array $selectedCartItemIds): void
    {
        try {
            // Get or create cart for user
            $cart = Cart::currentCart();
            
            // Restore items to cart
            foreach ($order->items as $orderItem) {
                // Check if item already exists in cart
                $existingCartItem = $cart->items()
                    ->where('product_id', $orderItem->product_id)
                    ->where('medicine_id', $orderItem->medicine_id)
                    ->first();

                if ($existingCartItem) {
                    // Add quantity to existing item
                    $existingCartItem->increment('quantity', $orderItem->quantity);
                } else {
                    // Create new cart item
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $orderItem->product_id,
                        'medicine_id' => $orderItem->medicine_id,
                        'quantity' => $orderItem->quantity,
                    ]);
                }
            }

            Log::info('Restored cart items for expired order', [
                'order_id' => $order->id,
                'cart_id' => $cart->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to restore cart items', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}