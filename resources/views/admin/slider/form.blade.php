@extends('admin.layouts.app')

@section('title', isset($slider) ? 'Edit Slider' : 'Add New Slider')

@section('content')
<div class="section-container section-padding max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                {{ isset($slider) ? 'Edit Slider' : 'New Slider' }}
            </h1>
            <p class="text-gray-500 font-light">
                {{ isset($slider) ? 'Update banner details and image.' : 'Upload a new hero banner for your homepage.' }}
            </p>
        </div>
        
        <a href="{{ route('admin.slider.index') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Back to Gallery</span>
        </a>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-bold font-display uppercase tracking-wider text-sm">Please check the form</h3>
            </div>
            <ul class="list-disc list-inside text-sm opacity-80 pl-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($slider) ? route('admin.slider.update', $slider->id) : route('admin.slider.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8">
        @csrf
        @if (isset($slider))
            @method('PUT')
        @endif

        <!-- Image Upload Area -->
        <div class="glass-panel rounded-3xl p-6 md:p-8">
            <div class="flex justify-between items-center mb-4">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Banner Image *</label>
                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded font-mono">16:9 Aspect Ratio</span>
            </div>

            <div class="relative group">
                <input id="image" name="image" type="file" class="hidden" accept="image/*" onchange="previewImage(event)">
                
                <div class="w-full aspect-video rounded-2xl bg-gray-50 border-2 border-dashed border-gray-300 hover:border-rose-300 hover:bg-rose-50/30 transition-all cursor-pointer overflow-hidden relative flex items-center justify-center" onclick="document.getElementById('image').click()">
                    
                    <!-- Preview Image -->
                    <img id="image-preview" 
                         src="{{ isset($slider) && $slider->hasImage() ? $slider->getImageUrl() : '#' }}" 
                         alt="Preview" 
                         class="absolute inset-0 w-full h-full object-cover {{ isset($slider) && $slider->hasImage() ? '' : 'hidden' }}">
                    
                    <!-- Remove Button (Only visible if image exists) -->
                    <button type="button" 
                            id="remove-image-btn"
                            onclick="removeImage(event)" 
                            class="absolute top-4 right-4 bg-white/90 backdrop-blur text-rose-500 p-2 rounded-xl shadow-lg hover:bg-rose-500 hover:text-white transition-all z-20 {{ isset($slider) && $slider->hasImage() ? '' : 'hidden' }}"
                            title="Remove Image">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    <!-- Overlay for Existing Image -->
                    <div id="image-overlay" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10 pointer-events-none {{ isset($slider) && $slider->hasImage() ? '' : 'hidden' }}">
                        <p class="text-white font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Click to Change
                        </p>
                    </div>

                    <!-- Placeholder (Empty State) -->
                    <div id="upload-placeholder" class="text-center p-6 {{ isset($slider) && $slider->hasImage() ? 'hidden' : '' }}">
                        <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4 text-rose-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Click to upload banner</p>
                        <p class="text-xs text-gray-500 mt-1">SVG, PNG, JPG or GIF (Max 2MB)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata Panel -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6">
            <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Settings</h3>

            <!-- Label -->
            <div>
                <label for="label" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Internal Label</label>
                <input type="text" 
                       name="label" 
                       id="label"
                       value="{{ old('label', $slider->label ?? '') }}"
                       class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                       placeholder="e.g. Christmas Promo 2025">
                <p class="text-[10px] text-gray-400 mt-2">Only visible to admins for organization.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Position -->
                <div>
                    <label for="position" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Display Order *</label>
                    <input type="number" 
                           name="position" 
                           id="position"
                           value="{{ old('position', $slider->position ?? '') }}"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-900"
                           placeholder="1"
                           min="1"
                           required>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Visibility *</label>
                    <div class="relative">
                        <select name="status" 
                                id="status"
                                class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 appearance-none cursor-pointer"
                                required>
                            @php
                                $currentStatus = old('status', $slider->status ?? 'active');
                            @endphp
                            <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ $currentStatus === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-4 flex items-center justify-end gap-4">
            <a href="{{ route('admin.slider.index') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-8 py-3 flex items-center gap-2">
                <span>{{ isset($slider) ? 'Save Changes' : 'Create Slider' }}</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>

    </form>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('upload-placeholder');
                const overlay = document.getElementById('image-overlay');
                const removeBtn = document.getElementById('remove-image-btn');

                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                placeholder.classList.add('hidden');
                overlay.classList.remove('hidden');
                removeBtn.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(event) {
        event.stopPropagation();
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const overlay = document.getElementById('image-overlay');
        const removeBtn = document.getElementById('remove-image-btn');

        input.value = '';
        preview.src = '#';
        preview.classList.add('hidden');
        
        placeholder.classList.remove('hidden');
        overlay.classList.add('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endsection