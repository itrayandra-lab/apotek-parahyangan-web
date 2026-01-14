@extends('admin.layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Add New Product')

@section('content')
<div class="section-container section-padding">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                {{ isset($product) ? 'Edit Product' : 'New Product' }}
            </h1>
            <p class="text-gray-500 font-light">
                {{ isset($product) ? 'Update product details, pricing, and inventory.' : 'Add a new item to your catalog.' }}
            </p>
        </div>
        
        <a href="{{ route('admin.products.index') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Back to Products</span>
        </a>
    </div>

    @if($errors->any())
        <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-bold font-display uppercase tracking-wider text-sm">Please check the form</h3>
            </div>
            <ul class="list-disc list-inside text-sm opacity-80 pl-8">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div class="lg:col-span-2 space-y-6">
            
            <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    General Information
                </h3>

                <div>
                    <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Product Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $product->name ?? '') }}"
                           class="w-full px-0 py-2 text-2xl md:text-3xl font-display font-medium text-gray-900 bg-transparent border-0 border-b-2 border-gray-100 focus:border-rose-400 focus:ring-0 placeholder-gray-300 transition-colors"
                           placeholder="e.g. Radiance Serum"
                           required>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description *</label>
                    <textarea name="description"
                              id="description"
                              rows="6"
                              class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-600 placeholder-gray-400"
                              placeholder="Describe the benefits, ingredients, and usage..."
                              required>{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Pricing & Inventory
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Regular Price (IDR) *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                            <input type="number"
                                   name="price"
                                   id="price"
                                   value="{{ old('price', $product->price ?? '') }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all font-mono font-medium text-gray-700"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="discount_price" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Discount Price (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                            <input type="number"
                                   name="discount_price"
                                   id="discount_price"
                                   value="{{ old('discount_price', $product->discount_price ?? '') }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all font-mono font-medium text-gray-700"
                                   placeholder="0">
                        </div>
                    </div>

                    <div>
                        <label for="stock" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Stock Quantity *</label>
                        <input type="number"
                               name="stock"
                               id="stock"
                               value="{{ old('stock', $product->stock ?? 0) }}"
                               class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all font-mono font-medium text-gray-700"
                               min="0"
                               required>
                    </div>

                    <div>
                        <label for="weight" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Weight (gram) *</label>
                        <input type="number"
                               name="weight"
                               id="weight"
                               value="{{ old('weight', $product->weight ?? 200) }}"
                               class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all font-mono font-medium text-gray-700"
                               min="1"
                               required>
                        <p class="text-xs text-gray-400 mt-1">Berat produk untuk kalkulasi ongkir</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6 lg:sticky lg:top-8">
            
            <div class="glass-panel rounded-3xl p-6 border-t-4 border-rose-400">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-display text-lg font-medium text-gray-900">Publishing</h3>
                    @php
                        $status = old('status', $product->status ?? 'published');
                    @endphp
                    <div class="w-2 h-2 rounded-full {{ $status === 'published' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300' }}"></div>
                </div>

                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $status === 'draft' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="status" value="draft" {{ $status === 'draft' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300">
                        <span class="ml-3 text-sm font-medium text-gray-700">Draft</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $status === 'published' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="status" value="published" {{ $status === 'published' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300">
                        <span class="ml-3 text-sm font-medium text-gray-700">Published</span>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $status === 'archived' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="status" value="archived" {{ $status === 'archived' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300">
                        <span class="ml-3 text-sm font-medium text-gray-700">Archived</span>
                    </label>
                </div>

                {{-- Featured Toggle --}}
                <div class="border-t border-gray-100 pt-4 mb-4">
                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ old('is_featured', $product->is_featured ?? false) ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Featured Product</span>
                            <p class="text-xs text-gray-400">Tampilkan di section "Check Another Product" homepage</p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full btn-primary group flex items-center justify-center gap-2">
                    <span>{{ isset($product) ? 'Save Changes' : 'Create Product' }}</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>

            <div class="glass-panel rounded-3xl p-6">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Organization</h3>
                
                <div class="mb-4">
                    <label for="category_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Category *</label>
                    <div class="relative">
                        <select name="category_id"
                                id="category_id"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border-transparent focus:bg-white focus:border-rose-300 focus:ring-2 focus:ring-rose-100 text-sm text-gray-700 appearance-none"
                                required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-6">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Product Image</h3>
                
                <div class="relative group">
                    <div class="w-full aspect-square rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-rose-300 transition-colors relative">
                        @if(isset($product) && $product->hasImage())
                            <img id="image-preview" src="{{ $product->getImageUrl() }}" alt="Product" class="w-full h-full object-cover">
                        @else
                            <img id="image-preview" src="" alt="Product" class="w-full h-full object-cover hidden">
                            <div id="image-placeholder" class="text-center p-4">
                                <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-xs text-gray-500">Upload Image</p>
                            </div>
                        @endif
                        
                        <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(event)">
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-2 text-center">Max 2MB, JPEG/PNG/WebP</p>
            </div>

            <div class="glass-panel rounded-3xl p-6">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Lynk id</h3>
                
                <div>
                    <label for="lynk_id_link" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Purchase URL</label>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-2.5 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                        </span>
                        <input type="url"
                               name="lynk_id_link"
                               id="lynk_id_link"
                               value="{{ old('lynk_id_link', $product->lynk_id_link ?? '') }}"
                               class="flex-1 px-4 py-2.5 rounded-r-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 text-sm placeholder-gray-300"
                               placeholder="https://...">
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        preview.src = reader.result;
        preview.classList.remove('hidden');
        if(placeholder) placeholder.classList.add('hidden');
    }
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>
@endsection