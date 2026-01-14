@extends('layouts.app')

@section('title', $category->name . ' Articles')

@section('content')
<div class="min-h-screen bg-bg-primary py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm text-text-muted">
            <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('articles.index') }}" class="hover:text-primary">Articles</a>
            <span class="mx-2">/</span>
            <span class="text-text-primary">{{ $category->name }}</span>
        </nav>

        <!-- Category Header -->
        <div class="text-center mb-12">
            @if($category->icon)
                <div class="text-6xl mb-4">{{ $category->icon }}</div>
            @endif
            <h1 class="text-4xl md:text-5xl font-bold text-text-primary mb-4">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                    {{ $category->description }}
                </p>
            @endif
        </div>

        <!-- Categories Filter -->
        @if($categories->isNotEmpty())
            <div class="glass-panel p-6 mb-8">
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('articles.index') }}"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-primary/10 text-text-secondary transition-all">
                        All Articles
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('articles.category', $cat->slug) }}"
                           class="px-4 py-2 rounded-xl transition-all {{ $cat->id === $category->id ? 'bg-primary text-white' : 'bg-white hover:bg-primary/10 text-text-secondary' }}">
                            {{ $cat->name }}
                            <span class="text-xs opacity-75">({{ $cat->articles_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
            @forelse($articles as $index => $article)
                <x-article-card :article="$article" :index="$index" />
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-text-muted text-lg">No articles found in this category.</p>
                    <a href="{{ route('articles.index') }}" class="text-primary hover:text-primary-dark mt-4 inline-block">
                        View all articles
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="flex justify-center">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
