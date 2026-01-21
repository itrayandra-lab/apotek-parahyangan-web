@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="container mx-auto px-6 md:px-8 max-w-6xl">
        <div class="mb-10">
            <p class="text-xs font-bold tracking-[0.2em] text-primary uppercase">Dashboard</p>
            <h1 class="text-4xl md:text-5xl font-display font-medium text-gray-900 mt-2">Halo, {{ $user->name }}.</h1>
            <p class="text-gray-500 mt-2">Kelola pesanan dan profilmu di satu tempat.</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-widest">Akun</p>
                        <h2 class="text-2xl font-display font-medium text-gray-900">Informasi Utama</h2>
                    </div>
                    <a href="{{ route('customer.profile.edit') }}"
                       class="text-xs font-bold tracking-widest text-primary uppercase hover:text-primary-dark">Edit Profil</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Nama</p>
                        <p class="text-gray-900 font-semibold">{{ $user->name }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Email</p>
                        <p class="text-gray-900 font-semibold">{{ $user->email }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Username</p>
                        <p class="text-gray-900 font-semibold">{{ $user->username }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">WhatsApp</p>
                        <p class="text-gray-900 font-semibold">{{ $user->whatsapp }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Role</p>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($user->role) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @php
                    // Regular Orders
                    $regularOrdersCount = $user->orders()->count();
                    $regularUnpaidCount = $user->orders()->where('payment_status', 'unpaid')->count();
                    $regularPaidCount = $user->orders()->where('payment_status', 'paid')->count();

                    // Prescription Orders
                    $prescriptionOrdersCount = $user->prescriptionOrders()->count();
                    $prescriptionUnpaidCount = $user->prescriptionOrders()->where('payment_status', 'unpaid')->count();
                    $prescriptionPaidCount = $user->prescriptionOrders()->where('payment_status', 'paid')->count();

                    // Combined Totals
                    $ordersCount = $regularOrdersCount + $prescriptionOrdersCount;
                    $unpaidCount = $regularUnpaidCount + $prescriptionUnpaidCount;
                    $paidCount = $regularPaidCount + $prescriptionPaidCount;

                    $prescriptionsCount = $user->prescriptions()->count();
                    $prescriptionsPending = $user->prescriptions()->where('status', 'pending')->count();
                    $prescriptionsVerified = $user->prescriptions()->where('status', 'verified')->count();
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Ringkasan Pesanan</p>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-gray-700"><span>Total Pesanan</span><span class="font-semibold text-gray-900">{{ $ordersCount }}</span></div>
                        <div class="flex justify-between text-sm text-gray-700"><span>Belum Dibayar</span><span class="font-semibold text-amber-600">{{ $unpaidCount }}</span></div>
                        <div class="flex justify-between text-sm text-gray-700"><span>Sudah Dibayar</span><span class="font-semibold text-emerald-700">{{ $paidCount }}</span></div>
                    </div>
                    <a href="{{ route('orders.index') }}"
                       class="mt-4 inline-flex items-center justify-center w-full bg-gray-900 text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-colors">
                        Lihat Pesanan
                    </a>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">Aksi Cepat</p>
                    <div class="grid grid-cols-1 gap-2">
                        <a href="{{ route('prescriptions.index') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary-dark transition-colors">Lihat Resep</a>
                        <a href="{{ route('prescriptions.create') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary-dark transition-colors">Upload Resep</a>
                        <a href="{{ route('products.index') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary-dark transition-colors">Belanja Produk</a>
                        <a href="{{ route('addresses.index') }}" class="w-full text-center bg-gray-900 text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-colors">Alamat Saya</a>
                        <a href="{{ route('customer.profile.show') }}" class="w-full text-center bg-gray-100 text-gray-700 px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-200 transition-colors">Profil Saya</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-display font-medium text-gray-900 mb-6">Aktivitas Terbaru</h2>
            
            @if($activities->count() > 0)
                <div class="space-y-4">
                    @foreach($activities as $activity)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-b-0 last:pb-0">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                    @if($activity->color === 'green') bg-green-100
                                    @elseif($activity->color === 'blue') bg-blue-100
                                    @elseif($activity->color === 'yellow') bg-yellow-100
                                    @elseif($activity->color === 'red') bg-red-100
                                    @else bg-gray-100
                                    @endif
                                ">
                                    @if($activity->icon === 'shopping-bag')
                                        <svg class="w-5 h-5 
                                            @if($activity->color === 'green') text-green-600
                                            @elseif($activity->color === 'blue') text-blue-600
                                            @elseif($activity->color === 'yellow') text-yellow-600
                                            @elseif($activity->color === 'red') text-red-600
                                            @else text-gray-600
                                            @endif
                                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    @elseif($activity->icon === 'document-text')
                                        <svg class="w-5 h-5 
                                            @if($activity->color === 'green') text-green-600
                                            @elseif($activity->color === 'blue') text-blue-600
                                            @elseif($activity->color === 'yellow') text-yellow-600
                                            @elseif($activity->color === 'red') text-red-600
                                            @else text-gray-600
                                            @endif
                                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $activity->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $activity->time_ago }}</p>
                                    </div>
                                    @if($activity->type === 'order' && isset($activity->metadata['order_number']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                            @if($activity->metadata['payment_status'] === 'paid')
                                                bg-green-100 text-green-800
                                            @elseif($activity->metadata['payment_status'] === 'unpaid')
                                                bg-yellow-100 text-yellow-800
                                            @elseif($activity->metadata['payment_status'] === 'pending')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-red-100 text-red-800
                                            @endif
                                        ">
                                            @if($activity->metadata['payment_status'] === 'paid')
                                                Lunas
                                            @elseif($activity->metadata['payment_status'] === 'unpaid')
                                                Belum Bayar
                                            @elseif($activity->metadata['payment_status'] === 'pending')
                                                Menunggu
                                            @else
                                                {{ ucfirst($activity->metadata['payment_status']) }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $activity->description }}</p>
                                @if($activity->type === 'order' && isset($activity->metadata['total']))
                                    <p class="text-sm text-gray-600 mt-1">Total: <span class="font-semibold">Rp {{ number_format($activity->metadata['total'], 0, ',', '.') }}</span></p>
                                @endif
                                @if($activity->type === 'order' && $activity->reference)
                                    <a href="{{ route('orders.show', $activity->reference) }}" class="text-xs text-primary hover:text-primary-dark font-medium mt-2 inline-block">
                                        Lihat Detail →
                                    </a>
                                @elseif($activity->type === 'prescription' && $activity->reference)
                                    <a href="{{ route('prescriptions.show', $activity->reference) }}" class="text-xs text-primary hover:text-primary-dark font-medium mt-2 inline-block">
                                        Lihat Detail →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                    <p class="text-gray-400 text-xs mt-1">Mulai berbelanja atau upload resep untuk melihat aktivitas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
