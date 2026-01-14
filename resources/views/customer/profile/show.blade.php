@extends('layouts.app')

@section('title', 'Profil Saya - Beautylatory')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="container mx-auto px-6 md:px-8 max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('customer.dashboard') }}" class="text-xs font-bold tracking-widest text-primary uppercase hover:text-primary-dark mb-2 inline-block">&larr; Kembali ke Dashboard</a>
            <p class="text-xs font-bold tracking-[0.2em] text-primary uppercase">Profil</p>
            <h1 class="text-4xl font-display font-medium text-gray-900 mt-2">Informasi Akun</h1>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Nama Lengkap</p>
                    <p class="text-gray-900 font-semibold text-lg">{{ $user->name }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Username</p>
                    <p class="text-gray-900 font-semibold text-lg">{{ $user->username }}</p>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Email</p>
                <p class="text-gray-900 font-semibold text-lg">{{ $user->email }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Role</p>
                    <p class="text-gray-900 font-semibold text-lg capitalize">{{ $user->role }}</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Member Sejak</p>
                    <p class="text-gray-900 font-semibold text-lg">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <a href="{{ route('customer.profile.edit') }}" class="flex-1 text-center bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                    Edit Profil
                </a>
                <a href="{{ route('addresses.index') }}" class="flex-1 text-center bg-white border border-gray-200 text-gray-900 px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-50 transition-all duration-300">
                    Kelola Alamat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
