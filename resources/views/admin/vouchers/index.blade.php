@extends('admin.layouts.app')

@section('title', 'Vouchers Management')

@section('content')
<div class="section-container section-padding">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Vouchers
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Kelola voucher dan kupon diskon.
            </p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}"
            class="btn-primary inline-flex items-center gap-2 group shadow-rose">
            <span>Tambah Voucher</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </a>
    </div>

    @if (session('success'))
        <div class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif

    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="relative group flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode atau nama voucher..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300">
            </div>

            <div class="relative">
                <select name="type"
                    class="block w-full md:w-48 pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer">
                    <option value="">Semua Tipe</option>
                    <option value="percentage" @selected(request('type') === 'percentage')>Persentase</option>
                    <option value="fixed" @selected(request('type') === 'fixed')>Nominal</option>
                    <option value="free_shipping" @selected(request('type') === 'free_shipping')>Gratis Ongkir</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="relative">
                <select name="status"
                    class="block w-full md:w-40 pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('admin.vouchers.index') }}"
                        class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl"
                        title="Reset Filters">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-white/30 backdrop-blur-sm">
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Voucher</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Tipe</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Nilai</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Penggunaan</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Berlaku</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Status</th>
                        <th class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vouchers as $voucher)
                        <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                            <td class="px-8 py-6">
                                <div>
                                    <div class="text-base font-bold text-gray-900 font-mono tracking-wider">{{ $voucher->code }}</div>
                                    <div class="text-sm text-gray-500">{{ $voucher->name }}</div>
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                @if ($voucher->type === 'percentage')
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                        Persentase
                                    </span>
                                @elseif ($voucher->type === 'fixed')
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-100">
                                        Nominal
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        Gratis Ongkir
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-6">
                                <span class="text-sm font-semibold text-gray-900">{{ $voucher->getValueFormatted() }}</span>
                                @if ($voucher->min_purchase > 0)
                                    <div class="text-xs text-gray-500">Min. Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-6">
                                <span class="text-sm text-gray-600">
                                    {{ $voucher->usage_count }}{{ $voucher->usage_limit ? ' / ' . $voucher->usage_limit : '' }}
                                </span>
                            </td>

                            <td class="px-6 py-6">
                                <div class="text-sm text-gray-600">
                                    @if ($voucher->valid_from && $voucher->valid_until)
                                        {{ $voucher->valid_from->format('d M Y') }} - {{ $voucher->valid_until->format('d M Y') }}
                                    @elseif ($voucher->valid_from)
                                        Mulai {{ $voucher->valid_from->format('d M Y') }}
                                    @elseif ($voucher->valid_until)
                                        Sampai {{ $voucher->valid_until->format('d M Y') }}
                                    @else
                                        <span class="text-gray-400">Tanpa batas</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                @if ($voucher->is_active && $voucher->isValid())
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="inline-flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                        title="Edit Voucher">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus voucher ini?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                            title="Hapus Voucher">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gradient-to-tr from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold font-display text-gray-900 mb-2">Belum Ada Voucher</h3>
                                    <p class="text-gray-500 mb-8 max-w-sm mx-auto font-light">Buat voucher pertama untuk memberikan diskon kepada pelanggan.</p>
                                    <a href="{{ route('admin.vouchers.create') }}" class="btn-primary shadow-lg shadow-rose-200/50">
                                        Tambah Voucher
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($vouchers->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
