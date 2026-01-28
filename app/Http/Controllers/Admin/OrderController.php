<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = \App\Models\Order::with(['user'])->latest();

        if ($request->ajax()) {
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('customer', function ($order) {
                    return '<div>
                                <div class="text-sm font-bold text-gray-900 mb-0.5">' . ($order->user?->name ?? 'Guest') . '</div>
                                <div class="text-xs text-gray-500">' . ($order->user?->email ?? '-') . '</div>
                            </div>';
                })
                ->editColumn('total', function ($order) {
                    return '<span class="text-sm font-bold text-gray-900">
                                Rp ' . number_format($order->total, 0, ',', '.') . '
                            </span>';
                })
                ->addColumn('status_label', function ($order) {
                    return '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                ' . ucwords(str_replace('_', ' ', $order->status)) . '
                            </span>';
                })
                ->addColumn('payment_label', function ($order) {
                    if ($order->payment_status === 'paid') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Paid
                                </span>';
                    } elseif ($order->payment_status === 'unpaid') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Unpaid
                                </span>';
                    } elseif ($order->payment_status === 'expired') {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                    Expired
                                </span>';
                    } else {
                        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-100">
                                    ' . ucfirst($order->payment_status) . '
                                </span>';
                    }
                })
                ->addColumn('actions', function ($order) {
                    return '<a href="' . route('admin.orders.show', $order) . '" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-rose-50: hover:border-rose-200 hover:bg-rose-50 transition-all shadow-sm"
                               title="View Details">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>';
                })
                ->rawColumns(['customer', 'total', 'status_label', 'payment_label', 'actions'])
                ->make(true);
        }

        return view('admin.orders.index', [
            'orders' => null, // We'll load via AJAX, but keeping the view structure
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'payment_status' => $request->string('payment_status')->toString(),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.medicine', 'user', 'prescription']);

        return view('admin.orders.show', ['order' => $order]);
    }

    public function markPaid(Order $order): RedirectResponse
    {
        $order->markPaid();
        $order->payment_status = 'paid';
        $order->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order ditandai sudah dibayar.');
    }

    public function paymentCallback(Order $order): JsonResponse
    {
        if (! $order->payment_callback_data) {
            return response()->json(['message' => 'No callback data available'], 404);
        }

        return response()->json($order->payment_callback_data, 200, [], JSON_PRETTY_PRINT);
    }

    public function updateAwb(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'shipping_awb' => ['required', 'string', 'max:100'],
        ]);

        $order->update([
            'shipping_awb' => $request->string('shipping_awb'),
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Nomor resi berhasil disimpan.');
    }

    public function rejectItem(\App\Models\OrderItem $item): RedirectResponse
    {
        if ($item->status === 'cancelled') {
            return redirect()->back()->with('error', 'Item sudah dibatalkan.');
        }

        $order = $item->order;
        
        DB::transaction(function () use ($item, $order) {
            $item->update(['status' => 'cancelled']);
            
            // Restore Stock
            if ($item->product_id) {
                $item->product?->increment('stock', $item->quantity);
            } elseif ($item->medicine_id) {
                // Determine medicine stock increment logic - total_stock_unit is standard
                $item->medicine?->increment('total_stock_unit', $item->quantity);
            }

            if (!$order->isPaid()) {
                // If not paid, recalculate total from active items
                $order->recalculateTotal();
                $note = "Item '{$item->product_name}' ditolak oleh admin. Tagihan diperbarui.";
            } else {
                // If already paid, increment refund amount for offline refund
                $order->increment('refund_amount', $item->subtotal);
                $note = "Item '{$item->product_name}' ditolak oleh admin. Dana pengembalian (saat pengambilan): Rp " . number_format($item->subtotal, 0, ',', '.');
            }
            
            // Add activity or note
            $order->update([
                'notes' => ($order->notes ? $order->notes . "\n" : "") . $note
            ]);
        });

        return redirect()->route('admin.orders.show', $order)->with('success', "Item '{$item->product_name}' berhasil dibatalkan.");
    }

    public function syncTotal(Order $order): RedirectResponse
    {
        if ($order->isPaid()) {
            return redirect()->back()->with('error', 'Pesanan sudah dibayar, total tidak dapat diubah.');
        }

        $order->recalculateTotal();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Total tagihan berhasil diperbarui.');
    }

    public function updatePrescriptionStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,verified,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if (!$order->prescription) {
            return redirect()->back()->with('error', 'Pesanan ini tidak memiliki resep.');
        }

        $order->prescription->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status resep berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending_payment,confirmed,processing,shipped,delivered,cancelled,expired',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // Logic for specific status changes
        if ($request->status === 'shipped') {
             // Optional: add timestamp or activity
             $order->update(['shipped_at' => now()]);
        } elseif ($request->status === 'delivered') {
             $order->update(['delivered_at' => now()]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
