@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="section-container section-padding">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Categories
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Organize your products into collections.
            </p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
            class="btn-primary inline-flex items-center gap-2 group shadow-rose">
            <span>Add Category</span>
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

    @if (session('error'))
        <div class="glass-panel border-l-4 border-rose-500 text-rose-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('error') }}</span>
        </div>
    @endif

    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col md:flex-row gap-4">
            
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
                    placeholder="Search categories..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300"
                >
            </div>

            <div class="relative min-w-[160px]">
                <select
                    name="status"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">All Status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="hidden" @selected(($filters['status'] ?? '') === 'hidden')>Hidden</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="relative min-w-[180px]">
                <select
                    name="has_products"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">All Categories</option>
                    <option value="with" @selected(($filters['has_products'] ?? '') === 'with')>With Products</option>
                    <option value="without" @selected(($filters['has_products'] ?? '') === 'without')>Empty</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                    Filter
                </button>
                
                @if (! empty($filters['search']) || ! empty($filters['status']) || ! empty($filters['has_products']))
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl border border-transparent hover:border-rose-100" title="Reset">
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
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Category Name</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Products</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Status</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Created</th>
                        <th class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($categories as $category)
                        <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center text-rose-500 shadow-sm">
                                        <span class="font-display font-bold text-lg">{{ substr($category->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-base font-bold text-gray-900 font-display">{{ $category->name }}</div>
                                        <div class="text-xs text-gray-400 font-mono">/{{ $category->slug }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-600">{{ $category->products_count ?? 0 }}</span>
                                    <span class="text-xs text-gray-400">items</span>
                                </div>
                            </td>

                            <td class="px-6 py-6">
                                @if ($category->status === 'active')
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Hidden
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-6">
                                <div class="text-sm text-gray-500">{{ $category->created_at->format('M d, Y') }}</div>
                            </td>

                            <td class="px-8 py-6 text-right">
                                <div class="inline-flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                       title="Edit Category">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? This might affect products linked to it.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                                title="Delete Category">
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
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold font-display text-gray-900 mb-2">No Categories Found</h3>
                                    <p class="text-gray-500 mb-8 max-w-sm mx-auto font-light">Create categories to organize your products effectively.</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn-primary shadow-lg shadow-rose-200/50">
                                        Add First Category
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection