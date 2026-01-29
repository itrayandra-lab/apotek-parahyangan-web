@extends('layouts.app')

@section('title', $article->title)

@section('content')
    @php
        $readingTime = max(1, (int) ceil(str_word_count(strip_tags($article->body ?? '')) / 200));
        $firstCategory = $article->categories->first();
        $publishedDate = $article->published_at
            ? $article->published_at->format('F j, Y')
            : $article->created_at->format('F j, Y');
    @endphp

    <article class="pb-20 min-h-screen bg-gray-50 relative overflow-hidden" style="padding-top: 180px;">
        {{-- Background Elements --}}
        <div class="absolute top-0 left-0 w-full h-[600px] bg-gradient-to-b from-rose-50/50 to-transparent pointer-events-none"></div>
        <div class="absolute top-[10%] right-[-5%] w-[500px] h-[500px] bg-rose-100/20 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Hero Header --}}
        <div class="container mx-auto px-6 md:px-8 mb-12 relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <a href="{{ route('articles.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-bold tracking-widest text-gray-400 hover:text-primary transition-colors uppercase group">
                        <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke Artikel
                    </a>
                </div>

                <div class="text-center space-y-6">
                    <div
                        class="flex items-center justify-center gap-4 text-[10px] font-bold tracking-[0.2em] text-primary uppercase">
                        <span>{{ $publishedDate }}</span>
                        <span>&bull;</span>
                        <span>{{ $firstCategory?->name ?? 'Journal' }}</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent leading-[1.1]">
                        {{ $article->title }}
                    </h1>

                    <div class="flex flex-wrap items-center justify-center gap-6 pt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Ditulis Oleh</p>
                                <span
                                    class="text-sm font-bold text-gray-900 uppercase tracking-wider">{{ $article->display_author }}</span>
                            </div>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium uppercase tracking-wider">{{ $readingTime }} menit baca</span>
                        </div>
                        <div class="h-8 w-px bg-gray-200"></div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-sm font-medium uppercase tracking-wider">{{ number_format($article->views_count) }} dilihat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="container mx-auto px-6 md:px-8 mb-16 relative z-10">
            <div class="max-w-6xl mx-auto aspect-[21/9] rounded-[3rem] overflow-hidden shadow-2xl shadow-gray-200">
                @if ($article->hasImage())
                    <img src="{{ $article->getImageUrl() }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center">
                         <div class="text-center">
                            <div class="w-20 h-20 rounded-full bg-white/50 backdrop-blur flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold tracking-widest text-rose-400 uppercase">Apotek Parahyangan Suite Journal</p>
                         </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Content Body --}}
        <div class="container mx-auto px-6 md:px-8 relative z-10">
            <div class="max-w-4xl mx-auto">
                {{-- Article Content with Enhanced Typography --}}
                <div class="article-content">
                    <x-rich-text::styles />
                    {!! $article->body !!}
                </div>

                {{-- Tags & Share Section --}}
                <div
                    class="mt-16 pt-10 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2">Tags:</span>
                        @forelse($article->tags as $tag)
                            <span
                                class="px-4 py-2 rounded-full bg-gradient-to-r from-primary/10 to-primary/5 text-primary text-xs font-bold uppercase tracking-wide hover:from-primary/20 hover:to-primary/10 transition-all cursor-pointer">
                                #{{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-sm text-gray-300">No tags</span>
                        @endforelse
                    </div>
                    <button type="button"
                        class="flex items-center gap-2 px-6 py-3 rounded-full glass-panel text-xs font-bold tracking-widest text-gray-900 hover:bg-primary hover:text-white transition-all uppercase"
                        onclick="navigator.clipboard?.writeText('{{ url()->current() }}'); alert('Link copied to clipboard!')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                            </path>
                        </svg>
                        Bagikan Artikel
                    </button>
                </div>
            </div>
        </div>

        {{-- Related Articles --}}
        @if ($relatedArticles->isNotEmpty())
            <div class="container mx-auto px-6 md:px-8 mt-24 relative z-10">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <p class="text-xs font-bold tracking-[0.2em] uppercase text-primary mb-3">Lanjutkan Membaca</p>
                        <h2 class="text-4xl md:text-5xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase">Artikel Terkait</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach ($relatedArticles as $index => $relatedArticle)
                            <x-article-card :article="$relatedArticle" :index="$index" />
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </article>

    <style>
        /* ===================================
           ENHANCED ARTICLE CONTENT TYPOGRAPHY
           =================================== */

        .article-content {
            @apply text-gray-800;
        }

        /* Drop Cap - Elegant First Letter */
        .article-content > p:first-of-type::first-letter {
            @apply float-left text-7xl md:text-8xl font-display font-bold leading-[0.8];
            @apply text-transparent bg-clip-text bg-gradient-to-br from-primary via-primary to-rose-400;
            @apply mr-4 mt-2;
        }

        /* Paragraphs - Larger, More Readable */
        .article-content p {
            @apply text-lg md:text-xl text-gray-700 leading-[1.8] mb-8 font-light;
        }

        /* Headings */
        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            @apply font-display font-bold text-gray-900 tracking-tight;
        }

        .article-content h2 {
            @apply text-3xl md:text-5xl mt-20 mb-10;
            @apply border-l-4 border-primary pl-6;
            @apply bg-gradient-to-r from-rose-50/50 to-transparent py-6 rounded-r-2xl;
        }

        .article-content h3 {
            @apply text-2xl md:text-4xl mt-16 mb-6 text-gray-800;
        }

        .article-content h4 {
            @apply text-xl md:text-3xl mt-12 mb-5 text-gray-700;
        }

        /* Links - Bold & Underlined */
        .article-content a {
            @apply text-primary font-bold no-underline;
            @apply border-b-2 border-primary/40;
            @apply hover:border-primary hover:text-rose-600;
            @apply transition-all duration-300;
        }

        /* Blockquotes - Premium Style */
        .article-content blockquote {
            @apply border-l-[6px] border-primary;
            @apply bg-gradient-to-r from-rose-50 via-rose-50/50 to-transparent;
            @apply py-10 px-10 md:px-14 my-16;
            @apply rounded-r-3xl;
            @apply not-italic font-display;
            @apply text-gray-800 text-2xl md:text-3xl leading-relaxed;
            @apply shadow-lg shadow-rose-100/50;
        }

        .article-content blockquote p {
            @apply text-gray-800 text-2xl md:text-3xl mb-0 font-medium;
        }

        /* Lists - Custom Bullets */
        .article-content ul,
        .article-content ol {
            @apply my-10 pl-8 space-y-5;
        }

        .article-content ul {
            @apply list-none;
        }

        .article-content ul li {
            @apply relative pl-10 text-lg md:text-xl text-gray-700 leading-[1.8] font-light;
        }

        .article-content ul li::before {
            content: '';
            @apply absolute left-0 top-[0.65em] w-4 h-4 rounded-full;
            @apply bg-gradient-to-br from-primary to-rose-400;
        }

        .article-content ol {
            @apply list-decimal list-inside;
        }

        .article-content ol li {
            @apply text-lg md:text-xl text-gray-700 leading-[1.8] font-light;
        }

        .article-content li::marker {
            @apply text-primary font-bold;
        }

        /* Images - Elegant Presentation */
        .article-content img {
            @apply rounded-[2rem] shadow-2xl shadow-gray-200;
            @apply my-16 w-full;
            @apply transition-transform duration-500 hover:scale-[1.02];
        }

        /* Strong/Bold - Emphasized */
        .article-content strong,
        .article-content b {
            @apply font-bold text-gray-900;
        }

        /* Emphasis/Italic */
        .article-content em,
        .article-content i {
            @apply italic text-gray-700;
        }

        /* Code Blocks */
        .article-content pre {
            @apply bg-gray-900 text-gray-100;
            @apply rounded-2xl p-8 my-12;
            @apply overflow-x-auto;
            @apply shadow-2xl;
            @apply text-sm md:text-base;
        }

        .article-content code {
            @apply font-mono;
        }

        .article-content :not(pre) > code {
            @apply bg-rose-50 text-primary px-3 py-1 rounded-lg;
            @apply text-base font-semibold;
        }

        /* Horizontal Rule */
        .article-content hr {
            @apply my-20 border-0 h-px;
            @apply bg-gradient-to-r from-transparent via-gray-300 to-transparent;
        }

        /* Tables - Modern Design */
        .article-content table {
            @apply w-full my-16 border-collapse;
            @apply shadow-lg rounded-2xl overflow-hidden;
        }

        .article-content thead {
            @apply bg-gradient-to-r from-primary/10 to-primary/5;
        }

        .article-content th {
            @apply px-6 py-5 text-left font-bold text-gray-900 uppercase tracking-wider text-sm;
            @apply border-b-2 border-primary/20;
        }

        .article-content td {
            @apply px-6 py-5 text-gray-700 text-base border-b border-gray-100;
        }

        .article-content tbody tr {
            @apply hover:bg-rose-50/30 transition-colors;
        }

        /* Figure & Figcaption */
        .article-content figure {
            @apply my-16;
        }

        .article-content figcaption {
            @apply text-center text-sm text-gray-500 mt-4 italic;
        }
    </style>
@endsection
