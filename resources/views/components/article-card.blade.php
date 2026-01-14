@props(['article', 'index' => 0])

<article class="animate-fade-in-up"
         style="animation-delay: {{ $index * 100 }}ms"
         data-article
         data-id="{{ $article->id }}">
    <a href="{{ route('articles.show', $article->slug) }}" class="block h-full group">
        {{-- Image Container --}}
        <div class="relative aspect-square rounded-[2rem] overflow-hidden mb-6 glass-panel p-2 transition-all duration-500 group-hover:shadow-2xl group-hover:shadow-rose-100/50 group-hover:-translate-y-2">
            <div class="relative w-full h-full rounded-[1.5rem] overflow-hidden">
                {{-- Thumbnail --}}
                @if($article->hasImage())
                    <img src="{{ $article->getImageUrl() }}"
                         alt="{{ $article->title }}"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center">
                        <svg class="w-12 h-12 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                @endif

                {{-- Overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                {{-- Category Badge --}}
                @if($article->categories->isNotEmpty())
                    <div class="absolute top-6 left-6 z-10">
                        <span class="glass-panel px-4 py-2 rounded-full text-[10px] font-bold tracking-[0.2em] text-gray-900 uppercase shadow-sm backdrop-blur-md bg-white/90">
                            {{ $article->categories->first()->name }}
                        </span>
                    </div>
                @endif

                {{-- Quick Read CTA (appears on hover) --}}
                <div class="absolute bottom-6 left-0 right-0 px-6 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                    <button class="w-full bg-white/90 backdrop-blur text-gray-900 py-3 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg hover:bg-primary hover:text-white transition-colors">
                        Read Article
                    </button>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="text-center px-2">
            {{-- Title --}}
            <h3 class="text-sm font-sans font-medium text-gray-900 mb-2 group-hover:text-primary transition-colors duration-300 line-clamp-2 uppercase tracking-wide">
                {{ $article->title }}
            </h3>
            
            {{-- Excerpt --}}
            @if($article->excerpt)
                <p class="text-gray-500 text-[11px] font-light leading-relaxed line-clamp-2 mt-2">
                    {{ $article->excerpt }}
                </p>
            @endif
        </div>
    </a>
</article>
