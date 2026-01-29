{{-- Product Card Component --}}
{{-- Props: $product (required), $index (optional, for animation delay) --}}

<a href="{{ route('products.show', $product->slug) }}" class="block h-full group animate-fade-in-up"
   style="{{ isset($index) ? 'animation-delay: ' . (($index * 100) + 400) . 'ms' : '' }}">
    {{-- Product Card Image --}}
    <div class="relative aspect-square rounded-[2rem] overflow-hidden mb-6 glass-panel p-2 transition-all duration-500 group-hover:shadow-2xl group-hover:shadow-rose-100/50 group-hover:-translate-y-2">
        <div class="relative w-full h-full rounded-[1.5rem] overflow-hidden">
            @if ($product->hasImage())
                {{-- Spatie responsive image auto-generates srcset for crisp display --}}
                <div class="w-full h-full [&_img]:w-full [&_img]:h-full [&_img]:object-cover [&_img]:transition-transform [&_img]:duration-700 group-hover:[&_img]:scale-110">
                    {!! $product->getImage() !!}
                </div>
            @else
                <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                    <span class="text-gray-300 text-xs font-bold tracking-widest uppercase">Tanpa Gambar</span>
                </div>
            @endif

            {{-- Overlay Gradient --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </div>

        {{-- Category Badge --}}
        @if ($product->category)
            <div class="absolute top-6 left-6 z-10">
                <span class="glass-panel px-4 py-2 rounded-full text-[10px] font-bold tracking-[0.2em] text-gray-900 uppercase shadow-sm backdrop-blur-md bg-white/90">
                    {{ $product->category->name }}
                </span>
            </div>
        @endif

        {{-- Discount Badge --}}
        @if ($product->discount_price && $product->discount_price < $product->price)
            <div class="absolute top-6 right-6 z-10">
                <span class="bg-primary text-white px-3 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase shadow-lg shadow-primary/30">
                    DISKON {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                </span>
            </div>
        @endif

        {{-- Quick Action --}}
        <div class="absolute bottom-6 left-0 right-0 px-6 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
            <button class="w-full bg-white/90 backdrop-blur text-gray-900 py-3 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg hover:bg-primary hover:text-white transition-colors">
                Lihat Detail
            </button>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="text-center px-2">
        <h3 class="text-sm font-sans font-medium text-gray-900 mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2">
            {{ $product->name }}
        </h3>

        <div class="flex items-center justify-center gap-3">
            @if ($product->discount_price && $product->discount_price < $product->price)
                <span class="line-through text-gray-400 text-sm">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
                <span class="text-primary font-semibold text-lg">
                    Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                </span>
            @else
                <span class="text-gray-900 font-semibold text-lg">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @endif
        </div>
    </div>
</a>
