@extends('layouts.app')

@section('title', 'Pembayaran Gagal - Beautylatory')

@section('content')
    <div class="pt-28 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8 max-w-3xl text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h1 class="text-3xl font-display font-medium text-gray-900 mb-3">Pembayaran Gagal</h1>
                <p class="text-gray-600 mb-6">Terjadi kendala pada pembayaran. Silakan coba lagi atau pilih metode lain.</p>
                <a href="{{ route('customer.dashboard') }}"
                   class="bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
