@extends('layouts.app')

@section('title', 'Articles - Beautylatory')

@section('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }

    /* Hide scrollbar for category filter */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@section('content')
    <div class="pt-12 pb-24 min-h-screen bg-gray-50 relative overflow-hidden">
        {{-- Background Elements --}}
        <div class="absolute top-0 left-0 w-full h-[600px] bg-gradient-to-b from-rose-50/50 to-transparent pointer-events-none"></div>
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-rose-100/30 rounded-full blur-3xl pointer-events-none animate-slow-spin"></div>
        <div class="absolute top-[20%] left-[-10%] w-[400px] h-[400px] bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-6 md:px-8 relative z-10">


            {{-- Page Header --}}
            <div class="max-w-4xl mx-auto text-center mb-12">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-4 block animate-fade-in-up delay-100">Articles</span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase leading-[1.1] mb-6 animate-fade-in-up delay-200">
                    SKIN TALKS
                </h1>
                <p class="text-lg md:text-xl text-gray-600 font-light leading-relaxed max-w-2xl mx-auto animate-fade-in-up delay-300">
                    Discover expert tips, science-backed beauty insights, and the latest trends in skincare
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-2xl mx-auto mb-12 animate-fade-in-up delay-300">
                <form method="GET" action="{{ route('articles.index') }}" class="relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ $searchQuery ?? '' }}"
                               placeholder="Search articles, tips, or topics..."
                               class="block w-full pl-14 pr-32 py-4 bg-white border-0 rounded-full text-gray-900 placeholder-gray-400 shadow-lg shadow-gray-100 focus:ring-2 focus:ring-primary/20 focus:shadow-xl transition-all duration-300">
                        <div class="absolute inset-y-0 right-2 flex items-center">
                            @if(request('search'))
                                <a href="{{ route('articles.index', request('category') ? ['category' => request('category')] : []) }}"
                                   class="mr-2 p-2 text-gray-400 hover:text-gray-600 transition-colors"
                                   title="Clear search">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                            <button type="submit"
                                    class="px-6 py-2 bg-gray-900 hover:bg-primary text-white rounded-full text-xs font-bold tracking-widest uppercase transition-all duration-300">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
                @if(request('search'))
                    <p class="text-center text-sm text-gray-500 mt-4">
                        Showing results for "<span class="font-medium text-gray-700">{{ request('search') }}</span>"
                        <span class="text-gray-400">({{ $articles->total() }} {{ Str::plural('article', $articles->total()) }} found)</span>
                    </p>
                @endif
            </div>

            {{-- Category Filter Section with Horizontal Scroll --}}
            @if($categories->isNotEmpty())
                <div class="mb-16 animate-fade-in-up delay-400" x-data="{ scrollContainer: null }" x-init="scrollContainer = $refs.categoryScroll">
                    <div class="relative max-w-5xl mx-auto px-4 md:px-12">
                        {{-- Left Arrow --}}
                        <button @click="scrollContainer?.scrollBy({ left: -300, behavior: 'smooth' })"
                                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full glass-panel hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-300 hidden md:flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        {{-- Category Buttons Container --}}
                        <div x-ref="categoryScroll" class="flex gap-4 overflow-x-auto hide-scrollbar scroll-smooth">
                            <a href="{{ route('articles.index') }}"
                               class="flex-shrink-0 px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase transition-all duration-300 border whitespace-nowrap
                                      {{ !request('category')
                                          ? 'bg-gray-900 text-white border-gray-900 shadow-lg shadow-gray-900/20 transform scale-105'
                                          : 'bg-white border-gray-200 text-gray-500 hover:border-primary hover:text-primary hover:-translate-y-1' }}">
                                All Articles
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('articles.index', ['category' => $category->slug]) }}"
                                   class="flex-shrink-0 px-8 py-3 rounded-full text-xs font-bold tracking-widest uppercase transition-all duration-300 border whitespace-nowrap
                                          {{ request('category') === $category->slug
                                              ? 'bg-gray-900 text-white border-gray-900 shadow-lg shadow-gray-900/20 transform scale-105'
                                              : 'bg-white border-gray-200 text-gray-500 hover:border-primary hover:text-primary hover:-translate-y-1' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Right Arrow --}}
                        <button @click="scrollContainer?.scrollBy({ left: 300, behavior: 'smooth' })"
                                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full glass-panel hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-300 hidden md:flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Featured Article Hero (Only on main page, not category filter) --}}
            @if($featuredArticle)
                <div class="mb-20 animate-fade-in-up delay-400">
                    <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl shadow-gray-200 group cursor-pointer">
                        <a href="{{ route('articles.show', $featuredArticle->slug) }}" class="block">
                            {{-- Featured Image --}}
                            <div class="relative aspect-[21/9]">
                                @if($featuredArticle->hasImage())
                                    <img src="{{ $featuredArticle->getImageUrl() }}"
                                         alt="{{ $featuredArticle->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[2s]">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20"></div>
                                @endif

                                {{-- Gradient Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>

                                {{-- Featured Badge --}}
                                <div class="absolute top-8 left-8">
                                    <div class="glass-panel-dark px-6 py-3 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-white font-bold text-sm tracking-widest uppercase">Featured</span>
                                    </div>
                                </div>

                                {{-- Categories --}}
                                @if($featuredArticle->categories->isNotEmpty())
                                    <div class="absolute top-8 right-8 flex flex-wrap gap-2 justify-end">
                                        @foreach($featuredArticle->categories->take(2) as $category)
                                            <span class="glass-panel-dark px-4 py-2 text-xs font-bold text-white uppercase tracking-wider">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Content Overlay --}}
                                <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12">
                                    <div class="max-w-4xl">
                                        {{-- Meta Info --}}
                                        <div class="flex items-center gap-4 text-sm text-white/80 mb-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span>{{ $featuredArticle->display_author }}</span>
                                            </div>
                                            <span>•</span>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $featuredArticle->published_at->format('M d, Y') }}</span>
                                            </div>
                                            <span>•</span>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>{{ number_format($featuredArticle->views_count) }} views</span>
                                            </div>
                                        </div>

                                        {{-- Title --}}
                                        <h2 class="text-3xl md:text-5xl lg:text-6xl font-display font-medium text-white mb-6 leading-tight group-hover:text-primary transition-colors">
                                            {{ $featuredArticle->title }}
                                        </h2>

                                        {{-- Excerpt --}}
                                        @if($featuredArticle->excerpt)
                                            <p class="text-lg md:text-xl text-white/90 font-light leading-relaxed mb-8 max-w-3xl line-clamp-2">
                                                {{ $featuredArticle->excerpt }}
                                            </p>
                                        @endif

                                        {{-- Tags --}}
                                        @if($featuredArticle->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-2 mb-6">
                                                @foreach($featuredArticle->tags->take(3) as $tag)
                                                    <span class="text-sm text-white/70 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-full">
                                                        #{{ $tag->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Read More CTA --}}
                                        <div class="inline-flex items-center gap-3 text-white font-bold text-sm tracking-widest uppercase group-hover:gap-5 transition-all">
                                            Read Full Article
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Articles Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16"
                 x-data="articleLoader()"
                 x-init="init()">
                {{-- Server-rendered Initial Articles (SEO-friendly) --}}
                @forelse($articles as $article)
                    <x-article-card :article="$article" :index="$loop->index" />
                @empty
                    <div class="col-span-full text-center py-24 animate-fade-in-up delay-300">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] bg-clip-text text-transparent uppercase mb-2">NO ARTICLES FOUND</h3>
                        <p class="text-gray-500 font-light mb-8">We couldn't find any articles matching your selection.</p>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-primary font-bold tracking-widest uppercase text-xs border-b border-primary pb-1 hover:text-rose-600 hover:border-rose-600 transition-colors">
                            View All Articles
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                @endforelse

                {{-- AJAX-loaded Articles Container --}}
                <template x-for="(article, index) in ajaxArticles" :key="article.id">
                    <div x-html="renderArticleCard(article, index)" class="animate-fade-in-up"></div>
                </template>
            </div>

            {{-- Load More Button --}}
            <template x-if="hasMorePages && initialArticleCount > 0">
                <div class="text-center pb-12 animate-fade-in-up delay-300">
                    <button @click="loadMore()"
                            :disabled="loading"
                            class="group relative inline-flex items-center justify-center px-12 py-4 overflow-hidden font-bold text-white transition-all duration-300 bg-gray-900 rounded-full hover:bg-primary hover:shadow-lg hover:shadow-primary/30 focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed gap-2">
                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-10"></span>
                        <span class="relative flex items-center gap-2 text-xs tracking-widest uppercase">
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="loading ? 'Loading...' : 'Load More Articles'"></span>
                        </span>
                    </button>
                </div>
            </template>

            {{-- End of Articles Message --}}
            <template x-if="!hasMorePages && ajaxArticles.length > 0">
                <div class="col-span-full text-center py-12 animate-fade-in-up">
                    <p class="text-gray-400 text-xs font-bold tracking-widest uppercase">You've reached the end</p>
                </div>
            </template>
        </div>
    </div>

    <script>
        function articleLoader() {
            return {
                ajaxArticles: [],
                currentPage: {{ $articles->currentPage() }},
                initialArticleCount: {{ $articles->count() }},
                categoryFilter: '{{ request('category', '') }}',
                searchQuery: '{{ request('search', '') }}',
                featuredArticleId: {{ $featuredArticle ? $featuredArticle->id : 'null' }},
                loading: false,
                hasMorePages: {{ $articles->hasMorePages() ? 'true' : 'false' }},

                init() {
                    // Initialize component
                },

                async loadMore() {
                    if (this.loading || !this.hasMorePages) return;

                    this.loading = true;

                    try {
                        const response = await axios.post('/articles/load-more', {
                            page: this.currentPage + 1,
                            category: this.categoryFilter,
                            search: this.searchQuery,
                            exclude_id: this.featuredArticleId
                        });

                        if (response.data.html) {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(response.data.html, 'text/html');
                            const articles = doc.querySelectorAll('[data-article]');

                            articles.forEach(articleEl => {
                                this.ajaxArticles.push({
                                    id: articleEl.dataset.id,
                                    html: articleEl.outerHTML
                                });
                            });
                        }

                        this.hasMorePages = response.data.has_more;
                        this.currentPage++;
                    } catch (error) {
                        console.error('Error loading articles:', error);
                        alert('Failed to load more articles. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },

                renderArticleCard(article, index) {
                    return article.html;
                }
            };
        }
    </script>

    <style>
        /* Glass panel dark variant */
        .glass-panel-dark {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
        }
    </style>
@endsection
