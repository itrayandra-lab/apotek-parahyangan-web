@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - Beautylatory')

@section('content')
    <div class="pt-28 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8 max-w-3xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-4">
                <h1 class="text-3xl font-display font-medium text-gray-900">Pesanan Berhasil</h1>
                <p class="text-gray-600">Order {{ $order->order_number }} telah tercatat. Status pembayaran: {{ ucfirst($order->payment_status) }}.</p>
                <div class="border rounded-xl p-4 bg-gray-50">
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Total</span>
                        <span class="font-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Alamat: {{ $order->shipping_address }},
                        {{ $order->shipping_village ? $order->shipping_village.', ' : '' }}
                        {{ $order->shipping_district ? $order->shipping_district.', ' : '' }}
                        {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('customer.dashboard') }}" class="bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">Dashboard</a>
                    <a href="{{ route('products.index') }}" class="bg-white border border-gray-200 text-gray-900 px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-50 transition-all duration-300">Lanjut Belanja</a>
                </div>
            </div>
        </div>
    </div>
@endsection
