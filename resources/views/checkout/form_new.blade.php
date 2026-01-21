@extends('layouts.app')

@section('title', 'Checkout - Apotek Parahyangan')

@section('content')
    @php
        $selectedItemIds = $selectedItemIds ?? [];
        $invoiceNumber = $invoiceNumber ?? 'DRAFT-INV';
    @endphp
    <div class="pt-28 pb-20 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-6 md:px-8">
            <div class="mb-8">
                <p class="text-xs font-bold tracking-widest text-primary uppercase mb-2">Checkout</p>
                <h1 class="text-4xl font-display font-medium text-gray-900">Konfirmasi Pesanan</h1>
                <p class="text-gray-500 mt-2">Ambil langsung di Apotek Parahyangan PVJ Bandung</p>
            </div>

            @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-100">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('checkout.process') }}" class="space-y-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100" x-data="{ 
                        notes: '{{ old('notes') }}'
                    }">
                        @csrf

                        <!-- Selected Items (Hidden) -->
                        <input type="hidden" name="selected_items" value="{{ json_encode($selectedItemIds ?? []) }}">

                        <!-- Invoice Number Preview -->
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Nomor Invoice (Draft)</p>
                                <p class="text-xl font-mono font-bold text-gray-900 tracking-tighter">{{ $invoiceNumber }}</p>
                            </div>
                            <div class="h-10 w-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pemesan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Nama Lengkap</label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" 
                                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                    @error('customer_name')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 mb-2">Nomor WhatsApp</label>
                                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->whatsapp) }}" 
                                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                           placeholder="08xxxxxxxxxx" required>
                                    @error('phone')<p class="text-rose-600 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pickup Info -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-900 mb-1">Lokasi Pengambilan</h4>
                                    <p class="text-blue-800 text-sm mb-2">
                                        <strong>Apotek Parahyangan</strong><br>
                                        Paris Van Java Mall, Lantai Ground Floor<br>
                                        Jl. Sukajadi No.131-139, Sukagalih, Sukajadi<br>
                                        Kota Bandung, Jawa Barat 40162
                                    </p>
                                    <p class="text-blue-700 text-xs">
                                        <strong>Jam Operasional:</strong> Senin - Minggu, 10:00 - 22:00 WIB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kode Voucher</h3>
                            <div class="flex gap-2">
                                <input type="text" id="voucher_code_input" placeholder="Masukkan kode voucher" 
                                       class="flex-1 rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent uppercase">
                                <button type="button" id="apply-voucher-btn" 
                                        class="px-6 py-3 bg-gray-900 text-white rounded-xl text-xs font-bold tracking-widest uppercase hover:bg-primary transition-all duration-300">
                                    Pakai
                                </button>
                            </div>
                            <input type="hidden" name="voucher_code" id="voucher_code" value="{{ old('voucher_code') }}">
                            <div id="voucher-result" class="mt-2 hidden">
                                <div id="voucher-success" class="hidden px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-between">
                                    <div>
                                        <span id="voucher-name" class="font-semibold"></span>
                                        <span id="voucher-discount-text" class="text-sm ml-2"></span>
                                    </div>
                                    <button type="button" id="remove-voucher-btn" class="text-emerald-600 hover:text-emerald-800">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="voucher-error" class="hidden px-4 py-3 rounded-xl bg-rose-50 text-rose-700 border border-rose-100"></div>
                            </div>
                        </div>

                        <!-- Notes & Confirmation -->
                        <div>
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Catatan (Opsional)</label>
                                <textarea name="notes" rows="3" 
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                        placeholder="Catatan untuk apotek...">{{ old('notes') }}</textarea>
                            </div>

                            <p class="text-xs text-gray-500 mb-4">
                                Dengan melanjutkan, Anda menyetujui <a href="{{ route('terms') }}" target="_blank" class="text-primary hover:underline">Syarat & Ketentuan</a> kami.
                            </p>

                            <button type="submit" class="w-full bg-primary text-white py-4 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-dark transition-all duration-300 shadow-lg hover:shadow-xl">
                                Bayar Sekarang
                            </button>
                        </div>                        
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="glass-panel p-6 rounded-2xl border border-white/60 shadow-lg shadow-rose-100/20 sticky top-28">
                        <h3 class="text-lg font-display font-medium text-gray-900 mb-4">Ringkasan Pesanan</h3>
                        
                        <!-- Selected Items -->
                        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                            @if(isset($selectedItems) && $selectedItems->count() > 0)
                                @foreach($selectedItems as $item)
                                    @php
                                        $isMedicine = (bool) $item->medicine_id;
                                        $displayName = $isMedicine ? $item->medicine?->name : $item->product?->name;
                                        $displayPrice = $isMedicine ? $item->medicine?->price : ($item->product?->discount_price ?? $item->product?->price);
                                        $imageUrl = $isMedicine ? $item->medicine?->getImageUrl() : $item->product?->getImageUrl();
                                        $hasImage = $isMedicine ? $item->medicine?->hasImage() : $item->product?->hasImage();
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                            @if($hasImage)
                                                <img src="{{ $imageUrl }}" alt="{{ $displayName }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $displayName }}</h4>
                                            <p class="text-xs text-gray-600">{{ $item->quantity }}x Rp {{ number_format($displayPrice, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($displayPrice * $item->quantity, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="space-y-3 text-sm text-gray-700 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal ({{ $selectedItems->count() ?? 0 }} item)</span>
                                <span id="subtotal-display">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between" id="voucher-discount-row" style="display: none;">
                                <span>Diskon Voucher</span>
                                <span id="voucher-discount-display" class="text-emerald-600">-Rp 0</span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Pengambilan</span>
                                <span class="text-xs">Gratis</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-xl font-display font-medium text-gray-900 border-t border-gray-100 pt-4">
                            <span>Total</span>
                            <span id="total-display">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="mt-4 p-3 bg-blue-50 rounded-xl">
                            <p class="text-xs text-blue-800">
                                <strong>Catatan:</strong> Pesanan akan diproses setelah pembayaran dikonfirmasi. 
                                Anda akan mendapat notifikasi WhatsApp saat pesanan siap diambil.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Payment method selection handled by Alpine.js


            // Voucher functionality
            const voucherCodeInput = document.getElementById('voucher_code_input');
            const voucherCodeHidden = document.getElementById('voucher_code');
            const applyVoucherBtn = document.getElementById('apply-voucher-btn');
            const removeVoucherBtn = document.getElementById('remove-voucher-btn');
            const voucherResult = document.getElementById('voucher-result');
            const voucherSuccess = document.getElementById('voucher-success');
            const voucherError = document.getElementById('voucher-error');
            const voucherName = document.getElementById('voucher-name');
            const voucherDiscountText = document.getElementById('voucher-discount-text');
            const voucherDiscountRow = document.getElementById('voucher-discount-row');
            const voucherDiscountDisplay = document.getElementById('voucher-discount-display');
            const subtotalDisplay = document.getElementById('subtotal-display');
            const totalDisplay = document.getElementById('total-display');

            const subtotal = {{ $subtotal ?? 0 }};
            let currentVoucherDiscount = 0;

            const routes = {
                voucherApply: '{{ route('voucher.apply') }}',
                voucherRemove: '{{ route('voucher.remove') }}',
            };

            const updateTotal = (voucherDiscount = 0) => {
                const total = Math.max(0, subtotal - voucherDiscount);
                totalDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
            };

            const applyVoucher = async () => {
                const code = voucherCodeInput.value.trim().toUpperCase();
                if (!code) return;

                applyVoucherBtn.textContent = 'Memproses...';
                applyVoucherBtn.disabled = true;

                try {
                    const response = await axios.post(routes.voucherApply, {
                        voucher_code: code,
                        subtotal: subtotal
                    });

                    const data = response.data;
                    
                    // Show success
                    voucherError.classList.add('hidden');
                    voucherSuccess.classList.remove('hidden');
                    voucherResult.classList.remove('hidden');
                    
                    voucherName.textContent = data.voucher.name;
                    voucherDiscountText.textContent = data.voucher.type === 'percentage' 
                        ? `(${data.voucher.value}%)` 
                        : `(Rp ${new Intl.NumberFormat('id-ID').format(data.voucher.value)})`;
                    
                    voucherDiscountDisplay.textContent = `-Rp ${new Intl.NumberFormat('id-ID').format(data.discount_amount)}`;
                    voucherDiscountRow.style.display = 'flex';
                    
                    voucherCodeHidden.value = code;
                    currentVoucherDiscount = data.discount_amount;
                    
                    updateTotal(currentVoucherDiscount);
                    
                    voucherCodeInput.disabled = true;
                    applyVoucherBtn.style.display = 'none';

                } catch (error) {
                    // Show error
                    voucherSuccess.classList.add('hidden');
                    voucherError.classList.remove('hidden');
                    voucherResult.classList.remove('hidden');
                    
                    const errorMessage = error.response?.data?.message || 'Kode voucher tidak valid';
                    voucherError.textContent = errorMessage;
                } finally {
                    applyVoucherBtn.textContent = 'Pakai';
                    applyVoucherBtn.disabled = false;
                }
            };

            const removeVoucher = async () => {
                try {
                    await axios.post(routes.voucherRemove);
                    
                    // Reset UI
                    voucherResult.classList.add('hidden');
                    voucherSuccess.classList.add('hidden');
                    voucherError.classList.add('hidden');
                    voucherDiscountRow.style.display = 'none';
                    
                    voucherCodeInput.value = '';
                    voucherCodeInput.disabled = false;
                    voucherCodeHidden.value = '';
                    applyVoucherBtn.style.display = 'block';
                    
                    currentVoucherDiscount = 0;
                    updateTotal(0);
                    
                } catch (error) {
                    console.error('Error removing voucher:', error);
                }
            };

            // Event listeners
            applyVoucherBtn.addEventListener('click', applyVoucher);
            removeVoucherBtn.addEventListener('click', removeVoucher);
            
            voucherCodeInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyVoucher();
                }
            });

            // Initialize
            updateTotal(0);
        });
    </script>
@endsection