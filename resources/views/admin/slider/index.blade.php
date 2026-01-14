@extends('admin.layouts.app')

@section('title', 'Sliders Management')

@section('content')
<div class="section-container section-padding">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Homepage Sliders
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Manage the hero banners displayed on your homepage.
            </p>
        </div>
        <a href="{{ route('admin.slider.create') }}"
            class="btn-primary inline-flex items-center gap-2 group shadow-rose">
            <span>Add Slider</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </a>
    </div>

    <!-- Alerts -->
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

    <!-- Filters Toolbar -->
    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('admin.slider.index') }}" class="flex flex-col md:flex-row gap-4">
            
            <!-- Search -->
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search by label..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300"
                >
            </div>

            <!-- Status Filter -->
            <div class="relative min-w-[160px]">
                <select
                    name="status"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">All Status</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archived</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Has Image Filter -->
            <div class="relative min-w-[160px]">
                <select
                    name="has_image"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">Image Status</option>
                    <option value="with" @selected(($filters['has_image'] ?? '') === 'with')>Has Image</option>
                    <option value="without" @selected(($filters['has_image'] ?? '') === 'without')>No Image</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                    Filter
                </button>
                
                @if (! empty($filters['search']) || ! empty($filters['status']) || ! empty($filters['has_image']))
                    <a href="{{ route('admin.slider.index') }}" class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl border border-transparent hover:border-rose-100" title="Reset">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sliders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 0.2s;">
        @forelse ($sliders as $slider)
            <div class="glass-panel p-4 rounded-3xl group hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300 flex flex-col h-full">
                <!-- Image Container -->
                <div class="relative aspect-[16/9] rounded-2xl overflow-hidden mb-4 bg-gray-100 shadow-inner">
                    @if($slider->hasImage())
                        <img src="{{ $slider->getImageUrl() }}" 
                             alt="{{ $slider->label }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                            <svg class="w-10 h-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium uppercase tracking-wider">No Image</span>
                        </div>
                    @endif
                    
                    <!-- Badges Overlay -->
                    <div class="absolute top-3 right-3 flex flex-col gap-2 items-end">
                        @if ($slider->status === 'active')
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white shadow-sm backdrop-blur-md">
                                Active
                            </span>
                        @elseif($slider->status === 'draft')
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-500/80 text-white shadow-sm backdrop-blur-md">
                                Draft
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-500/80 text-white shadow-sm backdrop-blur-md">
                                Archived
                            </span>
                        @endif
                    </div>

                    <div class="absolute top-3 left-3">
                        <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/90 backdrop-blur-md text-gray-900 font-display font-bold shadow-sm border border-white/50">
                            {{ $slider->position }}
                        </span>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-display truncate mb-1" title="{{ $slider->label }}">
                            {{ $slider->label ?: 'Untitled Slider' }}
                        </h3>
                        <p class="text-xs text-gray-500 font-mono">
                            ID: #{{ $slider->id }} • Updated {{ $slider->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.slider.edit', $slider->id) }}" class="p-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Edit">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.slider.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Toggle Status (Visual Only for now, could be interactive with Livewire/JS) -->
                        <div class="h-2 w-2 rounded-full {{ $slider->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300' }}" title="Status Indicator"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-panel p-12 rounded-[2.5rem] text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="w-24 h-24 bg-gradient-to-tr from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold font-display text-gray-900 mb-2">No Sliders Yet</h3>
                <p class="text-gray-500 mb-8 max-w-sm font-light">Add visual impact to your homepage by creating your first slider banner.</p>
                <a href="{{ route('admin.slider.create') }}" class="btn-primary shadow-lg shadow-rose-200/50">
                    Create First Slider
                </a>
            </div>
        @endforelse
    </div>
    
    @if ($sliders->hasPages())
        <div class="mt-8">
            {{ $sliders->links() }}
        </div>
    @endif
</div>
@endsection