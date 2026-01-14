@extends('admin.layouts.app')

@section('title', 'Medicines Management')

@section('content')
    <div class="section-container section-padding">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                    Medicines
                </h1>
                <p class="text-gray-500 font-light text-lg">
                    Manage pharmacy products, stock, and images.
                </p>
            </div>
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

        <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up">
            <form method="GET" action="{{ route('admin.medicines.index') }}" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="relative group col-span-1 md:col-span-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search medicines by name or code..." class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300">
                    </div>

                    <div class="relative">
                        <select name="category_id" class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($categoryId ?? null) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                            Apply Filters
                        </button>
                        @if($search || $categoryId)
                            <a href="{{ route('admin.medicines.index') }}" class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl" title="Reset Filters">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-white/30 backdrop-blur-sm">
                            <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Medicine Details</th>
                            <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Category</th>
                            <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Stock</th>
                            <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Price</th>
                            <th class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($medicines as $medicine)
                            <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-sm bg-gray-100 flex-shrink-0 relative group-hover:shadow-md transition-all duration-300">
                                            @if ($medicine->hasImage())
                                                <img src="{{ $medicine->getImageUrl() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $medicine->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300 bg-rose-50">
                                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-base font-bold text-gray-900 font-display mb-1">{{ $medicine->name }}</div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-xs text-gray-400 font-mono tracking-wider uppercase">{{ $medicine->code }}</div>
                                                @if($medicine->classification)
                                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                    <span class="text-[9px] font-bold uppercase tracking-widest {{ $medicine->classification === 'Obat Keras' ? 'text-rose-500' : 'text-emerald-500' }}">
                                                        {{ $medicine->classification }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                        {{ $medicine->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-mono font-bold text-gray-700">{{ $medicine->total_stock_unit }} {{ $medicine->base_unit }}</span>
                                        @if ($medicine->total_stock_unit <= $medicine->min_stock_alert)
                                            <span class="text-[10px] text-rose-500 font-bold uppercase tracking-widest mt-1">Low Stock</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="font-bold text-gray-900">{{ $medicine->formatted_price }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.medicines.edit', $medicine->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Medicines Found</h3>
                                        <p class="text-gray-500 font-light">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($medicines->hasPages())
                <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                    {{ $medicines->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
