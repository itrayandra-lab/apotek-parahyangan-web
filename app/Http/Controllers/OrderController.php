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

        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load('items.product');

        return view('orders.show', ['order' => $order]);
    }
}
