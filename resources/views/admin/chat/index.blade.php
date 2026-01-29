@extends('admin.layouts.app')

@section('title', 'Daftar Konsultasi Chat')

@section('content')
<div class="section-container section-padding max-w-7xl mx-auto" 
     x-data="{ 
        lastUpdate: Date.now(),
        async refreshList() {
            // Simplified: just reload the page if we're on the first page and not active in a modal
            // Or better: use fetch to get the updated content
            try {
                const response = await fetch(window.location.href);
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.querySelector('#session-list-container')?.innerHTML;
                if (newList) {
                    document.querySelector('#session-list-container').innerHTML = newList;
                    this.lastUpdate = Date.now();
                }
            } catch (e) {
                console.error('Refresh error:', e);
            }
        }
     }"
     x-init="setInterval(() => refreshList(), 10000)">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                Konsultasi Chat
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Lihat dan balas pesan dari pelanggan secara real-time.
            </p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.chats.index') }}?status=active" class="px-4 py-2 rounded-xl {{ request('status') === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' }} text-xs font-bold uppercase tracking-widest transition-all">
                Aktif
            </a>
            <a href="{{ route('admin.chats.index') }}?status=closed" class="px-4 py-2 rounded-xl {{ request('status') === 'closed' ? 'bg-gray-100 text-gray-600 border border-gray-200' : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' }} text-xs font-bold uppercase tracking-widest transition-all">
                Selesai
            </a>
            <a href="{{ route('admin.chats.index') }}" class="px-4 py-2 rounded-xl {{ !request('status') ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' }} text-xs font-bold uppercase tracking-widest transition-all">
                Semua
            </a>
        </div>
    </div>

    @if($sessions->isEmpty())
        <div class="glass-panel p-20 rounded-3xl text-center animate-fade-in-up">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <h3 class="text-xl font-display font-bold text-gray-900 mb-2 uppercase tracking-wide">Belum Ada Sesi Chat</h3>
            <p class="text-gray-500 max-w-xs mx-auto text-sm">Saat ini belum ada pelanggan yang memulai sesi konsultasi chat.</p>
        </div>
    @else
        <div id="session-list-container">
            <div class="grid grid-cols-1 gap-4 animate-fade-in-up" style="animation-delay: 0.1s;">
                @foreach($sessions as $session)
                    <a href="{{ route('admin.chats.show', $session) }}" class="glass-panel p-6 rounded-3xl flex items-center justify-between group hover:-translate-y-1 transition-all duration-300 border border-gray-100 hover:border-rose-100 hover:shadow-xl hover:shadow-rose-500/5">
                        <div class="flex items-center gap-6">
                            <!-- User Avatar/Icon -->
                            <div class="relative">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 group-hover:from-rose-50 group-hover:to-rose-100 group-hover:text-rose-500 group-hover:border-rose-200 transition-all duration-300">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                @if($session->status === 'active')
                                    <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white"></span>
                                    </span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="font-display font-bold text-gray-900 text-lg">
                                        {{ $session->user ? $session->user->name : 'Guest User ' . substr($session->session_id, 0, 8) }}
                                    </h3>
                                    @if($session->isGuest())
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-gray-100 text-gray-500">Guest</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 line-clamp-1 max-w-md">
                                    @if($session->messages->first())
                                        <span class="font-medium text-gray-900">{{ $session->messages->first()->type === 'user' ? 'Customer:' : ($session->messages->first()->type === 'pharmacist' ? 'You:' : 'Bot:') }}</span>
                                        {{ $session->messages->first()->content }}
                                    @else
                                        <span class="italic">Sesi baru dimulai...</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-right flex flex-col items-end gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                {{ $session->last_activity_at->diffForHumans() }}
                            </span>
                            <div class="flex items-center gap-2">
                                @if($session->unread_messages_count > 0)
                                    <span class="px-2 py-1 rounded-lg bg-rose-500 text-white text-[10px] font-black animate-pulse">
                                        {{ $session->unread_messages_count }} BARU
                                    </span>
                                @endif
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $session->status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $session->status }}
                                </span>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-gray-300 group-hover:bg-rose-500 group-hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            {{ $sessions->links() }}
        </div>
    @endif
</div>
@endsection
