<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionUploadRequest;
use App\Models\Prescription;
use App\Models\PrescriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    public function __construct(
        private \App\Contracts\PaymentGatewayInterface $gateway,
        private \App\Services\ActivityService $activityService
    ) {
        $this->middleware('auth');
    }

    /**
     * Show prescription upload form
     */
    public function create()
    {
        // Check if user has WhatsApp number
        $user = auth()->user();
        if (empty($user->whatsapp)) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Anda harus menambahkan nomor WhatsApp terlebih dahulu untuk menggunakan fitur ini.');
        }

        return view('prescriptions.create');
    }

    /**
     * Store uploaded prescription
     */
    public function store(PrescriptionUploadRequest $request)
    {
        $user = auth()->user();

        // Verify WhatsApp number exists
        if (empty($user->whatsapp)) {
            return back()->with('error', 'Nomor WhatsApp diperlukan untuk menggunakan fitur ini.');
        }

        // Store the image
        $image = $request->file('prescription_image');
        $filename = 'prescription_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('prescriptions', $filename, 'public');

        // Create prescription record
        $prescription = Prescription::create([
            'user_id' => $user->id,
            'image_path' => $path,
            'user_notes' => $request->user_notes,
            'status' => 'pending',
        ]);

        // Record activity
        $this->activityService->addPrescriptionActivity($user, $prescription, 'uploaded');

        return redirect()
            ->route('prescriptions.show', $prescription)
            ->with('success', 'Resep berhasil diunggah. Mohon tunggu verifikasi dari apoteker kami.');
    }

    /**
     * Show prescription details
     */
    public function show(Prescription $prescription)
    {
        // Ensure user can only view their own prescriptions
        if ($prescription->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $prescription->load(['order.items.product']);

        return view('prescriptions.show', compact('prescription'));
    }

    /**
     * List user's prescriptions
     */
    public function index()
    {
        $prescriptions = Prescription::where('user_id', auth()->id())
            ->with('order')
            ->latest()
            ->paginate(10);

        return view('prescriptions.index', compact('prescriptions'));
    }

    /**
     * API endpoint for polling prescription status
     */
    public function status(Prescription $prescription)
    {
        // Ensure user can only check their own prescriptions
        if ($prescription->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'id' => $prescription->id,
            'status' => $prescription->status,
            'has_order' => $prescription->order !== null,
            'order_id' => $prescription->order?->id,
            'updated_at' => $prescription->updated_at->toISOString(),
        ]);
    }

    /**
     * Show order details
     */
    public function showOrder(PrescriptionOrder $prescriptionOrder)
    {
        // Ensure user can only view their own orders
        if ($prescriptionOrder->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $prescriptionOrder->load(['items.product', 'prescription']);

        return view('prescriptions.order', compact('prescriptionOrder'));
    }
    /**
     * Handle prescription order payment
     */
    public function payment(PrescriptionOrder $prescriptionOrder)
    {
        // Ensure user can only pay their own orders
        if ($prescriptionOrder->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($prescriptionOrder->payment_status === 'paid') {
            return redirect()
                ->route('prescriptions.show', $prescriptionOrder->prescription_id)
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        // Generate snap token if not already exists or expired
        if (empty($prescriptionOrder->snap_token)) {
            // CRITICAL: Ensure order_number is NOT NULL for Midtrans
            if (empty($prescriptionOrder->order_number)) {
                $prescriptionOrder->order_number = $prescriptionOrder->generateOrderNumber();
                $prescriptionOrder->save();
            }

            try {
                $payment = $this->gateway->createTransaction($prescriptionOrder);
                
                $prescriptionOrder->update([
                    'snap_token' => $payment['snap_token'] ?? null,
                    'payment_url' => $payment['redirect_url'] ?? null,
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal menghubungkan ke layanan pembayaran. Pastikan konfigurasi Midtrans sudah benar.');
            }
        }

        return view('checkout.payment', [
            'order' => $prescriptionOrder,
            'snapToken' => $prescriptionOrder->snap_token,
            'snapUrl' => config('midtrans.snap_url'),
            'clientKey' => config('midtrans.client_key'),
        ]);
    }
}
