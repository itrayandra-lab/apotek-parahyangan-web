<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PrescriptionOrder;
use App\Models\PrescriptionOrderItem;
use App\Models\Product;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * List all prescriptions for admin review
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $prescriptions = Prescription::with(['user', 'verifier', 'order'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.prescriptions.index', compact('prescriptions', 'status'));
    }

    /**
     * Show prescription details for verification
     */
    public function show(Prescription $prescription)
    {
        $prescription->load(['user', 'order.items.product', 'order.items.medicine']);

        $all_products = Product::orderBy('name')->get(['id', 'name', 'price', 'discount_price']);
        $all_medicines = Medicine::orderBy('name')->get(['id', 'name', 'code', 'classification']);

        return view('admin.prescriptions.show', compact('prescription', 'all_products', 'all_medicines'));
    }

    /**
     * Verify prescription and create order
     */
    public function verify(Request $request, Prescription $prescription)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer',
            'items.*.medicine_id' => 'nullable|integer',
            'items.*.custom_name' => 'nullable|string|max:255',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $prescription) {
            // Update prescription status
            $prescription->update([
                'status' => 'verified',
                'admin_notes' => $request->admin_notes,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            // Create order
            $order = PrescriptionOrder::create([
                'user_id' => $prescription->user_id,
                'prescription_id' => $prescription->id,
                'total_price' => 0,
                'payment_status' => 'unpaid',
                'pickup_status' => 'waiting',
            ]);

            // Add items to order
            foreach ($request->items as $item) {
                $price = $item['price'] ?? 0;
                $productId = $item['product_id'] ?? null;
                $medicineId = $item['medicine_id'] ?? null;
                $customName = $item['custom_name'] ?? null;

                if ($productId && !$price) {
                    $product = Product::findOrFail($productId);
                    $price = $product->discount_price ?? $product->price;
                } elseif ($medicineId && !$price) {
                    $medicine = Medicine::findOrFail($medicineId);
                    $price = $medicine->price;
                }

                PrescriptionOrderItem::create([
                    'prescription_order_id' => $order->id,
                    'product_id' => $productId,
                    'medicine_id' => $medicineId,
                    'custom_name' => $customName,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $price,
                ]);
            }

            // Calculate total
            $order->calculateTotal();
        });

        return redirect()
            ->route('admin.prescriptions.show', $prescription)
            ->with('success', 'Resep berhasil diverifikasi dan pesanan telah dibuat.');
    }

    /**
     * Reject prescription
     */
    public function reject(Request $request, Prescription $prescription)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $prescription->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.prescriptions.index')
            ->with('success', 'Resep telah ditolak.');
    }

    /**
     * Search products for order creation (AJAX)
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'price', 'discount_price'])
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => (float)($p->discount_price ?? $p->price),
                    'type' => 'product'
                ];
            });

        $medicines = Medicine::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('classification', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'code', 'classification'])
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'sku' => $m->code,
                    'price' => (float)$m->price, 
                    'type' => 'medicine',
                    'classification' => $m->classification ?? 'General'
                ];
            });

        return response()->json($products->concat($medicines));
    }

    /**
     * Generate WhatsApp link for customer notification
     */
    public function getWhatsAppLink(PrescriptionOrder $order)
    {
        $order->load(['user', 'prescription']);

        $user = $order->user;
        $whatsapp = preg_replace('/[^0-9]/', '', $user->whatsapp);
        
        // Ensure Indonesian format (62xxx)
        if (substr($whatsapp, 0, 1) === '0') {
            $whatsapp = '62' . substr($whatsapp, 1);
        }

        $orderUrl = route('prescriptions.order', $order);
        $total = number_format($order->total_price, 0, ',', '.');

        $message = "Halo {$user->name},\n\n";
        $message .= "Resep Anda sudah diverifikasi dan siap untuk diproses! ✅\n\n";
        $message .= "📋 Detail Pesanan:\n";
        $message .= "Total: Rp {$total}\n\n";
        $message .= "Lihat detail lengkap dan lakukan pembayaran di sini:\n";
        $message .= "{$orderUrl}\n\n";
        $message .= "Setelah pembayaran, Anda dapat mengambil obat di:\n";
        $message .= "📍 Apotek Parahyangan - PVJ Bandung\n\n";
        $message .= "⚠️ PENTING: Bawa resep fisik asli saat pengambilan obat.\n\n";
        $message .= "Terima kasih! 🙏";

        $encodedMessage = urlencode($message);
        $waLink = "https://wa.me/{$whatsapp}?text={$encodedMessage}";

        return response()->json([
            'link' => $waLink,
            'phone' => $whatsapp,
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, PrescriptionOrder $order)
    {
        $request->validate([
            'action' => 'required|in:mark_paid,mark_ready,mark_picked_up',
        ]);

        switch ($request->action) {
            case 'mark_paid':
                $order->markAsPaid();
                $message = 'Pesanan ditandai sebagai sudah dibayar.';
                break;
            case 'mark_ready':
                $order->markAsReady();
                $message = 'Pesanan ditandai siap diambil.';
                break;
            case 'mark_picked_up':
                $order->markAsPickedUp();
                $message = 'Pesanan ditandai sudah diambil.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Verify QR code for pickup
     */
    public function verifyQrCode(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $order = PrescriptionOrder::where('qr_code_token', $request->qr_token)
            ->with(['user', 'items.product', 'prescription'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}
