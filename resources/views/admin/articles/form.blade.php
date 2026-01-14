@extends('admin.layouts.app')

@section('title', $isEdit ? 'Edit Article' : 'Create Article')


@section('content')
    <div class="section-container section-padding">

        <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                    {{ $isEdit ? 'Edit Article' : 'Write Story' }}
                </h1>
                <p class="text-gray-500 font-light">
                    {{ $isEdit ? 'Perbarui detail dan konten artikel Anda.' : 'Tuangkan ide dan inspirasi kecantikan Anda di sini.' }}
                </p>
            </div>

            <a href="{{ route('admin.articles.index') }}"
                class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
                <div
                    class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest">Back to List</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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

        <form action="{{ $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
            method="POST" enctype="multipart/form-data" class="relative grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="lg:col-span-2 space-y-6">

                <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6">
                    <div>
                        <label for="title"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Article
                            Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}"
                            placeholder="Enter a catchy headline..."
                            class="w-full px-0 py-2 text-2xl md:text-3xl font-display font-medium text-gray-900 bg-transparent border-0 border-b-2 border-gray-100 focus:border-rose-400 focus:ring-0 placeholder-gray-300 transition-colors"
                            required>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="slug" class="text-xs font-bold text-gray-400 uppercase tracking-widest">URL
                                Slug</label>
                            <span class="text-[10px] text-gray-400 font-mono">Auto-generated if empty</span>
                        </div>
                        <div
                            class="flex items-center text-gray-400 bg-gray-50/50 rounded-xl px-4 border border-gray-100 focus-within:ring-2 focus-within:ring-rose-100 transition-all">
                            <span
                                class="text-sm font-mono flex-shrink-0 text-gray-500">{{ config('app.url') }}/articles/</span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug) }}"
                                class="w-full px-2 py-3 text-sm text-gray-600 bg-transparent border-none focus:ring-0 placeholder-gray-300 font-mono"
                                placeholder="your-article-slug">
                        </div>
                    </div>

                    <div>
                        <label for="excerpt"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Excerpt /
                            Summary</label>
                        <textarea name="excerpt" id="excerpt" rows="3"
                            class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-600 placeholder-gray-400"
                            placeholder="Brief description for SEO and article lists...">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>
                </div>

                <x-trix-input
                    id="body"
                    name="body"
                    :value="old('body', $article->body?->toTrixHtml())"
                    placeholder="Start writing your beautiful story..."
                />

            </div>

            <div class="space-y-6 lg:sticky lg:top-8">

                <div class="glass-panel rounded-3xl p-6 border-t-4 border-rose-400">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-display text-lg font-medium text-gray-900">Publishing</h3>
                        <div
                            class="w-2 h-2 rounded-full {{ $article->status === 'published' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300' }}">
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <label
                            class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ old('status', $article->status) === 'draft' ? 'bg-rose-50 border-rose-200' : '' }}">
                            <input type="radio" name="status" value="draft"
                                {{ old('status', $article->status) === 'draft' ? 'checked' : '' }}
                                class="text-rose-500 focus:ring-rose-300 border-gray-300">
                            <span class="ml-3 text-sm font-medium text-gray-700">Draft</span>
                        </label>

                        <label
                            class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ old('status', $article->status) === 'published' ? 'bg-rose-50 border-rose-200' : '' }}">
                            <input type="radio" name="status" value="published"
                                {{ old('status', $article->status) === 'published' ? 'checked' : '' }}
                                class="text-rose-500 focus:ring-rose-300 border-gray-300">
                            <span class="ml-3 text-sm font-medium text-gray-700">Publish Immediately</span>
                        </label>

                        <label
                            class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ old('status', $article->status) === 'scheduled' ? 'bg-rose-50 border-rose-200' : '' }}">
                            <input type="radio" name="status" value="scheduled"
                                {{ old('status', $article->status) === 'scheduled' ? 'checked' : '' }}
                                class="text-rose-500 focus:ring-rose-300 border-gray-300">
                            <span class="ml-3 text-sm font-medium text-gray-700">Schedule</span>
                        </label>
                    </div>

                    <div id="scheduled-date-container" style="display: none;" class="mb-6 animate-fade-in-up">
                        <label for="scheduled_at"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Schedule Time
                            (WIB)</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                            value="{{ old('scheduled_at', $article->scheduled_at_for_form) }}"
                            class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 text-sm"
                            min="{{ now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i') }}">
                    </div>

                    <button type="submit" class="w-full btn-primary group flex items-center justify-center gap-2">
                        <span>{{ $isEdit ? 'Save Changes' : 'Create Article' }}</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>

                <div class="glass-panel rounded-3xl p-6">
                    <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Cover Image</h3>

                    <div class="relative group">
                        <div
                            class="w-full aspect-video rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:border-rose-300 transition-colors">
                            @if ($article->hasImage())
                                <img id="thumbnail-preview" src="{{ $article->getImageUrl() }}" alt="Thumbnail"
                                    class="w-full h-full object-cover">
                            @else
                                <img id="thumbnail-preview" src="" alt="Thumbnail"
                                    class="w-full h-full object-cover hidden">
                                <div id="thumbnail-placeholder" class="text-center p-4">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor"
                                        fill="none" viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="text-xs text-gray-500">Click to upload image</p>
                                </div>
                            @endif

                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                onchange="previewImage(event)">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 text-center">Recommended: 1200x630px (Max 2MB)</p>
                </div>

                <div class="glass-panel rounded-3xl p-6">
                    <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Organization</h3>

                    <div class="mb-6">
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Categories</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-2">
                            @foreach ($categories as $category)
                                <label
                                    class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', $article->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-rose-500 focus:ring-rose-200">
                                    <span class="ml-2 text-sm text-gray-600">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="tags"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tags</label>
                        <div id="tags-container" class="flex flex-wrap gap-2 mb-3"></div>
                        <input type="text" id="tag-input" placeholder="Add tag and press Enter"
                            class="w-full px-4 py-2 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 text-sm">
                    </div>
                </div>

                <div class="glass-panel rounded-3xl p-6">
                    <h3 class="font-display text-lg font-medium text-gray-900 mb-4">Attribution</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Author
                                Account</label>
                            <select name="author_id"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border-transparent text-sm text-gray-600 cursor-not-allowed"
                                readonly>
                                <option value="{{ auth()->id() }}" selected>{{ auth()->user()->name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Display
                                Name (Optional)</label>
                            <input type="text" name="display_author_name"
                                value="{{ old('display_author_name', $article->display_author_name ?? '') }}"
                                class="w-full px-4 py-2.5 rounded-xl bg-white border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 text-sm"
                                placeholder="e.g. Beauty Editor">
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        // --- Status & Date Logic ---
        const statusRadios = document.querySelectorAll('input[name="status"]');
        const dateContainer = document.getElementById('scheduled-date-container');

        function toggleDateContainer() {
            const selected = document.querySelector('input[name="status"]:checked')?.value;
            dateContainer.style.display = selected === 'scheduled' ? 'block' : 'none';
        }

        statusRadios.forEach(radio => radio.addEventListener('change', toggleDateContainer));
        toggleDateContainer(); // Initial check

        // --- Image Preview Logic ---
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('thumbnail-preview');
                const placeholder = document.getElementById('thumbnail-placeholder');
                preview.src = reader.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // --- Tags Logic (Updated Styling) ---
        const tagsContainer = document.getElementById('tags-container');
        const tagInput = document.getElementById('tag-input');
        let tags = @json(old('tags', $article->tags->pluck('name')->toArray()));

        function renderTags() {
            tagsContainer.innerHTML = '';
            tags.forEach((tag, index) => {
                const tagEl = document.createElement('div');
                // Updated styling for tags: Rose pill style
                tagEl.className =
                    'inline-flex items-center gap-1 bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-rose-100 animate-fade-in-up';
                tagEl.innerHTML = `
            <span>#${tag}</span>
            <button type="button" onclick="removeTag(${index})" class="ml-1 w-4 h-4 flex items-center justify-center rounded-full hover:bg-rose-200 text-rose-400 hover:text-rose-800 transition-colors">×</button>
            <input type="hidden" name="tags[]" value="${tag}">
        `;
                tagsContainer.appendChild(tagEl);
            });
        }

        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }

        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim();
                if (value && !tags.includes(value)) {
                    tags.push(value);
                    renderTags();
                    this.value = '';
                }
            }
        });

        renderTags();
    </script>
@endsection
