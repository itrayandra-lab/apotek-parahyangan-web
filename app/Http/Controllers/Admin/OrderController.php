<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user'])->latest();

        $search = $request->string('search')->trim();
        if ($search->isNotEmpty()) {
            $query->where(function ($q) use ($search): void {
                $q->where('order_number', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('email', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $status = $request->string('status')->trim();
        if ($status->isNotEmpty()) {
            $query->where('status', $status);
        }

        $paymentStatus = $request->string('payment_status')->trim();
        if ($paymentStatus->isNotEmpty()) {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->paginate(15)->appends($request->query());

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => [
                'search' => $search->toString(),
                'status' => $status->toString(),
                'payment_status' => $paymentStatus->toString(),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user']);

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
}
