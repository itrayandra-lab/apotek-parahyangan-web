@php
    $siteName = \App\Models\SiteSetting::getValue('general.site_name', 'Beautylatory');
    $supportEmail = \App\Models\SiteSetting::getValue('contact.support_email', 'support@beautylatory.com');
    $contactPhone = \App\Models\SiteSetting::getValue('contact.phone', '');
    $businessAddress = \App\Models\SiteSetting::getValue('contact.address', '');
    $businessHours = \App\Models\SiteSetting::getValue('contact.business_hours', 'Senin - Jumat: 09:00 - 17:00 WIB');
    $googleMapsEmbed = \App\Models\SiteSetting::getValue('contact.google_maps_embed', '');
    $instagramUrl = \App\Models\SiteSetting::getValue('social_media.instagram_url', '');
    $facebookUrl = \App\Models\SiteSetting::getValue('social_media.facebook_url', '');
    $youtubeUrl = \App\Models\SiteSetting::getValue('social_media.youtube_url', '');
@endphp

@extends('layouts.app')

@section('title', 'Contact - ' . $siteName)

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
    .contact-delay-100 { animation-delay: 0.1s; }
    .contact-delay-200 { animation-delay: 0.2s; }
    .contact-delay-300 { animation-delay: 0.3s; }
    .contact-delay-400 { animation-delay: 0.4s; }
    .contact-delay-500 { animation-delay: 0.5s; }
</style>
@endsection

@section('content')
    <div class="pt-32 pb-24 min-h-screen bg-gray-50 relative overflow-hidden">
        {{-- Background Elements --}}
        <div class="absolute top-0 left-0 w-full h-[600px] bg-gradient-to-b from-rose-50/50 to-transparent pointer-events-none"></div>
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-rose-100/30 rounded-full blur-3xl pointer-events-none animate-slow-spin"></div>
        <div class="absolute top-[20%] left-[-10%] w-[400px] h-[400px] bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-6 md:px-8 relative z-10">


            {{-- Page Header --}}
            <div class="max-w-4xl mx-auto text-center mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-4 block animate-fade-in-up contact-delay-100">Get In Touch</span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-display font-medium bg-gradient-to-r from-[#484A56] via-[#9C6C6D] to-[#7A5657] bg-clip-text text-transparent uppercase leading-[1.1] mb-6 animate-fade-in-up contact-delay-200">
                    CONTACT US
                </h1>
                <p class="text-lg md:text-xl text-gray-600 font-light leading-relaxed max-w-2xl mx-auto animate-fade-in-up contact-delay-300">
                    Have questions or need assistance? Our team is here to help you with anything you need.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 max-w-6xl mx-auto">
                {{-- Contact Form --}}
                <div class="lg:col-span-3 animate-fade-in-up contact-delay-400">
                    <div class="glass-panel p-8 md:p-10 rounded-[2rem]">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-display font-medium text-gray-900">Send a Message</h2>
                                <p class="text-sm text-gray-500">We'll respond within 24 hours</p>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            <div class="space-y-6">
                                {{-- Name & Email Row --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                            Name <span class="text-primary">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                                               class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all @error('name') ring-2 ring-rose-300 @enderror"
                                               placeholder="Your full name" required>
                                        @error('name')
                                            <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                            Email <span class="text-primary">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                               class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all @error('email') ring-2 ring-rose-300 @enderror"
                                               placeholder="email@example.com" required>
                                        @error('email')
                                            <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Subject --}}
                                <div>
                                    <label for="subject" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                        Subject <span class="text-primary">*</span>
                                    </label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                           class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all @error('subject') ring-2 ring-rose-300 @enderror"
                                           placeholder="What is this about?" required>
                                    @error('subject')
                                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Message --}}
                                <div>
                                    <label for="message" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                        Message <span class="text-primary">*</span>
                                    </label>
                                    <textarea id="message" name="message" rows="5"
                                              class="w-full px-5 py-4 bg-gray-50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all resize-none @error('message') ring-2 ring-rose-300 @enderror"
                                              placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit" :disabled="loading"
                                        class="group relative w-full inline-flex items-center justify-center px-8 py-4 overflow-hidden font-bold text-white transition-all duration-300 bg-gray-900 rounded-full hover:bg-primary hover:shadow-lg hover:shadow-primary/30 focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-80 group-hover:h-80 opacity-10"></span>
                                    <span class="relative flex items-center gap-3 text-xs tracking-widest uppercase">
                                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        <span x-text="loading ? 'Sending...' : 'Send Message'"></span>
                                        <svg x-show="!loading" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Contact Info Sidebar --}}
                <div class="lg:col-span-2 space-y-6 animate-fade-in-up contact-delay-500">
                    {{-- Contact Details Card --}}
                    <div class="glass-panel p-8 rounded-[2rem]">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Contact Information</h3>
                        <div class="space-y-6">
                            {{-- Email --}}
                            <a href="mailto:{{ $supportEmail }}" class="flex items-start gap-4 group">
                                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-primary/20 transition-colors">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</p>
                                    <p class="text-gray-900 group-hover:text-primary transition-colors">{{ $supportEmail }}</p>
                                </div>
                            </a>

                            {{-- WhatsApp --}}
                            @if($contactPhone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactPhone) }}" target="_blank" class="flex items-start gap-4 group">
                                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-emerald-200 transition-colors">
                                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">WhatsApp</p>
                                    <p class="text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $contactPhone }}</p>
                                </div>
                            </a>
                            @endif

                            {{-- Address --}}
                            @if($businessAddress)
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Address</p>
                                    <p class="text-gray-900">{{ $businessAddress }}</p>
                                </div>
                            </div>
                            @endif

                            {{-- Business Hours --}}
                            @if($businessHours)
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Business Hours</p>
                                    <p class="text-gray-900">{{ $businessHours }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Social Links Card --}}
                    @if($instagramUrl || $facebookUrl || $youtubeUrl)
                    <div class="glass-panel p-8 rounded-[2rem]">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Follow Us</h3>
                        <div class="flex gap-4">
                            @if($instagramUrl)
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer"
                               class="w-14 h-14 bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 rounded-2xl flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-pink-500/30 transition-all duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            @endif
                            @if($facebookUrl)
                            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"
                               class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            @endif
                            @if($youtubeUrl)
                            <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer"
                               class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center hover:scale-110 hover:shadow-lg hover:shadow-red-600/30 transition-all duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Google Maps Embed --}}
            @if($googleMapsEmbed)
            <div class="mt-16 max-w-6xl mx-auto animate-fade-in-up contact-delay-500">
                <div class="glass-panel p-3 rounded-[2rem]">
                    <div class="aspect-[21/9] rounded-[1.5rem] overflow-hidden">
                        <iframe src="{{ $googleMapsEmbed }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
