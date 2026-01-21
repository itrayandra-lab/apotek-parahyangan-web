<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate unique invoice number with format PRX-YYYYMMDD-XXXX-N
     */
    public function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $randomCode = strtoupper(Str::random(4));
        
        // Get sequence number for today
        $todayOrdersCount = Order::whereDate('created_at', now()->toDateString())
            ->whereNotNull('invoice_number')
            ->count();
        
        $sequence = $todayOrdersCount + 1;
        
        $invoiceNumber = "PRX-{$date}-{$randomCode}-{$sequence}";
        
        // Ensure uniqueness (very rare collision case)
        while (Order::where('invoice_number', $invoiceNumber)->exists()) {
            $randomCode = strtoupper(Str::random(4));
            $invoiceNumber = "PRX-{$date}-{$randomCode}-{$sequence}";
        }
        
        return $invoiceNumber;
    }
    
    /**
     * Generate invoice data for order
     */
    public function generateInvoiceData(Order $order): array
    {
        return [
            'invoice_number' => $order->invoice_number,
            'order_id' => $order->id,
            'customer_name' => $order->user->name,
            'customer_phone' => $order->phone,
            'order_date' => $order->created_at->format('d/m/Y H:i'),
            'payment_method' => $order->payment_gateway === 'manual' ? 'Bayar di Apotek' : 'Online Payment',
            'payment_status' => $this->getPaymentStatusLabel($order->payment_status),
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->product_price,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'subtotal' => $order->subtotal,
            'voucher_discount' => $order->voucher_discount,
            'shipping_cost' => $order->shipping_cost,
            'total' => $order->total,
            'pickup_address' => 'Apotek Parahyangan PVJ, Paris Van Java Mall, Bandung',
        ];
    }
    
    private function getPaymentStatusLabel(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Belum Dibayar',
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }
}