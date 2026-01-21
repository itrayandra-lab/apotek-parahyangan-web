@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-rose-50 to-gray-100 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('prescriptions.index') }}" class="inline-flex items-center text-rose-600 hover:text-rose-700 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Resep
                </a>
                <h1 class="text-4xl font-bold text-gray-900">Detail Resep</h1>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Left Column: Prescription Image -->
                <div class="glass-panel p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Foto Resep</h2>
                    <div class="relative group">
                        <img 
                            src="{{ $prescription->image_url }}" 
                            alt="Prescription Image" 
                            class="w-full rounded-lg shadow-lg cursor-pointer"
                            onclick="openImageModal()"
                        >
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <span class="text-white font-medium">Klik untuk memperbesar</span>
                        </div>
                    </div>

                    @if($prescription->user_notes)
                        <div class="mt-6">
                            <h3 class="font-semibold text-gray-900 mb-2">Catatan Anda:</h3>
                            <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $prescription->user_notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Status & Details -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="glass-panel p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Status Resep</h2>
                        
                        <div class="flex items-center gap-3 mb-4" id="statusBadge">
                            @if($prescription->status === 'pending')
                                <div class="flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full">
                                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span class="font-semibold">Menunggu Verifikasi</span>
                                </div>
                            @elseif($prescription->status === 'verified')
                                <div class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">Terverifikasi</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-semibold">Ditolak</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Upload:</span>
                                <span class="font-medium">{{ $prescription->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if($prescription->verified_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal Verifikasi:</span>
                                    <span class="font-medium">{{ $prescription->verified_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($prescription->admin_notes)
                            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                                <h4 class="font-semibold text-blue-900 mb-1">Catatan Apoteker:</h4>
                                <p class="text-blue-800 text-sm">{{ $prescription->admin_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Order Details (if verified) -->
                    @if($prescription->order)
                        <div class="glass-panel p-6" id="orderDetails">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pesanan</h2>
                            
                            <div class="space-y-3 mb-4">
                                @foreach($prescription->order->items as $item)
                                    <div class="flex justify-between items-start p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                        </div>
                                        <p class="font-semibold text-gray-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-lg font-semibold text-gray-900">Total:</span>
                                    <span class="text-2xl font-bold text-rose-600">
                                        Rp {{ number_format($prescription->order->total_price, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if($prescription->order->payment_status === 'unpaid')
                                    <div class="space-y-3">
                                        <a 
                                            href="{{ route('prescriptions.payment', $prescription->order) }}" 
                                            class="block w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl text-center"
                                        >
                                            Bayar Sekarang
                                        </a>
                                        <p class="text-center text-sm text-gray-600">
                                            atau bayar langsung di kasir saat pengambilan
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <p class="text-green-800 font-medium text-center">✓ Pembayaran Berhasil</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Pickup Instructions -->
                        <div class="glass-panel p-6 border-l-4 border-rose-500">
                            <h3 class="font-semibold text-gray-900 mb-3">Instruksi Pengambilan</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div>
                                        <strong>Apotek Parahyangan - PVJ Bandung</strong><br>
                                        <span class="text-gray-600">Jl. Sukajadi No.137-139, Bandung</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Jam Operasional: Senin - Minggu, 09:00 - 21:00</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <strong class="text-rose-600">WAJIB membawa resep fisik asli</strong>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <span>Tunjukkan <strong>Nomor Invoice</strong> di bawah ini kepada petugas</span>
                                </li>
                            </ul>

                            <!-- Invoice Info -->
                            <div class="mt-6 text-center">
                                <div class="inline-block p-4 bg-gray-50 rounded-lg border border-gray-100 w-full">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Nomor Invoice</p>
                                    <p class="text-xl font-black text-rose-600 font-mono tracking-tighter">{{ $prescription->order->order_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Waiting Message (if pending) -->
                    @if($prescription->status === 'pending')
                        <div class="glass-panel p-6 text-center" id="waitingMessage">
                            <div class="inline-block p-4 bg-yellow-100 rounded-full mb-4">
                                <svg class="w-12 h-12 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Menunggu Verifikasi Apoteker</h3>
                            <p class="text-gray-600 mb-4">
                                Resep Anda sedang dalam proses verifikasi. Anda akan menerima notifikasi WhatsApp setelah resep diverifikasi.
                            </p>
                            <p class="text-sm text-gray-500">Halaman ini akan otomatis diperbarui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
        <div class="relative max-w-6xl max-h-full">
            <button 
                onclick="closeImageModal()" 
                class="absolute -top-12 right-0 text-white hover:text-gray-300"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img 
                src="{{ $prescription->image_url }}" 
                alt="Prescription Full View" 
                class="max-w-full max-h-[90vh] rounded-lg"
            >
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openImageModal() {
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }

    // Polling for status updates (only if pending)
    @if($prescription->status === 'pending')
    let pollingInterval;
    
    function checkStatus() {
        fetch('{{ route('api.prescriptions.status', $prescription) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'pending') {
                    // Status changed, reload page
                    window.location.reload();
                }
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Start polling every 5 seconds
    pollingInterval = setInterval(checkStatus, 5000);

    // Stop polling when page is hidden
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(pollingInterval);
        } else {
            pollingInterval = setInterval(checkStatus, 5000);
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        clearInterval(pollingInterval);
    });
    @endif
</script>
@endsection
