@extends('admin.layouts.app')

@section('title', $article->title)

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-text-primary">{{ $article->title }}</h1>
            <p class="text-text-secondary mt-1">Published {{ $article->published_at?->diffForHumans() ?? 'Not published' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.articles.edit', $article) }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl transition-colors">
                Edit
            </a>
            <a href="{{ route('admin.articles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-text-primary px-4 py-2 rounded-xl transition-colors">
                Back
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Meta Info -->
        <div class="bg-white rounded-2xl shadow-sm p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <div class="text-xs text-text-muted uppercase">Status</div>
                <div class="mt-1">
                    @if($article->status === 'published')
                        <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-success/10 text-success">Published</span>
                    @elseif($article->status === 'scheduled')
                        <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-warning/10 text-warning">Scheduled</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-text-muted">Draft</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-xs text-text-muted uppercase">Author</div>
                <div class="mt-1 text-sm text-text-primary">{{ $article->display_author }}</div>
            </div>
            <div>
                <div class="text-xs text-text-muted uppercase">Views</div>
                <div class="mt-1 text-sm text-text-primary">{{ number_format($article->views_count) }}</div>
            </div>
            <div>
                <div class="text-xs text-text-muted uppercase">Created</div>
                <div class="mt-1 text-sm text-text-primary">{{ $article->created_at->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Thumbnail -->
        @if($article->hasImage())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-3">Thumbnail</h2>
                <img src="{{ $article->getImageUrl() }}" alt="{{ $article->title }}" class="w-full h-64 object-cover rounded-xl">
            </div>
        @endif

        <!-- Excerpt -->
        @if($article->excerpt)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-3">Excerpt</h2>
                <p class="text-text-secondary">{{ $article->excerpt }}</p>
            </div>
        @endif

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-text-primary mb-3">Content</h2>
            <div class="prose prose-lg max-w-none prose-headings:text-text-primary prose-p:text-text-secondary prose-strong:text-text-primary prose-em:text-text-secondary prose-a:text-primary hover:prose-a:text-primary-dark prose-ul:text-text-secondary prose-ol:text-text-secondary">
                <x-rich-text::styles />
                @include('rich-text-laravel::content', ['content' => $article->body])
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-text-primary mb-3">Categories</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($article->categories as $category)
                    <span class="px-3 py-1 text-sm rounded-lg bg-secondary/10 text-secondary">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Tags -->
        @if($article->tags->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-3">Tags</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 text-sm rounded-lg bg-primary/10 text-primary">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
