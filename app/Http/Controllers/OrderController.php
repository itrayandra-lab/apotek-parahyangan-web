<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        // Fetch standard orders
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'prescription'])
            ->get();

        // Fetch prescriptions that are NOT linked to a standard order (standalone uploads)
        $prescriptions = \App\Models\Prescription::where('user_id', $user->id)
            ->whereDoesntHave('standardOrder')
            ->with(['order.items.product', 'order.items.medicine'])
            ->get();

        // Combined collection
        $combined = $orders->concat($prescriptions)->sortByDesc('created_at');

        // Manual Pagination
        $page = request()->get('page', 1);
        $perPage = 10;
        
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage)->values(), // .values() to reset indices for plastic/blade
            $combined->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('orders.index', ['orders' => $paginatedItems]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'items.medicine', 'prescription']);

        return view('orders.show', ['order' => $order]);
    }

    public function cancel(Order $order): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('cancel', $order);

        if ($order->payment_status !== 'unpaid') {
            return redirect()->back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $order->restoreStock();
        });

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
