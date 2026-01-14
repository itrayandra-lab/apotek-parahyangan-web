@extends('layouts.app')

@section('title', $product->name . ' - Beautylatory')

@section('styles')
<style>
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
    <div class="pt-32 pb-24 min-h-screen bg-gray-50 relative overflow-hidden"
         x-data="{
            quantity: 1,
            openSection: 'benefits',
            productId: {{ $product->id }},
            stock: {{ $product->stock }},
            addingToCart: false,
            decreaseQty() { this.quantity = Math.max(1, this.quantity - 1); },
            increaseQty() { this.quantity = Math.min(this.stock, this.quantity + 1); },
            async addToCart() {
                this.addingToCart = true;
                try {
                    await axios.post('{{ route('cart.add') }}', {
                        product_id: this.productId,
                        quantity: this.quantity,
                    });
                    this.quantity = 1;
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.showToast?.('Produk ditambahkan ke keranjang.');
                } catch (error) {
                    window.showToast?.(error?.response?.data?.message ?? 'Gagal menambahkan ke keranjang.', 'error');
                } finally {
                    this.addingToCart = false;
                }
            }
         }">
        
        {{-- Background Elements --}}
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/10/40 rounded-full blur-3xl pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-cyan-500/5 rounded-full blur-3xl pointer-events-none translate-y-1/3 -translate-x-1/4"></div>

        <div class="container mx-auto px-6 md:px-8 relative z-10">
            
            {{-- Navigation --}}
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold tracking-widest text-gray-400 hover:text-primary mb-8 transition-colors uppercase group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Shop
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
                
                {{-- Left: Gallery (Sticky) --}}
                <div class="lg:col-span-7">
                    <div class="sticky top-32">
                        <div class="relative aspect-[4/5] md:aspect-square rounded-[3rem] overflow-hidden shadow-2xl shadow-gray-200 border border-white bg-white group">
                            @if($product->hasImage())
                                <img src="{{ $product->getImageUrl() }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-400 font-medium">No Image Available</span>
                                </div>
                            @endif

                            {{-- Top Left Badge --}}
                            <div class="absolute top-6 left-6">
                                <span class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-[10px] font-bold tracking-[0.2em] text-gray-900 uppercase shadow-sm">
                                    {{ $product->category->name ?? 'Beautylatory' }}
                                </span>
                            </div>

                            {{-- Tech Overlay Decorative (Bottom Right) --}}
                            <div class="absolute bottom-6 right-6 glass-panel px-4 py-3 rounded-2xl hidden md:flex items-center gap-3 animate-fade-in-up">
                                <div class="flex -space-x-2">
                                    @foreach(range(1, 3) as $i)
                                        <div class="w-6 h-6 rounded-full bg-primary/10 border-2 border-white flex items-center justify-center text-[8px] text-primary font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                    @endforeach
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Clinically Tested</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Details --}}
                <div class="lg:col-span-5 flex flex-col">
                    <div class="mb-8">
                        <h2 class="text-xs font-bold tracking-[0.2em] text-primary uppercase mb-4">
                            {{ $product->category->name ?? 'Premium Skincare' }}
                        </h2>
                        <h1 class="text-4xl md:text-5xl font-sans font-semibold text-gray-900 leading-tight mb-6">
                            {{ $product->name }}
                        </h1>
                        
                        <div class="flex items-center justify-between border-b border-gray-200 pb-8">
                            <div class="flex items-baseline gap-3">
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <p class="text-3xl font-medium text-primary">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</p>
                                    <p class="text-lg text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-3xl font-medium text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <!-- <div class="flex items-center gap-2">
                                <div class="flex text-primary-light">
                                    @foreach(range(1, 5) as $i)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endforeach
                                </div>
                                <span class="text-xs font-bold text-gray-400 translate-y-[1px]">(48 Reviews)</span>
                            </div> -->
                        </div>
                    </div>

                    <div class="prose prose-sm prose-gray mb-10 max-w-none">
                        <div class="text-gray-600 font-light text-base leading-relaxed whitespace-pre-line">{{ $product->description }}</div>
                    </div>

                    {{-- Action Area --}}
                    <div class="glass-panel p-6 rounded-3xl mb-10 border border-white/60 shadow-lg shadow-rose-100/20">
                        <div class="flex items-center gap-6 mb-6">
                            <div class="flex items-center gap-4 bg-white rounded-full px-4 py-3 border border-gray-100">
                                <button @click="decreaseQty()" class="text-gray-400 hover:text-gray-900 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span class="text-sm font-bold text-gray-900 w-4 text-center" x-text="quantity"></span>
                                <button @click="increaseQty()" class="text-gray-400 hover:text-gray-900 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/></svg>
                                </button>
                            </div>
                            <div class="text-xs text-gray-400 font-medium tracking-wider">
                                IN STOCK • READY TO SHIP
                            </div>
                        </div>
                        
                        <button @click="addToCart()" :disabled="addingToCart"
                                class="w-full bg-gray-900 text-white py-5 rounded-2xl text-xs font-bold tracking-[0.2em] uppercase hover:bg-primary transition-all duration-300 shadow-xl shadow-gray-900/20 flex items-center justify-center gap-3 group disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span x-text="addingToCart ? 'Menambahkan...' : 'Add to Bag'"></span>
                        </button>
                        
                        @if($product->lynk_id_link)
                            <a href="{{ $product->lynk_id_link }}" target="_blank" class="block w-full text-center mt-4 text-xs font-bold tracking-widest uppercase text-gray-500 hover:text-primary transition-colors">
                                Or Buy on Lynk
                            </a>
                        @endif
                    </div>

                    {{-- Accordions --}}
                    <!-- <div class="space-y-4">
                        {{-- Benefits --}}
                        <div class="border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 bg-white">
                            <button @click="openSection = openSection === 'benefits' ? null : 'benefits'" 
                                    class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <span class="text-xs font-bold tracking-widest text-gray-900 uppercase">Key Benefits</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="openSection === 'benefits' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openSection === 'benefits'" x-collapse>
                                <div class="p-5 pt-0 text-sm text-gray-600 font-light">
                                    <ul class="space-y-4">
                                        <li class="flex items-start gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></span>
                                            <div>
                                                <strong class="block text-gray-900 mb-1">Intensive Hydration</strong>
                                                Deep moisture infusion with long-lasting hydration that penetrates multiple skin layers.
                                            </div>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></span>
                                            <div>
                                                <strong class="block text-gray-900 mb-1">Anti-Aging Formula</strong>
                                                Reduces fine lines and wrinkles with potent antioxidants and peptides.
                                            </div>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></span>
                                            <div>
                                                <strong class="block text-gray-900 mb-1">Brightening Effect</strong>
                                                Vitamin C complex illuminates dull skin and evens tone.
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Ingredients --}}
                        <div class="border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 bg-white">
                            <button @click="openSection = openSection === 'ingredients' ? null : 'ingredients'" 
                                    class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <span class="text-xs font-bold tracking-widest text-gray-900 uppercase">Ingredients</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="openSection === 'ingredients' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openSection === 'ingredients'" x-collapse>
                                <div class="p-5 pt-0 text-sm text-gray-600 font-light leading-relaxed space-y-3">
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                        <span>Hyaluronic Acid</span>
                                        <span class="font-bold text-primary">5.0%</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                        <span>Vitamin C</span>
                                        <span class="font-bold text-primary">3.5%</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                        <span>Niacinamide</span>
                                        <span class="font-bold text-primary">4.0%</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-4">Full list: Water, Glycerin, Butylene Glycol, ...</p>
                                </div>
                            </div>
                        </div>

                        {{-- How to Use --}}
                        <div class="border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 bg-white">
                            <button @click="openSection = openSection === 'usage' ? null : 'usage'" 
                                    class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs font-bold tracking-widest text-gray-900 uppercase">How to Use</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="openSection === 'usage' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openSection === 'usage'" x-collapse>
                                <div class="p-5 pt-0 text-sm text-gray-600 font-light leading-relaxed">
                                    <ol class="space-y-4 relative border-l border-gray-200 ml-2">
                                        <li class="ml-6">
                                            <span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 bg-primary/10 rounded-full ring-4 ring-white text-[10px] font-bold text-primary">1</span>
                                            <h4 class="font-bold text-gray-900">Cleanse</h4>
                                            <p class="text-xs mt-1">Gently cleanse face and pat dry.</p>
                                        </li>
                                        <li class="ml-6">
                                            <span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 bg-primary/10 rounded-full ring-4 ring-white text-[10px] font-bold text-primary">2</span>
                                            <h4 class="font-bold text-gray-900">Apply</h4>
                                            <p class="text-xs mt-1">Dispense 2-3 pumps and apply to face.</p>
                                        </li>
                                        <li class="ml-6">
                                            <span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 bg-primary/10 rounded-full ring-4 ring-white text-[10px] font-bold text-primary">3</span>
                                            <h4 class="font-bold text-gray-900">Massage</h4>
                                            <p class="text-xs mt-1">Massage gently until fully absorbed.</p>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>
            </div>
        </div>
    </div>

    <!-- {{-- Reviews Section --}}
    <section class="py-20 border-t border-gray-100 bg-white">
        <div class="container mx-auto px-6 md:px-8">
            <div class="flex justify-between items-end mb-12">
                <div class="space-y-2">
                    <h2 class="text-xs font-bold text-primary tracking-[0.2em] uppercase">Customer Feedback</h2>
                    <h3 class="text-4xl md:text-5xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase">WHAT CUSTOMERS SAY</h3>
                </div>
                <button class="hidden md:block bg-gray-900 text-white px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase hover:bg-primary transition-colors duration-300">
                    Write a Review
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach(['Sarah Mitchell', 'Emily Rodriguez', 'Jessica Chen'] as $name)
                <div class="bg-gray-50 rounded-3xl p-8 hover:shadow-xl hover:shadow-rose-100/30 transition-all duration-300 group">
                    <div class="flex items-center gap-1 mb-4 text-yellow-400">
                        @foreach(range(1,5) as $i) <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endforeach
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6 line-clamp-4 group-hover:text-gray-900 transition-colors">"This serum is absolutely amazing! My skin feels so hydrated and glowing. I've noticed a significant improvement in fine lines within just two weeks. Highly recommend!"</p>
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $name }}</p>
                        <p class="text-xs text-primary font-bold tracking-wider uppercase mt-1">Verified Purchase</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Related Products --}}
    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-6 md:px-8">
            <div class="mb-16 text-center">
                <h2 class="text-xs font-bold text-primary tracking-[0.2em] uppercase mb-2">You May Also Like</h2>
                <h3 class="text-4xl md:text-5xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase">RELATED PRODUCTS</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                @forelse($product->category?->products()->where('id', '!=', $product->id)->take(4)->get() ?? [] as $related)
                    <div class="group">
                        <a href="{{ route('products.show', $related->slug) }}" class="block">
                            <div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-4 glass-panel p-2 transition-all duration-500 group-hover:shadow-xl group-hover:shadow-rose-100/50 group-hover:-translate-y-2">
                                <div class="relative w-full h-full rounded-2xl overflow-hidden">
                                    @if($related->image)
                                        <img src="{{ asset($related->image) }}" alt="{{ $related->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center"><span class="text-gray-400 text-xs">No Image</span></div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <h4 class="text-sm font-display font-medium text-gray-900 group-hover:text-primary transition-colors">{{ $related->name }}</h4>
                                <p class="text-xs font-bold text-gray-500 mt-1">
                                    @if($related->discount_price && $related->discount_price < $related->price)
                                        <span class="line-through mr-2">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                                        <span class="text-primary">Rp {{ number_format($related->discount_price, 0, ',', '.') }}</span>
                                    @else
                                        Rp {{ number_format($related->price, 0, ',', '.') }}
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-400 text-sm">No related products found.</div>
                @endforelse
            </div>
        </div>
    </section> -->
@endsection
