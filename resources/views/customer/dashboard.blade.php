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
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Role</p>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($user->role) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @php
                    $ordersCount = $user->orders()->count();
                    $unpaidCount = $user->orders()->where('payment_status', 'unpaid')->count();
                    $paidCount = $user->orders()->where('payment_status', 'paid')->count();
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
                        <a href="{{ route('products.index') }}" class="w-full text-center bg-primary text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary-dark transition-colors">Belanja Produk</a>
                        <a href="{{ route('addresses.index') }}" class="w-full text-center bg-gray-900 text-white px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-colors">Alamat Saya</a>
                        <a href="{{ route('customer.profile.show') }}" class="w-full text-center bg-gray-100 text-gray-700 px-4 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-200 transition-colors">Profil Saya</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-display font-medium text-gray-900 mb-3">Aktivitas Terbaru</h2>
            <p class="text-gray-600">Belum ada aktivitas. Mulai dengan membuat pesanan atau perbarui profilmu.</p>
        </div>
    </div>
</div>
@endsection
