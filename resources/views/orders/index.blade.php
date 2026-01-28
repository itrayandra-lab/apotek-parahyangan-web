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
                    @foreach($orders as $item)
                        @if($item instanceof \App\Models\Order)
                            @php
                                $order = $item;
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
                                    'confirmed' => 'Sudah Bayar',
                                    'expired' => 'Kadaluarsa',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                                <!-- Card Header -->
                                <div class="px-6 py-4 border-b border-gray-50 flex flex-wrap items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1 px-2 rounded bg-rose-50 border border-rose-100">
                                            <span class="text-[10px] font-bold text-rose-600 uppercase tracking-tighter">Belanja</span>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($order->prescription)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $order->prescription->status === 'verified' ? 'bg-emerald-50 text-emerald-600' : ($order->prescription->status === 'rejected' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                                            Resep: {{ $order->prescription->status }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400 font-mono">{{ $order->order_number }}</span>
                                </div>

                                <!-- Card Body -->
                                <div class="p-6 flex flex-col md:flex-row gap-6">
                                    <div class="flex-1 flex gap-4">
                                        <!-- Product Image -->
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
                                                @if($firstItem && $firstItem->medicine_id && $firstItem->medicine?->classification === 'Obat Keras')
                                                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500 ml-1" title="Mengandung Obat Keras"></span>
                                                @endif
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
                                            @if($order->status === 'cancelled' || $order->status === 'expired')
                                                <button disabled class="flex-1 md:flex-none px-8 py-2.5 bg-gray-100 text-gray-400 text-xs font-bold rounded-xl cursor-not-allowed text-center">
                                                    Bayar Sekarang
                                                </button>
                                            @else
                                                <a href="{{ route('checkout.payment', $order) }}" class="flex-1 md:flex-none px-8 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-all shadow-sm hover:shadow-md text-center">
                                                    Bayar Sekarang
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('products.index') }}" class="flex-1 md:flex-none px-8 py-2.5 bg-emerald-500 text-white text-xs font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md text-center">
                                                Beli Lagi
                                            </a>
                                        @endif
                                        
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" @click.away="open = false" class="p-2.5 text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" x-cloak class="absolute right-0 bottom-full mb-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden py-2">
                                                @can('cancel', $order)
                                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                        @csrf
                                                        <button type="submit" class="w-full text-left block px-6 py-3 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                                            Batalkan Pesanan
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" disabled class="w-full text-left block px-6 py-3 text-xs font-bold text-gray-300 cursor-not-allowed">
                                                        Batalkan Pesanan
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($item instanceof \App\Models\Prescription)
                            @php
                                $prescription = $item;
                                $prescriptionOrder = $prescription->order;
                                $firstPrescriptionItem = $prescriptionOrder ? $prescriptionOrder->items->first() : null;
                                
                                $statusColor = match($prescription->status) {
                                    'verified' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-rose-100 text-rose-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $statusLabel = match($prescription->status) {
                                    'verified' => 'Terverifikasi',
                                    'rejected' => 'Ditolak',
                                    'pending' => 'Menunggu Verifikasi',
                                    default => ucfirst($prescription->status)
                                };
                            @endphp
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                                <!-- Card Header -->
                                <div class="px-6 py-4 border-b border-gray-50 flex flex-wrap items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1 px-2 rounded bg-emerald-50 border border-emerald-100">
                                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tighter">Resep</span>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $prescription->created_at->format('d M Y') }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                    @if($prescriptionOrder)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $prescriptionOrder->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $prescriptionOrder->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400 font-mono">RESEP#{{ $prescription->id }}</span>
                                </div>

                                <!-- Card Body -->
                                <div class="p-6 flex flex-col md:flex-row gap-6">
                                    <div class="flex-1 flex gap-4">
                                        <!-- Prescription Thumbnail -->
                                        <div class="w-20 h-20 rounded-xl bg-gray-50 flex-shrink-0 border border-gray-100 overflow-hidden">
                                            <img src="{{ $prescription->image_url }}" alt="Resep" class="w-full h-full object-cover">
                                        </div>
                                        
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold text-gray-900 truncate mb-1">
                                                @if($prescriptionOrder && $firstPrescriptionItem)
                                                    {{ $firstPrescriptionItem->product_name ?? ($firstPrescriptionItem->medicine?->name ?? 'Obat Resep') }}
                                                @else
                                                    Unggah Resep Digital
                                                @endif
                                            </h3>
                                            <p class="text-xs text-gray-500">
                                                @if($prescription->user_notes)
                                                    {{ Str::limit($prescription->user_notes, 50) }}
                                                @else
                                                    Pesanan berdasarkan resep dokter
                                                @endif
                                            </p>
                                            @if($prescriptionOrder && $prescriptionOrder->items->count() > 1)
                                                <p class="text-[10px] text-gray-400 mt-2">+{{ $prescriptionOrder->items->count() - 1 }} produk lainnya</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="md:w-px md:h-12 bg-gray-100 self-center hidden md:block"></div>

                                    <div class="flex flex-row md:flex-col justify-between md:justify-center items-center md:items-start md:px-6 gap-8">
                                        <p class="text-xs text-gray-500 md:mb-1">Total Tagihan</p>
                                        <p class="text-sm font-bold text-gray-900">
                                            @if($prescriptionOrder)
                                                Rp {{ number_format($prescriptionOrder->total_price, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="px-6 py-4 bg-gray-50 md:bg-white flex flex-col md:flex-row items-center justify-end gap-4">
                                    <a href="{{ route('prescriptions.show', $prescription) }}" class="text-xs font-bold text-primary hover:text-primary-dark transition-colors order-2 md:order-1">
                                        Lihat Detail Resep
                                    </a>
                                    
                                    <div class="flex items-center gap-2 w-full md:w-auto order-1 md:order-2">
                                        @if($prescriptionOrder && $prescriptionOrder->payment_status === 'unpaid')
                                            <a href="{{ route('prescriptions.payment', $prescriptionOrder) }}" class="flex-1 md:flex-none px-8 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-dark transition-all shadow-sm hover:shadow-md text-center">
                                                Bayar Sekarang
                                            </a>
                                        @elseif($prescription->status === 'verified')
                                            <span class="px-8 py-2.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-xl border border-emerald-100 cursor-default">
                                                Terverifikasi
                                            </span>
                                        @elseif($prescription->status === 'pending')
                                            <span class="px-8 py-2.5 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-xl border border-amber-100 cursor-default animate-pulse">
                                                Memproses Resep...
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
