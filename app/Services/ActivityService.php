<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Activity;
use App\Models\Prescription;

class ActivityService
{
    /**
     * Add order activity to user's feed
     */
    public function addOrderActivity(User $user, Order $order, string $action = 'created'): void
    {
        $description = $this->getOrderActivityDescription($order, $action);
        
        Activity::create([
            'user_id' => $user->id,
            'type' => 'order',
            'title' => $this->getOrderActivityTitle($action),
            'description' => $description,
            'reference_id' => $order->id,
            'reference_type' => Order::class,
            'metadata' => [
                'order_number' => $order->invoice_number,
                'total' => $order->total,
                'payment_method' => $order->payment_gateway === 'manual' ? 'counter' : 'online',
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }
    
    /**
     * Update order activity when status changes
     */

    public function updateOrderActivity($order, string $newStatus): void
    {
        $referenceType = $order instanceof \App\Models\PrescriptionOrder ? \App\Models\PrescriptionOrder::class : Order::class;
        
        $activity = Activity::where('reference_id', $order->id)
            ->where('reference_type', $referenceType)
            ->where('type', 'order')
            ->latest()
            ->first();
            
        if ($activity) {
            $metadata = $activity->metadata ?? [];
            $metadata['status'] = $newStatus;
            $metadata['payment_status'] = $order->payment_status;
            
            $activity->update([
                'description' => $this->getOrderActivityDescription($order, 'updated'),
                'metadata' => $metadata,
            ]);
        }
    }
    
    /**
     * Add prescription activity to user's feed
     */
    public function addPrescriptionActivity(User $user, Prescription $prescription, string $action = 'uploaded'): void
    {
        $description = $this->getPrescriptionActivityDescription($prescription, $action);
        
        Activity::create([
            'user_id' => $user->id,
            'type' => 'prescription',
            'title' => $this->getPrescriptionActivityTitle($action),
            'description' => $description,
            'reference_id' => $prescription->id,
            'reference_type' => Prescription::class,
            'metadata' => [
                'prescription_number' => $prescription->prescription_number,
                'status' => $prescription->status,
                'verification_status' => $prescription->verification_status,
            ],
        ]);
    }
    
    /**
     * Get recent activities for user
     */
    public function getRecentActivities(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Activity::where('user_id', $user->id)
            ->with(['reference'])
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    private function getOrderActivityTitle(string $action): string
    {
        return match ($action) {
            'created' => 'Pesanan Dibuat',
            'updated' => 'Status Pesanan Diperbarui',
            'paid' => 'Pembayaran Berhasil',
            'cancelled' => 'Pesanan Dibatalkan',
            'expired' => 'Pembayaran Kadaluarsa',
            default => 'Aktivitas Pesanan',
        };
    }
    
    private function getOrderActivityDescription(Order $order, string $action): string
    {
        $statusLabel = $this->getStatusLabel($order->status);
        $paymentLabel = $this->getPaymentStatusLabel($order->payment_status);
        
        return match ($action) {
            'created' => "Pesanan {$order->order_number} telah dibuat dengan total Rp " . number_format($order->total, 0, ',', '.'),
            'updated' => "Pesanan {$order->order_number} - Status: {$statusLabel}, Pembayaran: {$paymentLabel}",
            'paid' => "Pembayaran untuk pesanan {$order->order_number} telah berhasil",
            'cancelled' => "Pesanan {$order->order_number} telah dibatalkan",
            'expired' => "Pembayaran untuk pesanan {$order->order_number} telah kadaluarsa",
            default => "Aktivitas pada pesanan {$order->order_number}",
        };
    }
    
    private function getPrescriptionActivityTitle(string $action): string
    {
        return match ($action) {
            'uploaded' => 'Resep Diunggah',
            'verified' => 'Resep Diverifikasi',
            'rejected' => 'Resep Ditolak',
            default => 'Aktivitas Resep',
        };
    }
    
    private function getPrescriptionActivityDescription(Prescription $prescription, string $action): string
    {
        return match ($action) {
            'uploaded' => "Resep {$prescription->prescription_number} telah diunggah dan menunggu verifikasi",
            'verified' => "Resep {$prescription->prescription_number} telah diverifikasi oleh apoteker",
            'rejected' => "Resep {$prescription->prescription_number} ditolak: {$prescription->rejection_reason}",
            default => "Aktivitas pada resep {$prescription->prescription_number}",
        };
    }
    
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_for_pickup' => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
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