@props([
    'item',
])

<div class="flex items-start gap-4 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 js-cart-item" data-item-id="{{ $item->id }}">
    <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0">
        @if($item->product?->hasImage())
            <img src="{{ $item->product->getImageUrl() }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
        @endif
    </div>

    <div class="flex-1 space-y-1">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            {{ $item->product?->category?->name ?? 'Beautylatory' }}
        </p>
        <h3 class="text-lg font-display text-gray-900">{{ $item->product?->name ?? 'Produk tidak tersedia' }}</h3>
        <div class="flex items-center gap-3">
            @if($item->product?->hasDiscount())
                <span class="text-primary font-semibold">Rp {{ number_format($item->product->discount_price, 0, ',', '.') }}</span>
                <span class="text-sm text-gray-400 line-through">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
            @else
                <span class="text-primary font-semibold">Rp {{ number_format($item->product?->price ?? 0, 0, ',', '.') }}</span>
            @endif
        </div>
        <div class="text-xs text-gray-500">Stok: {{ $item->product?->stock ?? 0 }}</div>
    </div>

    <div class="flex flex-col items-end gap-3">
        <form method="POST" action="{{ route('cart.items.update', $item) }}" class="flex items-center gap-2 js-cart-update" data-item-id="{{ $item->id }}">
            @csrf
            @method('PATCH')
            <input type="number"
                   name="quantity"
                   value="{{ $item->quantity }}"
                   min="1"
                   class="w-16 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent">
            <button type="submit" class="text-sm text-primary hover:text-primary-dark">Update</button>
        </form>

        <form method="POST" action="{{ route('cart.items.remove', $item) }}" class="js-cart-remove" data-item-id="{{ $item->id }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-error hover:opacity-80">Hapus</button>
        </form>
    </div>
</div>
