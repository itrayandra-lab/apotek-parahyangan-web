@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="section-container section-padding max-w-7xl mx-auto">
    
    @php
        $adminUser = Auth::guard('admin')->user();
        $isAdmin = $adminUser?->role === 'admin';
        $isContentManager = $adminUser?->role === 'content_manager';
        // Get recent products for admin
        $recentProducts = $isAdmin ? \App\Models\Product::latest()->take(3)->get() : collect();
        // Get recent articles for content manager
        $recentArticles = $isContentManager ? \App\Models\Article::with('category')->latest()->take(3)->get() : collect();
        // Get orders and customers count for admin
        $orderCount = $isAdmin ? \App\Models\Order::count() : 0;
        $customerCount = $isAdmin ? \App\Models\User::where('role', 'user')->count() : 0;
    @endphp

    <!-- Welcome Header -->
    <div class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6 animate-fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium text-gray-900 mb-3 tracking-tight">
                Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-rose-400">{{ $adminUser?->username }}</span>
            </h1>
            <p class="text-gray-500 font-light text-lg">
                {{ $isAdmin ? "Overview of your store's performance & activity." : 'Manage your articles and content strategy.' }}
            </p>
        </div>
        <div class="text-right hidden md:block">
            <p class="text-sm font-bold uppercase tracking-widest text-gray-400">{{ now()->format('l, d F Y') }}</p>
        </div>
    </div>

    @if($isAdmin)
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 animate-fade-in-up" style="animation-delay: 0.1s;">
            
            <!-- Products Card -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-600">
                        Active
                    </span>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Products</h3>
                    <p class="text-4xl font-display font-medium text-gray-900">{{ $productCount }}</p>
                </div>
            </div>

            <!-- Categories Card -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Categories</h3>
                    <p class="text-4xl font-display font-medium text-gray-900">{{ $categoryCount }}</p>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Orders</h3>
                    <p class="text-4xl font-display font-medium text-gray-900">{{ $orderCount }}</p>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Customers</h3>
                    <p class="text-4xl font-display font-medium text-gray-900">{{ $customerCount }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in-up" style="animation-delay: 0.2s;">
        
        <!-- Quick Actions Panel -->
        <div class="glass-panel p-8 rounded-3xl h-full">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Quick Actions</h2>
            </div>

            @if($isAdmin)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.products.create') }}" class="group flex items-center p-4 rounded-2xl bg-white/50 border border-gray-100 hover:bg-white hover:border-rose-200 hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Add Product</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Create new inventory item</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.categories.create') }}" class="group flex items-center p-4 rounded-2xl bg-white/50 border border-gray-100 hover:bg-white hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Add Category</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Organize your catalog</p>
                        </div>
                    </a>
                </div>
            @endif

            @if($isContentManager)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.articles.create') }}" class="group flex items-center p-4 rounded-2xl bg-white/50 border border-gray-100 hover:bg-white hover:border-rose-200 hover:shadow-lg hover:shadow-rose-500/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Write Article</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Publish new content</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.article-categories.index') }}" class="group flex items-center p-4 rounded-2xl bg-white/50 border border-gray-100 hover:bg-white hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Manage Taxonomy</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Article categories</p>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        @if($isAdmin)
        <!-- Recent Products Panel -->
        <div class="glass-panel p-8 rounded-3xl h-full flex flex-col">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">New Arrivals</h2>
            </div>

            <div class="flex-1 space-y-4">
                @forelse($recentProducts as $product)
                    <div class="flex items-center gap-4 p-3 rounded-xl bg-white/50 border border-gray-100 hover:bg-white hover:border-rose-100 hover:shadow-sm transition-all duration-300 group">
                        <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">
                            @if($product->hasImage())
                                <img src="{{ $product->getImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 truncate group-hover:text-rose-500 transition-colors">{{ $product->name }}</h3>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider truncate">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-900 font-mono">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            @if($product->stock < 5)
                                <p class="text-[10px] text-rose-500 font-bold uppercase tracking-wider">Low Stock</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <p>No products available yet.</p>
                        <a href="{{ route('admin.products.create') }}" class="text-rose-500 hover:underline mt-1 inline-block text-xs uppercase tracking-widest font-bold">Add One</a>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('admin.products.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-rose-500 uppercase tracking-widest transition-colors flex items-center justify-center gap-1 group">
                    View All Products
                    <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        @endif

        @if($isContentManager)
        <!-- Recent Articles Panel -->
        <div class="glass-panel p-8 rounded-3xl h-full flex flex-col">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Recent Articles</h2>
            </div>

            <div class="flex-1 space-y-4">
                @forelse($recentArticles as $article)
                    <a href="{{ route('admin.articles.edit', $article) }}" class="flex items-center gap-4 p-3 rounded-xl bg-white/50 border border-gray-100 hover:bg-white hover:border-rose-100 hover:shadow-sm transition-all duration-300 group">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center text-rose-400 flex-shrink-0 border border-rose-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 truncate group-hover:text-rose-500 transition-colors">{{ $article->title }}</h3>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider truncate">
                                {{ $article->category->name ?? 'Uncategorized' }}
                            </p>
                        </div>
                        <div class="text-right">
                            @if($article->status === 'published')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600">Published</span>
                            @elseif($article->status === 'draft')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500">Draft</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600">Scheduled</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <p>No articles yet.</p>
                        <a href="{{ route('admin.articles.create') }}" class="text-rose-500 hover:underline mt-1 inline-block text-xs uppercase tracking-widest font-bold">Write One</a>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('admin.articles.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-rose-500 uppercase tracking-widest transition-colors flex items-center justify-center gap-1 group">
                    View All Articles
                    <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection