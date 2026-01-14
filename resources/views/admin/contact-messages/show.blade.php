@extends('admin.layouts.app')

@section('title', 'Detail Pesan - ' . $contactMessage->subject)

@section('content')
<div class="section-container section-padding">
    <div class="flex flex-col md:flex-row justify-between items-start mb-8 gap-6">
        <div>
            <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-rose-500 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-sm font-medium">Kembali ke Daftar Pesan</span>
            </a>
            <h1 class="text-3xl md:text-4xl font-display font-medium text-gray-900 mb-2">
                Detail Pesan
            </h1>
            <p class="text-gray-500 font-light">
                Diterima {{ $contactMessage->created_at->diffForHumans() }}
            </p>
        </div>

        <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST"
            onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-medium text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Pesan
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-display font-medium text-gray-900 mb-1">{{ $contactMessage->subject }}</h2>
                    <p class="text-sm text-gray-500">{{ $contactMessage->created_at->format('d F Y, H:i') }} WIB</p>
                </div>

                <div class="prose prose-gray max-w-none">
                    <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ $contactMessage->message }}</div>
                </div>

                {{-- Reply Button --}}
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}"
                        class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Balas via Email
                    </a>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Sender Info --}}
            <div class="glass-panel rounded-3xl p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Pengirim</h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama</p>
                        <p class="text-gray-900 font-medium">{{ $contactMessage->name }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <a href="mailto:{{ $contactMessage->email }}" class="text-primary hover:underline">
                            {{ $contactMessage->email }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Meta Info --}}
            <div class="glass-panel rounded-3xl p-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Teknis</h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        @if($contactMessage->is_read)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Sudah Dibaca
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                Baru
                            </span>
                        @endif
                    </div>

                    @if($contactMessage->read_at)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Dibaca pada</p>
                        <p class="text-gray-600 text-sm">{{ $contactMessage->read_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif

                    @if($contactMessage->ip_address)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">IP Address</p>
                        <p class="text-gray-600 text-sm font-mono">{{ $contactMessage->ip_address }}</p>
                    </div>
                    @endif

                    @if($contactMessage->user_agent)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Browser</p>
                        <p class="text-gray-600 text-xs break-all">{{ Str::limit($contactMessage->user_agent, 100) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
