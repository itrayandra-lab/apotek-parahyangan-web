@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="pt-28 pb-16 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8">
            <div class="mb-8">
                <p class="text-xs font-bold tracking-widest text-primary uppercase mb-2">Orders</p>
                <h1 class="text-4xl font-display font-medium text-gray-900">Riwayat Pesanan</h1>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <p class="text-gray-600">Belum ada pesanan.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 text-primary font-semibold">Mulai Belanja</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest">#{{ $order->order_number }}</p>
                                    <p class="text-lg font-display font-medium text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->payment_status === 'paid') bg-emerald-100 text-emerald-700
                                        @elseif($order->payment_status === 'unpaid') bg-yellow-100 text-yellow-700
                                        @elseif($order->payment_status === 'expired') bg-gray-100 text-gray-600
                                        @else bg-rose-100 text-rose-700
                                        @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ ucwords(str_replace('_',' ',$order->status)) }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
