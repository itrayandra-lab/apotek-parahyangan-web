@extends('admin.layouts.app')

@section('title', isset($category) ? 'Edit Article Category' : 'Add New Article Category')

@section('content')
    <div class="section-container section-padding max-w-3xl mx-auto">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                    {{ isset($category) ? 'Edit Category' : 'New Category' }}
                </h1>
                <p class="text-gray-500 font-light">
                    {{ isset($category) ? 'Update category details.' : 'Create a new classification for your articles.' }}
                </p>
            </div>

            <a href="{{ route('admin.article-categories.index') }}"
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

        <!-- Error Alert -->
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

        @php
            $isEdit = $isEdit ?? false;
        @endphp

        <form
            action="{{ $isEdit ? route('admin.article-categories.update', $category) : route('admin.article-categories.store') }}"
            method="POST" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- Main Form Panel -->
            <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-8">

                <!-- Name Input -->
                <div>
                    <label for="name"
                        class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Category Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}"
                        class="w-full px-0 py-2 text-2xl md:text-3xl font-display font-medium text-gray-900 bg-transparent border-0 border-b-2 border-gray-100 focus:border-rose-400 focus:ring-0 placeholder-gray-300 transition-colors"
                        placeholder="e.g. Beauty Tips" required>
                    <p class="text-xs text-gray-400 mt-2">This name will be displayed in the blog navigation.</p>
                </div>

                <!-- Slug Input -->
                <div>
                    <label for="slug" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Slug
                        (Optional)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug ?? '') }}"
                        class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-600 placeholder-gray-400"
                        placeholder="beauty-tips">
                    <p class="text-xs text-gray-400 mt-2">Leave blank to auto-generate from name.</p>
                </div>

                <!-- Description Input -->
                <div>
                    <label for="description"
                        class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-600 placeholder-gray-400"
                        placeholder="Brief description of this category...">{{ old('description', $category->description ?? '') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-4">
                    <a href="{{ route('admin.article-categories.index') }}"
                        class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary px-8 py-3 flex items-center gap-2">
                        <span>{{ isset($category) ? 'Save Changes' : 'Create Category' }}</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection
