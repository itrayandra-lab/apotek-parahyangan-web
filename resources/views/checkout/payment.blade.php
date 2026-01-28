@php
    $isPrescription = $order instanceof \App\Models\PrescriptionOrder;
    $payOfflineRoute = $isPrescription ? 'prescriptions.pay-offline' : 'checkout.pay-offline';
    $confirmationRoute = $isPrescription ? 'prescriptions.confirmation' : 'checkout.confirmation';
    $pendingRoute = $isPrescription ? 'prescriptions.pending' : 'checkout.pending';
@endphp

@extends('layouts.app')

@section('title', 'Pembayaran - Beautylatory')

@section('content')
    <div class="pt-28 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8 max-w-4xl">
            <div class="mb-8 text-center">
                <p class="text-xs font-bold tracking-widest text-primary uppercase mb-2">Pembayaran</p>
                <h1 class="text-4xl font-display font-medium text-gray-900">Selesaikan Pembayaran</h1>
                <p class="text-gray-500 mt-2">Order: {{ $order->order_number }}</p>
            </div>

            <div x-data="{ paymentMethod: 'online' }" class="space-y-6">
                <!-- Payment Method Tabs -->
                <div class="flex border-b border-gray-100">
                    <button @click="paymentMethod = 'online'"
                            :class="paymentMethod === 'online' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                            class="flex-1 pb-4 text-xs font-bold tracking-widest uppercase border-b-2 transition-all">
                        Bayar Online
                    </button>
                    <button @click="paymentMethod = 'pharmacy'"
                            :class="paymentMethod === 'pharmacy' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                            class="flex-1 pb-4 text-xs font-bold tracking-widest uppercase border-b-2 transition-all">
                        Bayar di Apotek
                    </button>
                </div>

                <!-- Online Payment Section -->
                <div x-show="paymentMethod === 'online'" x-transition class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Pembayaran</p>
                            <p class="text-3xl font-display font-medium text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                        @if($snapToken)
                            <button id="pay-button"
                                    class="bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                                Bayar Sekarang
                            </button>
                        @else
                            <span class="text-sm text-rose-600">Token pembayaran tidak tersedia. Silakan ulangi checkout.</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Lokasi Pengambilan</p>
                            <p class="font-medium text-gray-900">Apotek Parahyangan PVJ</p>
                            <p class="text-gray-600">Paris Van Java Mall, Bandung</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Kontak</p>
                            <p class="font-medium text-gray-900">{{ $order->phone }}</p>
                            <p class="text-gray-600">{{ $order->user?->email }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl flex gap-3 text-xs text-amber-800">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Snap by Midtrans akan muncul sebagai popup. Mohon jangan tutup atau refresh halaman ini sampai proses pembayaran selesai.</p>
                    </div>
                </div>

                <!-- Pharmacy Payment Section -->
                <div x-show="paymentMethod === 'pharmacy'" x-transition class="space-y-6">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                        <h3 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Instruksi Pembayaran di Apotek
                        </h3>
                        <ul class="space-y-4 text-sm text-blue-800">
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs mt-0.5">1</span>
                                <div>
                                    <p class="font-medium">Tunjukkan Nomor Invoice kepada petugas kasir:</p>
                                    <p class="text-xl font-mono font-bold text-blue-900 tracking-tighter mt-1">{{ $order->order_number }}</p>
                                </div>
                            </li>
                            @if($order instanceof \App\Models\PrescriptionOrder || $order->prescription_id)
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs mt-0.5">2</span>
                                <p><strong>Wajib membawa resep fisik asli</strong> untuk diverifikasi dan ditukarkan dengan obat.</p>
                            </li>
                            @endif
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center font-bold text-xs mt-0.5">{{ ($order instanceof \App\Models\PrescriptionOrder || $order->prescription_id) ? '3' : '2' }}</span>
                                <p>Lakukan pembayaran di kasir <strong>Apotek Parahyangan - PVJ Bandung</strong> (Paris Van Java Mall, Lantai Dasar/GF).</p>
                            </li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-4">
                        <form action="{{ route($payOfflineRoute, $order) }}" method="POST">
                            @csrf
                            <button type="submit" 
                               class="w-full bg-gray-900 text-white py-4 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300 text-center shadow-lg hover:shadow-xl">
                                Konfirmasi Pembayaran di Apotek
                            </button>
                        </form>
                        <p class="text-center text-xs text-gray-500">
                            Klik tombol di atas setelah Anda memastikan akan membayar langsung di apotek.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if($snapToken)
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                triggerSnap();
            });

            // Trigger snap only on click (auto-trigger removed as per request)

            function triggerSnap() {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function (result) {
                        window.location.href = '{{ route($confirmationRoute, [$isPrescription ? 'prescriptionOrder' : 'order' => $order->id]) }}';
                    },
                    onPending: function (result) {
                        window.location.href = '{{ route($pendingRoute, [$isPrescription ? 'prescriptionOrder' : 'order' => $order->id]) }}';
                    },
                    onError: function (result) {
                        window.location.href = '{{ route('checkout.error') }}';
                    },
                    onClose: function () {
                        // Optional: Show toast or just stay on page
                        window.showToast?.('Pembayaran belum selesai. Silakan klik tombol "Bayar Sekarang" jika ingin mencoba lagi.', 'error');
                    }
                });
            }
        </script>
    @endif
@endsection
