@extends('layouts.app')

@section('title', 'Collection - Beautylatory')

@section('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }

    /* Hide scrollbar for category filter */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@section('content')
    <div class="pt-12 pb-24 min-h-screen bg-gray-50 relative overflow-hidden">
        {{-- Background Elements --}}
        <div class="absolute top-0 left-0 w-full h-[600px] bg-gradient-to-b from-rose-50/50 to-transparent pointer-events-none"></div>
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-rose-100/30 rounded-full blur-3xl pointer-events-none animate-slow-spin"></div>
        <div class="absolute top-[20%] left-[-10%] w-[400px] h-[400px] bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-6 md:px-8 relative z-10" x-data="productLoader()" x-init="init()">


            {{-- Page Header --}}
            <div class="max-w-4xl mx-auto text-center mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-4 block animate-fade-in-up delay-100">Discover Excellence</span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase leading-[1.1] mb-8 animate-fade-in-up delay-200">
                    CURATED COLLECTION
                </h1>
                
                {{-- Search Bar --}}
                <div class="max-w-xl mx-auto mb-10 animate-fade-in-up delay-300">
                    <form action="{{ route('products.index') }}" method="GET" class="relative group">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Search our products..."
                            class="w-full h-16 pl-14 pr-6 rounded-2xl bg-white shadow-xl shadow-rose-100/20 border border-gray-100 focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-gray-700 text-lg">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('products.index', request()->only('category')) }}" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </form>
                </div>

                <p class="text-lg md:text-xl text-gray-600 font-light leading-relaxed max-w-2xl mx-auto animate-fade-in-up delay-[400ms]">
                    Explore our scientifically formulated healthcare products, designed to support your wellbeing through trusted pharmaceutical excellence.
                </p>
            </div>

            {{-- Category Filter Section with Horizontal Scroll --}}
            <div class="mb-16 animate-fade-in-up delay-300" x-data="{ scrollContainer: null }" x-init="scrollContainer = $refs.categoryScroll">
                <div class="relative max-w-5xl mx-auto px-4 md:px-12">
                    {{-- Left Arrow --}}
                    <button @click="scrollContainer?.scrollBy({ left: -300, behavior: 'smooth' })"
                            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full glass-panel hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-300 hidden md:flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    {{-- Category Buttons Container --}}
                    <div x-ref="categoryScroll" class="flex gap-4 overflow-x-auto hide-scrollbar scroll-smooth">
                        <a href="{{ route('products.index', request()->only('search')) }}"
                           class="flex-shrink-0 px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase transition-all duration-300 border whitespace-nowrap
                                  {{ !request()->get('category')
                                      ? 'bg-gray-900 text-white border-gray-900 shadow-lg shadow-gray-900/20 transform scale-105'
                                      : 'bg-white border-gray-200 text-gray-500 hover:border-primary hover:text-primary hover:-translate-y-1' }}">
                            All Products
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', array_merge(request()->only('search'), ['category' => $category->id])) }}"
                               class="flex-shrink-0 px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase transition-all duration-300 border whitespace-nowrap
                                      {{ request()->get('category') == $category->id
                                          ? 'bg-gray-900 text-white border-gray-900 shadow-lg shadow-gray-900/20 transform scale-105'
                                          : 'bg-white border-gray-200 text-gray-500 hover:border-primary hover:text-primary hover:-translate-y-1' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Right Arrow --}}
                    <button @click="scrollContainer?.scrollBy({ left: 300, behavior: 'smooth' })"
                            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full glass-panel hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-300 hidden md:flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Products Grid (with Alpine.js) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                {{-- Server-rendered Initial Products (SEO-friendly) --}}
                @forelse($products as $product)
                    <x-product-card :product="$product" :index="$loop->index" />
                @empty
                    <div class="col-span-full text-center py-24 animate-fade-in-up delay-300">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase mb-2">NO PRODUCTS FOUND</h3>
                        <p class="text-gray-500 font-light mb-8">We couldn't find any products matching your selection.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-primary font-bold tracking-widest uppercase text-xs border-b border-primary pb-1 hover:text-rose-600 hover:border-rose-600 transition-colors">
                            Clear All Filters
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </div>
                @endforelse

                {{-- AJAX-loaded Products Container --}}
                <template x-for="(product, index) in ajaxProducts" :key="product.id">
                    <div x-html="renderProductCard(product, index + initialProductCount)" class="animate-fade-in-up"></div>
                </template>
            </div>

            {{-- Load More Button --}}
            <template x-if="hasMorePages && initialProductCount > 0">
                <div class="text-center pb-12 animate-fade-in-up delay-300">
                    <button @click="loadMore()"
                            :disabled="loading"
                            class="group relative inline-flex items-center justify-center px-12 py-4 overflow-hidden font-bold text-white transition-all duration-300 bg-gray-900 rounded-full hover:bg-primary hover:shadow-lg hover:shadow-primary/30 focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed gap-2">
                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                        <span class="relative flex items-center gap-2 text-xs tracking-widest uppercase">
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="loading ? 'Loading...' : 'Load More Products'"></span>
                        </span>
                    </button>


                </div>
            </template>

            {{-- End of Collection Message --}}
            <template x-if="!hasMorePages && ajaxProducts.length > 0">
                <div class="col-span-full text-center py-12 animate-fade-in-up">
                    <p class="text-gray-400 text-xs font-bold tracking-widest uppercase">You've reached the end of the collection</p>
                </div>
            </template>
        </div>
    </div>

    <script>
        function productLoader() {
            return {
                ajaxProducts: [],
                currentPage: {{ $products->currentPage() }},
                initialProductCount: {{ $products->count() }},
                categoryFilter: '{{ request()->get('category') ?? '' }}',
                searchQuery: '{{ request()->get('search') ?? '' }}',
                loading: false,
                hasMorePages: {{ $products->hasMorePages() ? 'true' : 'false' }},

                init() {
                    // Initialize component - nothing needed for initial load
                },

                /**
                 * Load next page of products via AJAX
                 */
                async loadMore() {
                    if (this.loading || !this.hasMorePages) return;

                    this.loading = true;

                    try {
                        const response = await axios.post('{{ route('products.load-more') }}', {
                            page: this.currentPage + 1,
                            category: this.categoryFilter,
                            search: this.searchQuery
                        });

                        if (response.data.products && response.data.products.length > 0) {
                            // Add new products to reactive array
                            this.ajaxProducts.push(...response.data.products);

                            // Update pagination state
                            this.hasMorePages = response.data.hasMorePages;
                            this.currentPage++;
                        }
                    } catch (error) {
                        console.error('Failed to load more products:', error);
                        window.showToast?.('Failed to load more products. Please try again.', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                /**
                 * Render product card HTML for AJAX-loaded products
                 * @param {Object} product - Product data from AJAX response
                 * @param {number} index - Product index for animation delay
                 * @returns {string} HTML string for product card
                 */
                renderProductCard(product, index) {
                    const discountPercent = product.discount_price && product.discount_price < product.price
                        ? Math.round(((product.price - product.discount_price) / product.price) * 100)
                        : 0;

                    const productUrl = product.url ?? `/shop/${product.slug}`;
                    const formatPrice = (price) => {
                        return new Intl.NumberFormat('id-ID').format(price);
                    };

                    const animationDelay = (index * 100) + 400;

                    // Build image HTML
                    const imageHtml = product.has_image
                        ? `<img src="${product.image_url}"
                                alt="${this.escapeHtml(product.name)}"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">`
                        : `<div class="w-full h-full bg-gray-50 flex items-center justify-center"><span class="text-gray-300 text-xs font-bold tracking-widest uppercase">No Image</span></div>`;

                    return `
                        <a href="${productUrl}" class="block h-full group" style="animation-delay: ${animationDelay}ms">
                            <div class="relative aspect-square rounded-[2rem] overflow-hidden mb-6 glass-panel p-2 transition-all duration-500 group-hover:shadow-2xl group-hover:shadow-rose-100/50 group-hover:-translate-y-2">
                                <div class="relative w-full h-full rounded-[1.5rem] overflow-hidden">
                                    ${imageHtml}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                </div>

                                ${product.category
                                    ? `<div class="absolute top-6 left-6 z-10"><span class="glass-panel px-4 py-2 rounded-full text-[10px] font-bold tracking-[0.2em] text-gray-900 uppercase shadow-sm backdrop-blur-md bg-white/90">${this.escapeHtml(product.category.name)}</span></div>`
                                    : ''
                                }

                                ${discountPercent > 0
                                    ? `<div class="absolute top-6 right-6 z-10"><span class="bg-primary text-white px-3 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase shadow-lg shadow-primary/30">${discountPercent}% OFF</span></div>`
                                    : ''
                                }

                                <div class="absolute bottom-6 left-0 right-0 px-6 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                                    <button class="w-full bg-white/90 backdrop-blur text-gray-900 py-3 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg hover:bg-primary hover:text-white transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <div class="text-center px-2">
                                <h3 class="text-sm font-sans font-medium text-gray-900 mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2">
                                    ${this.escapeHtml(product.name)}
                                </h3>
                                <div class="flex items-center justify-center gap-3">
                                    ${product.discount_price && product.discount_price < product.price
                                        ? `<span class="line-through text-gray-400 text-sm">Rp ${formatPrice(product.price)}</span><span class="text-primary font-semibold text-lg">Rp ${formatPrice(product.discount_price)}</span>`
                                        : `<span class="text-gray-900 font-semibold text-lg">Rp ${formatPrice(product.price)}</span>`
                                    }
                                </div>
                            </div>
                        </a>
                    `;
                },

                /**
                 * Escape HTML special characters to prevent XSS
                 */
                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            };
        }
    </script>
@endsection
