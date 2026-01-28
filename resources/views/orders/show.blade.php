@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="pt-28 pb-16 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8 max-w-4xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold tracking-widest text-primary uppercase mb-1">Order</p>
                    <h1 class="text-3xl font-display font-medium text-gray-900">#{{ $order->order_number }}</h1>
                    <p class="text-sm text-gray-500">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($order->payment_status === 'paid') bg-emerald-100 text-emerald-700
                        @elseif($order->payment_status === 'unpaid') bg-yellow-100 text-yellow-700
                        @elseif($order->payment_status === 'expired') bg-gray-100 text-gray-600
                        @else bg-rose-100 text-rose-700
                        @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        @php
                            $statusLabel = match($order->status) {
                                'delivered' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'pending_payment' => 'Menunggu Pembayaran',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'confirmed' => 'Sudah Bayar',
                                'expired' => 'Kadaluarsa',
                                default => ucwords(str_replace('_',' ',$order->status))
                            };
                        @endphp
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Items</h3>
                        <div class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <div class="py-3 flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product_name }}</p>
                                            @if($item->status === 'cancelled')
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest bg-gray-100 text-gray-500">Ditolak</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-sm font-semibold {{ $item->status === 'cancelled' ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran</h3>
                        <dl class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                            <div><dt class="font-semibold text-gray-900">Metode</dt><dd>{{ $order->payment_type ?? '-' }}</dd></div>
                            <div><dt class="font-semibold text-gray-900">Status</dt><dd>{{ ucfirst($order->payment_status) }}</dd></div>
                            <div><dt class="font-semibold text-gray-900">External ID</dt><dd>{{ $order->payment_external_id ?? '-' }}</dd></div>
                        </dl>

                        @if($order->payment_status === 'unpaid')
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                @if($order->status === 'cancelled' || $order->status === 'expired')
                                    <button disabled class="w-full bg-gray-100 text-gray-400 text-center px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase cursor-not-allowed">
                                        Bayar Sekarang
                                    </button>
                                    <p class="text-[10px] text-rose-500 text-center mt-2 font-medium leading-relaxed">
                                        Pesanan dibatalkan/kadaluarsa. Silakan hubungi admin jika ingin mengaktifkan kembali transaksi ini.
                                    </p>
                                @else
                                    <a href="{{ route('checkout.payment', ['order' => $order->id]) }}"
                                       class="block w-full bg-primary text-white text-center px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary-dark transition-all duration-300">
                                        Bayar Sekarang
                                    </a>
                                    <p class="text-xs text-gray-500 text-center mt-2">
                                        Klik untuk memilih metode pembayaran (Online atau Apotek)
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($order->prescription)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Resep Dokter</h3>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $order->prescription->status === 'verified' ? 'bg-emerald-50 text-emerald-600' : ($order->prescription->status === 'rejected' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                                    {{ $order->prescription->status }}
                                </span>
                            </div>
                            
                            @if($order->prescription->status === 'rejected')
                                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-100">
                                    <p class="text-xs text-rose-700 font-bold mb-1">Resep Ditolak:</p>
                                    <p class="text-xs text-rose-600">{{ $order->prescription->admin_notes ?? 'Resep tidak valid atau tidak terbaca.' }}</p>
                                </div>
                            @endif

                            <div class="aspect-video rounded-xl bg-gray-50 border border-gray-100 overflow-hidden relative group">
                                <img src="{{ asset('storage/' . $order->prescription->image_path) }}" alt="Resep" class="w-full h-full object-cover">
                                <a href="{{ asset('storage/' . $order->prescription->image_path) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold uppercase tracking-widest">
                                    Lihat Full
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Ringkasan</h3>
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between"><span>Subtotal</span><span class="font-semibold text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                            @if($order->voucher_discount > 0)
                                <div class="flex justify-between"><span>Diskon Voucher</span><span class="font-semibold text-emerald-600">-Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</span></div>
                                @if($order->voucher_code)
                                    <div class="text-xs text-gray-500">Kode: {{ $order->voucher_code }}</div>
                                @endif
                            @endif
                            <div class="flex justify-between border-t pt-2"><span>Total Pesanan</span><span class="font-semibold text-gray-900">Rp {{ number_format($order->subtotal + $order->shipping_cost, 0, ',', '.') }}</span></div>
                            
                            @if($order->refund_amount > 0)
                                <div class="flex justify-between py-2 px-3 bg-rose-50 rounded-xl mt-2">
                                    <span class="text-xs text-rose-700">Dana Dikembalikan (Refund)</span>
                                    <span class="text-sm font-bold text-rose-600">Rp {{ number_format($order->refund_amount, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">*Refund dilakukan secara manual di apotek saat pengambilan barang karena pembatalan obat keras.</p>
                            @endif

                            <div class="flex justify-between items-center text-lg font-bold text-gray-900 pt-2">
                                <span>Total Bayar</span>
                                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Pengiriman</h3>
                        <p class="text-sm text-gray-700">{{ $order->shipping_address }}</p>
                        <p class="text-sm text-gray-700">
                            {{ $order->shipping_village ? $order->shipping_village.', ' : '' }}
                            {{ $order->shipping_district ? $order->shipping_district.', ' : '' }}
                            {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                        </p>
                        <p class="text-sm text-gray-700 mt-2">Telepon: {{ $order->phone }}</p>
                        @if($order->notes)
                            <p class="text-sm text-gray-500 mt-2">Catatan: {{ $order->notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
