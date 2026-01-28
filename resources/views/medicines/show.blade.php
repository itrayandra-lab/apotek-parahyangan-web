@extends('layouts.app')

@section('title', $medicine->name . ' - Apotek Parahyangan')

@section('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    [x-cloak] { display: none !important; }

    .tab-active { @apply text-rose-500 border-b-2 border-rose-500; }
    .tab-inactive { @apply text-gray-400 border-b-2 border-transparent hover:text-gray-600; }
</style>
@endsection

@section('content')
    <div class="pt-32 pb-24 min-h-screen bg-white"
         x-data="{
            quantity: 1,
            activeTab: 'detail',
            basePrice: {{ $medicine->price }},
            stock: {{ $medicine->total_stock_unit }},
            addingToCart: false,
            get totalPrice() {
                return (this.basePrice * this.quantity).toLocaleString('id-ID');
            },
            decreaseQty() { if(this.quantity > 1) this.quantity--; },
            increaseQty() { if(this.quantity < this.stock) this.quantity++; },
            async addToCart() {
                if(this.stock <= 0) {
                    window.showToast?.('Maaf, stok habis.', 'error');
                    return;
                }
                this.addingToCart = true;
                try {
                    await axios.post('{{ route('cart.add') }}', {
                        medicine_id: {{ $medicine->id }},
                        quantity: this.quantity,
                    });
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.showToast?.('Produk ditambahkan ke keranjang.');
                } catch (error) {
                    window.showToast?.(error?.response?.data?.message ?? 'Gagal menambahkan ke keranjang.', 'error');
                } finally {
                    this.addingToCart = false;
                }
            }
         }">

        <div class="container mx-auto px-6 md:px-8">
            {{-- Breadcrumbs --}}
            <nav class="mb-8 flex items-center text-sm text-gray-500 font-sans">
                <a href="{{ route('home') }}" class="hover:text-rose-500 transition-colors">Home</a>
                <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('products.index') }}" class="hover:text-rose-500 transition-colors">Apotek</a>
                <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-400 truncate">{{ $medicine->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                {{-- Left Side: Visuals (Single Image) --}}
                <div class="lg:col-span-6">
                    <div class="sticky top-32">
                        {{-- Main Image Display --}}
                        <div class="relative aspect-square rounded-[2rem] overflow-hidden bg-gray-50 border border-gray-100 group mb-6">
                            @if($medicine->hasImage())
                                <img src="{{ $medicine->getImageUrl() }}" alt="{{ $medicine->name }}" 
                                     class="w-full h-full object-contain p-8 lg:p-12 transition-all duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    <span class="text-xs font-bold uppercase tracking-widest">No Image Available</span>
                                </div>
                            @endif

                            {{-- Corner Labels (BPOM / Original) --}}
                            <div class="absolute top-6 left-6 flex flex-col gap-2">
                                <div class="bg-rose-500/10 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-2 border border-rose-500/20">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-[9px] font-bold text-rose-700 uppercase tracking-widest">Garansi Original</span>
                                </div>
                            </div>
                        </div>

                        {{-- Regulatory Badges --}}
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-3xl bg-gray-50 border border-gray-100 flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm overflow-hidden">
                                     <img src="https://upload.wikimedia.org/wikipedia/id/a/a8/BADAN_POM.png" class="w-8 object-contain" alt="BPOM">
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">BPOM Registered</span>
                                    <span class="text-[10px] font-bold text-gray-900 truncate">{{ $medicine->bpom_number ?? 'Verified' }}</span>
                                </div>
                            </div>
                            <div class="p-4 rounded-3xl bg-gray-50 border border-gray-100 flex items-center gap-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-rose-500">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Classification</span>
                                    <span class="text-[10px] font-bold text-gray-900">{{ $medicine->classification ?? 'General' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Information & Transaction --}}
                <div class="lg:col-span-6">
                    <div class="mb-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold tracking-[0.2em] text-rose-600 uppercase">By {{ $medicine->manufacturer }}</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-sans font-semibold text-gray-900 mb-4">{{ $medicine->name }}</h1>
                        <p class="text-gray-400 text-xs mb-6">10+ orang telah memesan barang ini</p>
                        
                        <div class="flex items-baseline gap-2 mb-10">
                            <span class="text-4xl font-sans font-bold text-gray-900">{{ $medicine->formatted_price }}</span>
                            <span class="text-xs text-gray-400 uppercase tracking-widest">/ {{ $medicine->base_unit ?? 'Satuan' }}</span>
                        </div>

                        {{-- Tabs Navigation --}}
                        <div class="flex gap-8 mb-8">
                            <button @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'tab-active' : 'tab-inactive'" class="pb-4 text-sm font-bold transition-all duration-300">Detail Produk</button>
                            <button @click="activeTab = 'spesifikasi'" :class="activeTab === 'spesifikasi' ? 'tab-active' : 'tab-inactive'" class="pb-4 text-sm font-bold transition-all duration-300">Spesifikasi</button>
                            <button @click="activeTab = 'penting'" :class="activeTab === 'penting' ? 'tab-active' : 'tab-inactive'" class="pb-4 text-sm font-bold transition-all duration-300">Info Penting</button>
                        </div>

                        {{-- Tab Contexts --}}
                        <div class="min-h-[300px]">
                            @php
                                $lastUnit = $medicine->medicineUnits()->latest('id')->first();
                            @endphp
                            {{-- Detail Produk --}}
                            <div x-show="activeTab === 'detail'" x-cloak x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4 gap-x-4 text-sm">
                                        <div class="flex justify-between py-2 border-b border-gray-50">
                                            <span class="text-gray-400">Stok</span>
                                            <span class="text-gray-900 font-medium">
                                                {{ floor($medicine->total_stock_unit / ($lastUnit->conversion_quantity ?? 1)) }} {{ $medicine->base_unit ?? 'Pcs' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between py-2 border-b border-gray-50">
                                            <span class="text-gray-400">Kemasan</span>
                                            <span class="text-gray-900 font-medium">
                                                {{ ($lastUnit->conversion_quantity ?? 1) . ' ' . ($lastUnit->unit_name ?? $medicine->base_unit) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between py-2 border-b border-gray-50">
                                            <span class="text-gray-400">Min. Beli</span>
                                            <span class="text-gray-900 font-medium">1 {{ $medicine->base_unit ?? 'Pcs' }}</span>
                                        </div>
                                        <div class="flex justify-between py-2 border-b border-gray-50">
                                            <span class="text-gray-400">Kategori</span>
                                            <span class="text-rose-500 font-bold uppercase">{{ $medicine->category->name ?? 'Obat' }}</span>
                                        </div>
                                    </div>
                                    <div class="prose prose-sm prose-rose max-w-none text-gray-600 leading-relaxed">
                                        <h4 class="text-gray-900 font-bold mb-3 uppercase tracking-widest text-xs">Deskripsi & Indikasi</h4>
                                        <p class="whitespace-pre-line">{{ $medicine->indication ?? 'Informasi indikasi umum untuk produk ini belum tersedia secara detail.' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Spesifikasi Medis --}}
                            <div x-show="activeTab === 'spesifikasi'" x-cloak x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-8">
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-[0.2em] mb-4 border-l-4 border-rose-500 pl-4">Komposisi Bahan</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-6 rounded-2xl italic">
                                            {{ $medicine->composition ?? 'Bahan aktif tidak tercantum detail.' }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-[0.2em] mb-4 border-l-4 border-rose-500 pl-4">Dosis & Aturan Pakai</h4>
                                        <div class="text-sm text-gray-600 leading-relaxed">
                                            {!! nl2br(e($medicine->dosage ?? 'Gunakan sesuai anjuran dokter atau instruksi label kemasan.')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Penting --}}
                            <div x-show="activeTab === 'penting'" x-cloak x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="space-y-6">
                                    <div class="bg-rose-50 border border-rose-100 p-6 rounded-3xl">
                                        <div class="flex items-center gap-3 mb-4 text-rose-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <h4 class="text-xs font-bold uppercase tracking-widest">Keamanan & Kontraindikasi</h4>
                                        </div>
                                        <p class="text-sm text-rose-900/70 leading-relaxed">
                                            {{ $medicine->side_effects ?? 'Harap berhati-hati bagi pengguna dengan riwayat alergi tertentu. Segera konsultasikan ke dokter jika terjadi reaksi negatif.' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-6 rounded-3xl">
                                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-3">Penyimpanan</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed font-light">
                                            {{ $medicine->shelf_location ? 'Penyimpanan: ' . $medicine->shelf_location . '. ' : '' }}Simpan di tempat yang kering dengan suhu di bawah 30°C serta terlindung dari cahaya matahari langsung. Jauhkan dari jangkauan anak-anak.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Transaction Box --}}
                        <div class="mt-12 fade-in-up">
                            <div class="glass-panel p-8 rounded-[2rem] border border-white/60 shadow-2xl shadow-rose-900/5 backdrop-blur-xl">
                                <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                                    <div class="flex items-center gap-4 bg-white/50 rounded-2xl px-6 py-4 border border-gray-100 shadow-sm w-full md:w-auto grow">
                                        <button @click="decreaseQty()" class="text-gray-400 hover:text-rose-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="text-lg font-bold text-gray-900 w-10 text-center" x-text="quantity"></span>
                                        <button @click="increaseQty()" class="text-gray-400 hover:text-rose-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m7-7H5"/></svg>
                                        </button>
                                    </div>
                                    <div class="text-right w-full md:w-auto">
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Subtotal Harga</span>
                                        <span class="text-3xl font-bold text-gray-900">Rp <span x-text="totalPrice"></span></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <button @click="addToCart()" :disabled="addingToCart || stock <= 0"
                                        class="w-full bg-rose-500 text-white py-5 rounded-2xl text-xs font-bold tracking-[0.2em] uppercase hover:bg-rose-600 transition-all duration-300 shadow-xl shadow-rose-500/20 flex items-center justify-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        <span x-text="addingToCart ? 'Menambahkan...' : 'Beli Sekarang'"></span>
                                    </button>
                                    <a href="https://wa.me/6281234567890?text=Halo%20Apotek%20Parahyangan,%20saya%20ingin%20tanya%20tentang%20produk%20{{ urlencode($medicine->name) }}" 
                                       target="_blank"
                                       class="w-full bg-white text-rose-500 border border-rose-100 py-5 rounded-2xl text-xs font-bold tracking-[0.15em] uppercase hover:bg-rose-50 transition-all text-center flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/></svg>
                                        Tanya Apoteker
                                    </a>
                                </div>
                                
                                <div class="mt-6 flex items-center justify-center gap-6">
                                    @if($medicine->total_stock_unit <= 5 && $medicine->total_stock_unit > 0)
                                        <p class="text-[10px] font-bold text-rose-500 uppercase animate-pulse">Sisa Stok Sedikit!</p>
                                    @endif
                                    @if($medicine->total_stock_unit <= 0)
                                        <p class="text-[10px] font-bold text-rose-600 uppercase">Maaf, Stok Habis</p>
                                    @endif
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">Original & Terjamin</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Related Products --}}
    @if($relatedMedicines->isNotEmpty())
    <section class="py-24 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-6 md:px-8">
            <div class="mb-16">
                <h3 class="text-2xl font-display font-medium text-gray-900 uppercase">Produk Serupa</h3>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($relatedMedicines as $related)
                    <div class="group">
                        <a href="{{ route('products.show', $related->code) }}" class="block">
                            <div class="relative aspect-square rounded-[2rem] overflow-hidden mb-4 bg-white border border-gray-100 p-2 transition-all duration-500 group-hover:shadow-xl group-hover:-translate-y-2">
                                <div class="relative w-full h-full rounded-[1.5rem] overflow-hidden bg-gray-50 flex items-center justify-center">
                                    @if($related->hasImage())
                                        <img src="{{ $related->getImageUrl() }}" alt="{{ $related->name }}" class="w-full h-full object-contain p-4 transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    @endif
                                </div>
                            </div>
                            <h4 class="text-xs font-sans font-medium text-gray-900 group-hover:text-rose-500 transition-colors line-clamp-2 uppercase tracking-wide px-2">{{ $related->name }}</h4>
                            <p class="text-sm font-bold text-gray-900 mt-2 px-2">{{ $related->formatted_price }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
