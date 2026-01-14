@extends('admin.layouts.app')

@section('title', 'Articles')

@section('content')
    <div class="section-container section-padding">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                    Articles
                </h1>
                <p class="text-gray-500 font-light text-lg">
                    Kelola konten, berita, dan tips kecantikan Anda.
                </p>
            </div>
            <a href="{{ route('admin.articles.create') }}"
                class="btn-primary inline-flex items-center gap-2 group shadow-rose">
                <span>Create New</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>

        @if (session('success'))
            <div
                class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-medium font-sans">{{ session('success') }}</span>
            </div>
        @endif

        <div class="glass-panel rounded-3xl p-3 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <form method="GET" action="{{ route('admin.articles.index') }}" class="flex flex-col md:flex-row gap-3">

                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari judul atau slug..."
                        class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300">
                </div>

                <div class="relative min-w-[180px]">
                    <select name="status"
                        class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
                        <option value="scheduled" @selected(($filters['status'] ?? '') === 'scheduled')>Scheduled</option>
                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <label
                    class="flex items-center gap-3 px-5 py-3 bg-white/50 rounded-2xl cursor-pointer hover:bg-white transition-all duration-300 select-none border border-transparent hover:border-rose-100">
                    <input type="checkbox" name="mine" value="1" @checked(!empty($filters['mine']))
                        class="w-5 h-5 rounded border-gray-300 text-rose-500 focus:ring-rose-200 transition-colors">
                    <span class="text-gray-600 font-medium">Artikel Saya</span>
                </label>

                <button type="submit"
                    class="px-8 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                    Filter
                </button>

                @if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['mine']))
                    <a href="{{ route('admin.articles.index') }}"
                        class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors"
                        title="Reset Filters">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-white/30 backdrop-blur-sm">
                            <th
                                class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Article Details</th>
                            <th
                                class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Author</th>
                            <th
                                class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Status</th>
                            <th
                                class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Date</th>
                            <th
                                class="px-6 py-6 text-center text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Views</th>
                            <th
                                class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($articles as $article)
                            <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-start gap-5">
                                        <div
                                            class="w-20 h-20 rounded-2xl overflow-hidden shadow-sm bg-gray-100 flex-shrink-0 relative group-hover:shadow-md transition-all duration-300">
                                            @if ($article->hasImage())
                                                <img src="{{ $article->getImageUrl() }}"
                                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                    alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-rose-200">
                                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0 pt-1">
                                            <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="block text-lg font-bold text-gray-900 font-display hover:text-rose-600 transition-colors mb-1 truncate">
                                                {{ $article->title }}
                                            </a>
                                            <div class="text-xs text-gray-400 font-mono mb-2 truncate max-w-xs">
                                                /{{ $article->slug }}</div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($article->categories as $category)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-white border border-gray-100 text-gray-500 shadow-sm">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-6 align-top pt-8">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-100 to-rose-200 flex items-center justify-center text-rose-600 font-bold text-xs shadow-inner">
                                            {{ substr($article->display_author, 0, 1) }}
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700">{{ $article->display_author }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-6 align-top pt-8">
                                    @if ($article->status === 'published')
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="relative flex h-2 w-2">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                            </span>
                                            Published
                                        </span>
                                    @elseif($article->status === 'scheduled')
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                            Scheduled
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-6 align-top pt-8">
                                    <div class="flex flex-col">
                                        @if ($article->published_at)
                                            <span
                                                class="text-sm font-bold text-gray-900">{{ $article->published_at->format('d M Y') }}</span>
                                            <span
                                                class="text-xs text-gray-400 mt-0.5">{{ $article->published_at->format('H:i') }}
                                                WIB</span>
                                        @elseif($article->scheduled_at)
                                            <span
                                                class="text-sm font-bold text-amber-600">{{ $article->scheduled_at->format('d M Y') }}</span>
                                            <span class="text-xs text-amber-600/70 mt-0.5">Will publish at
                                                {{ $article->scheduled_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-gray-400 text-sm">—</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-6 align-top pt-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-bold text-gray-900">{{ number_format($article->views_count) }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">views</span>
                                    </div>
                                </td>

                                <td class="px-8 py-6 align-middle text-right">
                                    <div
                                        class="inline-flex items-center gap-1 opacity-60 group-hover:opacity-100 transition-all duration-300 transform group-hover:translate-x-0 translate-x-4">
                                        <a href="{{ route('admin.articles.edit', $article) }}"
                                            class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                            title="Edit Article">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                                title="Delete Article">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-24 h-24 bg-gradient-to-tr from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold font-display text-gray-900 mb-2">Belum ada artikel
                                        </h3>
                                        <p class="text-gray-500 mb-8 max-w-sm mx-auto font-light">Mulai bagikan inspirasi
                                            kecantikan dengan membuat artikel pertama Anda.</p>
                                        <a href="{{ route('admin.articles.create') }}"
                                            class="btn-primary shadow-lg shadow-rose-200/50">
                                            Create First Article
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
