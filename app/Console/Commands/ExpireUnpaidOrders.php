<?php

namespace App\Console\Commands;

use App\Mail\OrderFailedMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpireUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:expire-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark unpaid pending_payment orders as expired and restore stock';

    public function handle(): int
    {
        $expiredCount = 0;

        Order::query()
            ->pendingPayment()
            ->where('payment_status', 'unpaid')
            ->whereNotNull('payment_expired_at')
            ->where('payment_expired_at', '<=', now())
            ->chunk(100, function ($orders) use (&$expiredCount): void {
                foreach ($orders as $order) {
                    if ($order->expireIfDue()) {
                        $expiredCount++;
                        $this->sendExpiredNotification($order);
                    }
                }
            });

        if ($expiredCount > 0) {
            Log::info('Expired unpaid orders', ['count' => $expiredCount]);
        }

        $this->info("Expired {$expiredCount} orders.");

        return self::SUCCESS;
    }

    private function sendExpiredNotification(Order $order): void
    {
        try {
            $order->load('items', 'user');
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new OrderFailedMail($order, 'expired'));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send order expired email', [
                'order_id' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
