@extends('layouts.app')

@section('title', 'Apotek Parahyangan - Solusi Kesehatan Terpercaya')

@section('content')

    {{-- ========================================
     1. HERO SECTION - HEALTH PORTAL STYLE
     ======================================== --}}
    <section class="relative min-h-[500px] flex items-center pt-12 pb-12 overflow-hidden bg-slate-50">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-1/3 h-full opacity-10 pointer-events-none">
            <svg class="w-full h-full text-rose-500" viewBox="0 0 100 100" fill="currentColor">
                <circle cx="80" cy="20" r="40" />
            </svg>
        </div>
        <div class="absolute bottom-0 left-0 w-1/4 h-1/2 opacity-5 pointer-events-none">
            <svg class="w-full h-full text-blue-500" viewBox="0 0 100 100" fill="currentColor">
                <rect x="0" y="50" width="100" height="50" rx="10" />
            </svg>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-display font-bold text-gray-900 leading-tight">
                    Solusi Kesehatan <span class="text-rose-500 italic">Terpercaya</span> Untuk Anda
                </h1>
                <p class="text-lg md:text-xl text-gray-600 font-light max-w-2xl mx-auto">
                    Cari obat, konsultasi apoteker, dan dapatkan informasi kesehatan resmi dalam satu aplikasi.
                </p>

                {{-- Unified Search Bar --}}
                <div class="max-w-2xl mx-auto pt-4">
                    <form action="{{ route('products.index') }}" method="GET" class="relative group">
                        <input type="text" name="search" placeholder="Cari obat, vitamin, atau kebutuhan kesehatan..."
                            class="w-full h-16 pl-14 pr-6 rounded-full bg-white shadow-2xl shadow-rose-100/50 border border-gray-100 focus:border-rose-400 focus:ring-4 focus:ring-rose-500/5 transition-all outline-none text-gray-700 text-lg">
                        <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-rose-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                    <div class="flex flex-wrap justify-center gap-2 mt-4 text-xs font-medium text-gray-400">
                        <span>Paling dicari:</span>
                        <a href="{{ route('products.index', ['search' => 'Vitamin C']) }}" class="hover:text-rose-500 transition-colors">Vitamin C</a>
                        <span class="opacity-30">|</span>
                        <a href="{{ route('products.index', ['search' => 'Paracetamol']) }}" class="hover:text-rose-500 transition-colors">Paracetamol</a>
                        <span class="opacity-30">|</span>
                        <a href="{{ route('products.index', ['search' => 'Masker']) }}" class="hover:text-rose-500 transition-colors">Masker</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================
     2. SERVICE SHORTCUTS GRID
     ======================================== --}}
    <section class="py-12 -mt-10 relative z-20">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                {{-- Store Shortcut --}}
                <a href="{{ route('products.index') }}" class="group glass-panel p-6 rounded-3xl border border-white hover:border-rose-200 transition-all duration-300 hover:shadow-xl hover:shadow-rose-100/50 flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Toko Obat</h4>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Beli Online</p>
                    </div>
                </a>

                {{-- Consultation Shortcut --}}
                <a href="#consultation" class="group glass-panel p-6 rounded-3xl border border-white hover:border-blue-200 transition-all duration-300 hover:shadow-xl hover:shadow-blue-100/50 flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Konsultasi</h4>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Tanya Apoteker</p>
                    </div>
                </a>

                {{-- Prescription Shortcut --}}
                <a href="#upload-recipe" class="group glass-panel p-6 rounded-3xl border border-white hover:border-emerald-200 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-100/50 flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Upload Resep</h4>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Sesuai Dokter</p>
                    </div>
                </a>

                {{-- Articles Shortcut --}}
                <a href="{{ route('articles.index') }}" class="group glass-panel p-6 rounded-3xl border border-white hover:border-amber-200 transition-all duration-300 hover:shadow-xl hover:shadow-amber-100/50 flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Info Sehat</h4>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Baca Jurnal</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================
     3. FEATURED MEDICINES (TOKO OBAT)
     ======================================== --}}
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 px-4">
                <div class="space-y-2">
                    <h2 class="text-xs font-bold text-rose-500 tracking-[0.2em] uppercase">Paling Dicari</h2>
                    <h3 class="text-3xl md:text-4xl font-display font-medium text-gray-900 pt-2">TOKO OBAT POPULER</h3>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-gray-400 hover:text-rose-500 transition-colors uppercase tracking-widest border-b border-gray-100 pb-2 mt-6 md:mt-0">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-8">
                @forelse($featuredMedicines as $medicine)
                    <x-product-card :product="$medicine" :index="$loop->index" />
                @empty
                    <div class="col-span-full text-center py-20 bg-slate-50 rounded-3xl border border-dashed border-gray-200">
                        <p class="text-gray-400">Tidak ada obat unggulan tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ========================================
     4. SKIN TALKS / INFO KESEHATAN
     ======================================== --}}
    <section class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-xs font-bold tracking-[0.2em] text-rose-500 uppercase mb-3">Info Kesehatan Resmi</h2>
                <h3 class="text-3xl md:text-5xl font-display font-medium text-gray-900 pt-2">JURNAL APOTEK</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($articles as $article)
                    <x-article-card :article="$article" :index="$loop->index" />
                @endforeach
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('articles.index') }}"
                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-10 py-4 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-rose-500 transition-all duration-300 shadow-xl shadow-gray-200">
                    Buka Jurnal Kesehatan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================
     5. CALL TO ACTION / DOWNLOAD
     ======================================== --}}
    <section class="py-24">
        <div class="container mx-auto px-6">
            <div class="bg-gray-900 rounded-[3rem] p-8 md:p-16 relative overflow-hidden group">
                {{-- Decorative pattern --}}
                <div class="absolute inset-0 opacity-10 pointer-events-none group-hover:opacity-20 transition-opacity">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
                        <rect width="100" height="100" fill="url(#grid)"/>
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left">
                    <div class="max-w-xl space-y-6">
                        <h2 class="text-3xl md:text-5xl font-display font-medium text-white leading-tight">
                            Layanan Kesehatan Dalam <span class="text-rose-500">Genggaman</span>
                        </h2>
                        <p class="text-gray-400 font-light text-lg">
                            Dapatkan akses prioritas dan diskon eksklusif dengan berkonsultasi langsung melalui platform kami.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#" class="px-10 py-5 bg-rose-500 text-white rounded-full font-bold text-xs tracking-widest uppercase hover:bg-white hover:text-gray-900 transition-all duration-300">
                            Konsultasi Sekarang
                        </a>
                        <a href="{{ route('products.index') }}" class="px-10 py-5 border border-white/20 text-white rounded-full font-bold text-xs tracking-widest uppercase hover:bg-white/10 transition-all duration-300">
                            Pesan Obat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .animate-fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
