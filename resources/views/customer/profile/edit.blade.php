@extends('layouts.app')

@section('title', 'Edit Profil - Beautylatory')

@section('content')
<div class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="container mx-auto px-6 md:px-8 max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('customer.profile.show') }}" class="text-xs font-bold tracking-widest text-primary uppercase hover:text-primary-dark mb-2 inline-block">&larr; Kembali ke Profil</a>
            <p class="text-xs font-bold tracking-[0.2em] text-primary uppercase">Profil</p>
            <h1 class="text-4xl font-display font-medium text-gray-900 mt-2">Edit Profil</h1>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.profile.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="username" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username</label>
                <input type="text"
                       name="username"
                       id="username"
                       value="{{ old('username', $user->username) }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent text-gray-900"
                       required>
                @error('username')
                    <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Ubah Password</h3>
                <p class="text-xs text-gray-400 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent text-gray-900"
                               placeholder="••••••••">
                        @error('password')
                            <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent text-gray-900"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="flex-1 bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                    Simpan Perubahan
                </button>
                <a href="{{ route('customer.profile.show') }}" class="flex-1 text-center bg-white border border-gray-200 text-gray-900 px-6 py-3 rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-gray-50 transition-all duration-300">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
