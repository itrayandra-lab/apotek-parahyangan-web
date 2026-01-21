@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-rose-50 to-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Resep Saya</h1>
                    <p class="text-gray-600">Kelola dan pantau status resep Anda</p>
                </div>
                <a 
                    href="{{ route('prescriptions.create') }}" 
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl inline-flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload Resep Baru
                </a>
            </div>

            @if(session('success'))
                <div class="glass-panel p-4 mb-6 bg-green-50 border-l-4 border-green-500">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="glass-panel p-4 mb-6 bg-red-50 border-l-4 border-red-500">
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Prescriptions List -->
            @if($prescriptions->count() > 0)
                <div class="space-y-4">
                    @foreach($prescriptions as $prescription)
                        <div class="glass-panel p-6 hover:shadow-xl transition-all">
                            <div class="flex flex-col lg:flex-row gap-6">
                                <!-- Prescription Image Thumbnail -->
                                <div class="lg:w-48 flex-shrink-0">
                                    <img 
                                        src="{{ $prescription->image_url }}" 
                                        alt="Prescription" 
                                        class="w-full h-32 lg:h-full object-cover rounded-lg shadow-md"
                                    >
                                </div>

                                <!-- Details -->
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                Resep #{{ $prescription->id }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                Diupload: {{ $prescription->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>

                                        <!-- Status Badge -->
                                        <div>
                                            @if($prescription->status === 'pending')
                                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Menunggu Verifikasi
                                                </span>
                                            @elseif($prescription->status === 'verified')
                                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Ditolak
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- User Notes -->
                                    @if($prescription->user_notes)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-700">
                                                <span class="font-medium">Catatan:</span> 
                                                {{ Str::limit($prescription->user_notes, 100) }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Order Info (if exists) -->
                                    @if($prescription->order)
                                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-sm text-gray-600">Total Pesanan:</p>
                                                    <p class="text-xl font-bold text-rose-600">
                                                        Rp {{ number_format($prescription->order->total_price, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-gray-600">Status Pembayaran:</p>
                                                    @if($prescription->order->payment_status === 'paid')
                                                        <p class="text-sm font-semibold text-green-600">✓ Lunas</p>
                                                    @else
                                                        <p class="text-sm font-semibold text-yellow-600">Belum Dibayar</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-gray-600">Status Pickup:</p>
                                                    @if($prescription->order->pickup_status === 'picked_up')
                                                        <p class="text-sm font-semibold text-green-600">✓ Sudah Diambil</p>
                                                    @elseif($prescription->order->pickup_status === 'ready')
                                                        <p class="text-sm font-semibold text-blue-600">Siap Diambil</p>
                                                    @else
                                                        <p class="text-sm font-semibold text-gray-600">Menunggu</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Admin Notes -->
                                    @if($prescription->admin_notes)
                                        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
                                            <p class="text-sm text-blue-900">
                                                <span class="font-semibold">Catatan Apoteker:</span> 
                                                {{ $prescription->admin_notes }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex gap-3">
                                        <a 
                                            href="{{ route('prescriptions.show', $prescription) }}" 
                                            class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition-all text-sm font-medium"
                                        >
                                            Lihat Detail
                                        </a>
                                        @if($prescription->order && $prescription->order->payment_status === 'unpaid')
                                            <a 
                                                href="{{ route('prescriptions.payment', $prescription->order) }}" 
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-sm font-medium"
                                            >
                                                Bayar Sekarang
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $prescriptions->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="glass-panel p-12 text-center">
                    <div class="inline-block p-6 bg-gray-100 rounded-full mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Belum Ada Resep</h3>
                    <p class="text-gray-600 mb-6">Anda belum mengunggah resep apapun</p>
                    <a 
                        href="{{ route('prescriptions.create') }}" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Resep Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
