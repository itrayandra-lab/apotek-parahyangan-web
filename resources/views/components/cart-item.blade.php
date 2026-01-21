@props([
    'item',
])

@php
    $isMedicine = (bool) $item->medicine_id;
    $displayName = $isMedicine ? $item->medicine?->name : $item->product?->name;
    $displayCategory = $isMedicine ? $item->medicine?->category?->name : $item->product?->category?->name;
    $displayPrice = $isMedicine ? $item->medicine?->price : ($item->product?->discount_price ?? $item->product?->price);
    $originalPrice = $isMedicine ? null : ($item->product?->discount_price ? $item->product?->price : null);
    $imageUrl = $isMedicine ? $item->medicine?->getImageUrl() : $item->product?->getImageUrl();
    $hasImage = $isMedicine ? $item->medicine?->hasImage() : $item->product?->hasImage();
    $stock = $isMedicine ? $item->medicine?->total_stock_unit : $item->product?->stock;
@endphp

<div class="flex items-start gap-4 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 js-cart-item" 
     data-item-id="{{ $item->id }}"
     data-price="{{ $displayPrice }}"
     data-quantity="{{ $item->quantity }}">


    <!-- Checkbox untuk select item -->
    <div class="flex-shrink-0 pt-2">
        <input type="checkbox" 
               class="item-checkbox w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary" 
               value="{{ $item->id }}"
               id="item-{{ $item->id }}">
    </div>

    <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0">
        @if($hasImage)
            <img src="{{ $imageUrl }}" alt="{{ $displayName }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400 font-bold uppercase tracking-widest text-[8px] text-center p-2">No Image</div>
        @endif
    </div>

    <div class="flex-1 space-y-1">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            {{ $displayCategory ?? ($isMedicine ? 'Farmasi' : 'Beautylatory') }}
        </p>
        <h3 class="text-lg font-display text-gray-900 leading-tight">{{ $displayName ?? 'Produk tidak tersedia' }}</h3>
        <div class="flex items-center gap-3">
            <span class="text-rose-600 font-bold">Rp {{ number_format($displayPrice ?? 0, 0, ',', '.') }}</span>
            @if($originalPrice)
                <span class="text-sm text-gray-400 line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span>
            @endif
        </div>
        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Stok: {{ $stock ?? 0 }}</div>
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
