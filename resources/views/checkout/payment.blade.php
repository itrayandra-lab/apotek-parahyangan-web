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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
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

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Alamat</p>
                        <p>
                            {{ $order->shipping_address }},
                            {{ $order->shipping_village ? $order->shipping_village.', ' : '' }}
                            {{ $order->shipping_district ? $order->shipping_district.', ' : '' }}
                            {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-widest mb-1">Kontak</p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->user?->email }}</p>
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    Snap by Midtrans akan muncul sebagai popup. Jangan tutup halaman ini sampai pembayaran selesai.
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
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function () {
                        window.location.href = '{{ route('checkout.confirmation', ['order' => $order->id]) }}';
                    },
                    onPending: function () {
                        window.location.href = '{{ route('checkout.pending', ['order' => $order->id]) }}';
                    },
                    onError: function () {
                        window.location.href = '{{ route('checkout.error') }}';
                    },
                    onClose: function () {
                        window.showToast?.('Pembayaran belum selesai. Kamu bisa coba lagi dari riwayat pesanan.', 'error');
                    }
                });
            });
        </script>
    @endif
@endsection
