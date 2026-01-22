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
                <div class="space-y-6">
                    @foreach($orders as $order)
                        @php
                            $firstItem = $order->items->first();
                            $statusColor = match($order->status) {
                                'delivered', 'confirmed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled', 'expired' => 'bg-rose-100 text-rose-700',
                                'pending_payment' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            $statusLabel = match($order->status) {
                                'delivered' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'pending_payment' => 'Menunggu Pembayaran',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'confirmed' => 'Dikonfirmasi',
                                'expired' => 'Kadaluarsa',
                                default => ucfirst($order->status)
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Card Header -->
                            <div class="px-6 py-4 border-b border-gray-50 flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span class="text-xs font-bold text-gray-900">Belanja</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                                <span class="text-xs text-gray-400 font-mono">{{ $order->order_number }}</span>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 flex flex-col md:flex-row gap-6">
                                <div class="flex-1 flex gap-4">
                                    <!-- Product Image Placeholder or Actual -->
                                    <div class="w-20 h-20 rounded-xl bg-gray-50 flex-shrink-0 border border-gray-100 overflow-hidden">
                                        @if($firstItem && $firstItem->product && $firstItem->product->hasMedia('images'))
                                            <img src="{{ $firstItem->product->getFirstMediaUrl('images', 'thumb') }}" alt="{{ $firstItem->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-gray-900 truncate mb-1">
                                            {{ $firstItem ? $firstItem->product_name : 'Order #' . $order->order_number }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ $firstItem ? $firstItem->quantity : 0 }} barang x Rp {{ number_format($firstItem ? $firstItem->product_price : 0, 0, ',', '.') }}
                                        </p>
                                        @if($order->items->count() > 1)
                                            <p class="text-[10px] text-gray-400 mt-2">+{{ $order->items->count() - 1 }} produk lainnya</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="md:w-px md:h-12 bg-gray-100 self-center hidden md:block"></div>

                                <div class="flex flex-row md:flex-col justify-between md:justify-center items-center md:items-start md:px-6 gap-8">
                                    <p class="text-xs text-gray-500 md:mb-1">Total Belanja</p>
                                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="px-6 py-4 bg-gray-50 md:bg-white flex flex-col md:flex-row items-center justify-end gap-4">
                                <a href="{{ route('orders.show', $order) }}" class="text-xs font-bold text-primary hover:text-primary-dark transition-colors order-2 md:order-1">
                                    Lihat Detail Transaksi
                                </a>
                                
                                <div class="flex items-center gap-2 w-full md:w-auto order-1 md:order-2">
                                    @if($order->payment_status === 'unpaid')
                                        <a href="{{ route('checkout.payment', $order) }}" class="flex-1 md:flex-none px-8 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-all shadow-sm hover:shadow-md text-center">
                                            Bayar Sekarang
                                        </a>
                                    @else
                                        <a href="{{ route('products.index') }}" class="flex-1 md:flex-none px-8 py-2.5 bg-emerald-500 text-white text-xs font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md text-center">
                                            Beli Lagi
                                        </a>
                                    @endif
                                    
                                    <button class="p-2.5 text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
