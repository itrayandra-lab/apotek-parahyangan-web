@extends('admin.layouts.app')

@section('title', $voucher ? 'Edit Voucher' : 'Tambah Voucher')

@section('content')
<div class="section-container section-padding max-w-4xl">
    <div class="mb-8">
        <a href="{{ route('admin.vouchers.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-rose-500 transition-colors group mb-4">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="font-medium">Kembali ke Vouchers</span>
        </a>
        <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 tracking-wide">
            {{ $voucher ? 'Edit Voucher' : 'Tambah Voucher' }}
        </h1>
    </div>

    <form action="{{ $voucher ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}" method="POST" class="space-y-8">
        @csrf
        @if ($voucher)
            @method('PUT')
        @endif

        <div class="glass-panel rounded-3xl p-8 space-y-6">
            <h2 class="text-lg font-display font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-4">Informasi Voucher</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Kode Voucher <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $voucher?->code) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all uppercase font-mono tracking-wider @error('code') border-red-300 @enderror"
                        placeholder="BEAUTY20" required>
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Voucher <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $voucher?->name) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('name') border-red-300 @enderror"
                        placeholder="Diskon Spesial 20%" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all resize-none @error('description') border-red-300 @enderror"
                    placeholder="Deskripsi voucher (opsional)">{{ old('description', $voucher?->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-8 space-y-6">
            <h2 class="text-lg font-display font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-4">Tipe & Nilai</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Tipe Voucher <span class="text-rose-500">*</span></label>
                    <select name="type" id="type"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('type') border-red-300 @enderror" required>
                        <option value="percentage" @selected(old('type', $voucher?->type) === 'percentage')>Persentase (%)</option>
                        <option value="fixed" @selected(old('type', $voucher?->type) === 'fixed')>Nominal (Rp)</option>
                        <option value="free_shipping" @selected(old('type', $voucher?->type) === 'free_shipping')>Gratis Ongkir</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="value-container">
                    <label for="value" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nilai <span class="text-rose-500">*</span></label>
                    <input type="number" name="value" id="value" value="{{ old('value', $voucher?->value ?? 0) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('value') border-red-300 @enderror"
                        min="0" required>
                    <p class="mt-1 text-xs text-gray-500" id="value-hint">Masukkan persentase (misal: 20 untuk 20%)</p>
                    @error('value')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="min_purchase" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Minimum Pembelian</label>
                    <input type="number" name="min_purchase" id="min_purchase" value="{{ old('min_purchase', $voucher?->min_purchase ?? 0) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('min_purchase') border-red-300 @enderror"
                        min="0" placeholder="0">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan atau 0 jika tidak ada minimum</p>
                    @error('min_purchase')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="max-discount-container">
                    <label for="max_discount" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Maksimum Diskon</label>
                    <input type="number" name="max_discount" id="max_discount" value="{{ old('max_discount', $voucher?->max_discount) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('max_discount') border-red-300 @enderror"
                        min="0" placeholder="Tanpa batas">
                    <p class="mt-1 text-xs text-gray-500">Batas maksimum potongan (untuk tipe persentase)</p>
                    @error('max_discount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-8 space-y-6">
            <h2 class="text-lg font-display font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-4">Batas Penggunaan</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="usage_limit" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Total Penggunaan</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $voucher?->usage_limit) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('usage_limit') border-red-300 @enderror"
                        min="1" placeholder="Tanpa batas">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas</p>
                    @error('usage_limit')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="usage_limit_per_user" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Per User <span class="text-rose-500">*</span></label>
                    <input type="number" name="usage_limit_per_user" id="usage_limit_per_user" value="{{ old('usage_limit_per_user', $voucher?->usage_limit_per_user ?? 1) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('usage_limit_per_user') border-red-300 @enderror"
                        min="1" required>
                    <p class="mt-1 text-xs text-gray-500">Berapa kali satu user bisa pakai voucher ini</p>
                    @error('usage_limit_per_user')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-8 space-y-6">
            <h2 class="text-lg font-display font-bold uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-4">Periode Berlaku</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="valid_from" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Mulai Berlaku</label>
                    <input type="datetime-local" name="valid_from" id="valid_from" value="{{ old('valid_from', $voucher?->valid_from?->format('Y-m-d\TH:i')) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('valid_from') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika langsung aktif</p>
                    @error('valid_from')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Berakhir</label>
                    <input type="datetime-local" name="valid_until" id="valid_until" value="{{ old('valid_until', $voucher?->valid_until?->format('Y-m-d\TH:i')) }}"
                        class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 focus:border-rose-300 transition-all @error('valid_until') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                    @error('valid_until')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    class="w-5 h-5 text-rose-500 border-gray-300 rounded focus:ring-rose-200"
                    @checked(old('is_active', $voucher?->is_active ?? true))>
                <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan voucher ini</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.vouchers.index') }}"
                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-display font-medium uppercase tracking-wider text-sm transition-all">
                Batal
            </a>
            <button type="submit"
                class="btn-primary shadow-lg shadow-rose-200/50">
                {{ $voucher ? 'Simpan Perubahan' : 'Buat Voucher' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueContainer = document.getElementById('value-container');
    const valueInput = document.getElementById('value');
    const valueHint = document.getElementById('value-hint');
    const maxDiscountContainer = document.getElementById('max-discount-container');

    function updateValueField() {
        const type = typeSelect.value;

        if (type === 'percentage') {
            valueContainer.style.display = 'block';
            valueHint.textContent = 'Masukkan persentase (misal: 20 untuk 20%)';
            maxDiscountContainer.style.display = 'block';
        } else if (type === 'fixed') {
            valueContainer.style.display = 'block';
            valueHint.textContent = 'Masukkan nominal dalam Rupiah';
            maxDiscountContainer.style.display = 'none';
        } else {
            valueContainer.style.display = 'none';
            maxDiscountContainer.style.display = 'none';
            valueInput.value = 0;
        }
    }

    typeSelect.addEventListener('change', updateValueField);
    updateValueField();
});
</script>
@endpush
@endsection
