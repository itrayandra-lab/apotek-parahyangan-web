@extends('admin.layouts.app')

@section('title', isset($expertQuote) ? 'Edit Expert Quote' : 'Add New Expert Quote')

@section('content')
<div class="section-container section-padding max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                {{ isset($expertQuote) ? 'Edit Quote' : 'New Quote' }}
            </h1>
            <p class="text-gray-500 font-light">
                {{ isset($expertQuote) ? 'Update expert testimonial details.' : 'Add a new expert voice to your homepage.' }}
            </p>
        </div>
        
        <a href="{{ route('admin.expert-quotes.index') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Back to List</span>
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

    <form action="{{ isset($expertQuote) ? route('admin.expert-quotes.update', $expertQuote) : route('admin.expert-quotes.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8">
        @csrf
        @if (isset($expertQuote))
            @method('PUT')
        @endif

        <!-- Quote Content Panel -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">The Quote</h2>
            </div>

            <div>
                <label for="quote" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Testimonial Content *</label>
                <textarea id="quote" 
                          name="quote" 
                          rows="4" 
                          class="w-full px-6 py-4 rounded-2xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-lg font-light text-gray-700 placeholder-gray-300 italic leading-relaxed shadow-sm"
                          placeholder="&ldquo;Write the expert's insight here...&rdquo;"
                          required>{{ old('quote', $expertQuote->quote ?? '') }}</textarea>
            </div>
        </div>

        <!-- Expert Profile Panel -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Expert Profile</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Image Upload (Left Column) -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Photo</label>
                    
                    <div class="relative group w-full aspect-square rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 hover:border-rose-300 transition-colors overflow-hidden cursor-pointer" onclick="document.getElementById('image').click()">
                        <!-- Preview Image -->
                        <img id="image-preview" 
                             src="{{ isset($expertQuote) && $expertQuote->hasImage() ? $expertQuote->getImageUrl() : '#' }}" 
                             alt="Preview" 
                             class="absolute inset-0 w-full h-full object-cover {{ isset($expertQuote) && $expertQuote->hasImage() ? '' : 'hidden' }}">
                        
                        <!-- Placeholder -->
                        <div id="image-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 {{ isset($expertQuote) && $expertQuote->hasImage() ? 'hidden' : '' }}">
                            <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[10px] font-bold uppercase tracking-wide">Upload</span>
                        </div>

                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white text-xs font-bold uppercase tracking-wider">Change</span>
                        </div>

                        <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 text-center">Square ratio recommended.</p>
                </div>

                <!-- Details (Right Column) -->
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <label for="author_name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Full Name *</label>
                        <input type="text" 
                               id="author_name" 
                               name="author_name"
                               value="{{ old('author_name', $expertQuote->author_name ?? '') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-bold text-gray-900 placeholder-gray-400"
                               placeholder="e.g. Dr. Sarah Jenkins"
                               required>
                    </div>

                    <div>
                        <label for="author_title" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Professional Title *</label>
                        <input type="text" 
                               id="author_title" 
                               name="author_title"
                               value="{{ old('author_title', $expertQuote->author_title ?? '') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-600 placeholder-gray-400"
                               placeholder="e.g. Lead Dermatologist"
                               required>
                    </div>

                    <!-- Visibility Toggle -->
                    <div class="pt-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-gray-50/50 cursor-pointer hover:border-emerald-200 hover:bg-emerald-50/30 transition-all">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   class="w-5 h-5 rounded border-gray-300 text-emerald-500 focus:ring-emerald-200 transition-colors"
                                   {{ old('is_active', $expertQuote->is_active ?? true) ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Active Status</span>
                                <span class="block text-xs text-gray-500">Show this quote on the homepage.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.expert-quotes.index') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-8 py-3 flex items-center gap-2 shadow-lg shadow-rose-200 hover:shadow-rose-300">
                <span>{{ isset($expertQuote) ? 'Update Quote' : 'Publish Quote' }}</span>
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
                const placeholder = document.getElementById('image-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection