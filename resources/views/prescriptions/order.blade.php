@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-rose-50 to-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <a href="{{ route('prescriptions.show', $prescriptionOrder->prescription_id) }}" class="inline-flex items-center text-rose-600 hover:text-rose-700 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Detail Resep
                </a>
                <h1 class="text-4xl font-bold text-gray-900">Pesanan Obat</h1>
                <p class="text-gray-600 mt-2">ID Pesanan: #{{ $prescriptionOrder->id }}</p>
            </div>

            <div class="space-y-6">
                <!-- Invoice Section -->
                <div class="glass-panel p-8 text-center bg-white">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-100 text-rose-600 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em] mb-2">Nomor Invoice</h2>
                    <p class="text-3xl font-black text-gray-900 font-mono tracking-tighter">{{ $prescriptionOrder->order_number }}</p>
                    <div class="mt-6 flex justify-center gap-2">
                        <span class="px-4 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-widest">Dibuat: {{ $prescriptionOrder->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="mt-8 bg-rose-50 border border-rose-100 rounded-xl p-4">
                        <p class="text-rose-800 text-sm font-medium">Sebutkan Nomor Invoice di atas kepada petugas Apotek Parahyangan Suite (Gedung Soho) saat pengambilan obat.</p>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="glass-panel p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Detail Item</h2>
                    <div class="space-y-4 mb-6">
                        @foreach($prescriptionOrder->items as $item)
                            <div class="flex justify-between items-center p-4 bg-gray-50/50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-gray-100 shadow-sm">
                                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="font-bold text-gray-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span class="text-gray-900">Total Pembayaran</span>
                            <span class="text-rose-600">Rp {{ number_format($prescriptionOrder->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pickup Status & Instructions -->
                <div class="glass-panel p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Status & Lokasi</h2>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Status Pengambilan</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $prescriptionOrder->pickup_status === 'ready' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                @if($prescriptionOrder->pickup_status === 'waiting')
                                    Menunggu Persiapan
                                @elseif($prescriptionOrder->pickup_status === 'ready')
                                    Siap Diambil
                                @else
                                    Sudah Diambil
                                @endif
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Status Pembayaran</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $prescriptionOrder->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $prescriptionOrder->payment_status === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Apotek Parahyangan Suite - Gedung Soho</h4>
                                <p class="text-sm text-gray-600">Gedung Soho, Jl. Karang Tinggal No.5-7</p>
                                <p class="text-sm text-gray-600">Cipedes, Kec. Sukajadi, Kota Bandung 40162</p>
                                <a href="https://maps.app.goo.gl/rM9fXj9YQ9m9zY6V7" target="_blank" class="text-xs font-bold text-rose-500 uppercase mt-2 inline-block hover:underline">Buka Maps →</a>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Penting!</h4>
                                <p class="text-sm text-gray-600">Pastikan Anda membawa <strong>Resep Fisik Asli</strong> saat pengambilan obat. Petugas kami tidak dapat menyerahkan obat tanpa resep asli.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action -->
                @if($prescriptionOrder->payment_status === 'unpaid')
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col items-center">
                        <p class="text-gray-600 text-sm mb-4 text-center">Selesaikan pembayaran secara online untuk mempercepat proses pengambilan.</p>
                        <a href="{{ route('prescriptions.payment', $prescriptionOrder) }}" class="w-full max-w-sm px-8 py-4 bg-gray-900 text-white rounded-xl font-bold uppercase tracking-widest hover:bg-rose-500 transition-all text-center shadow-lg">
                            Bayar Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
