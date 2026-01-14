@extends('admin.layouts.app')

@section('title', 'Site Settings')

@php
    /** @var \Illuminate\Support\Collection $settings */
    $social = $settings['social_media'] ?? collect();
    $contact = $settings['contact'] ?? collect();
    $general = $settings['general'] ?? collect();
@endphp

@section('content')
<div class="section-container section-padding max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                Site Settings
            </h1>
            <p class="text-gray-500 font-light">
                Manage global configuration, social links, and contact info.
            </p>
        </div>
        
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Dashboard</span>
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-panel border-l-4 border-rose-500 text-rose-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.site-settings.update') }}" class="space-y-8">
        @csrf

        <!-- General Settings -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">General Information</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <label for="site_name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Site Name *</label>
                    <input type="text"
                           id="site_name"
                           name="general[site_name]"
                           value="{{ old('general.site_name', $general->firstWhere('key', 'general.site_name')?->value) }}"
                           placeholder="Beautylatory"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                           required>
                    @error('general.site_name')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="tagline" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tagline</label>
                    <input type="text"
                           id="tagline"
                           name="general[tagline]"
                           value="{{ old('general.tagline', $general->firstWhere('key', 'general.tagline')?->value) }}"
                           placeholder="Great skin starts with innovation."
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    @error('general.tagline')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Social Media</h2>
            </div>

            <div class="space-y-6">
                <!-- Instagram -->
                <div class="space-y-3">
                    <label for="instagram_url" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Instagram URL</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-rose-500 transition-colors">
                            <i class="fab fa-instagram text-lg"></i>
                        </span>
                        <input type="url"
                               id="instagram_url"
                               name="social_media[instagram_url]"
                               value="{{ old('social_media.instagram_url', $social->firstWhere('key', 'social_media.instagram_url')?->value) }}"
                               placeholder="https://instagram.com/beautylatory"
                               class="w-full pl-12 pr-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    </div>
                    @error('social_media.instagram_url')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Facebook -->
                <div class="space-y-3">
                    <label for="facebook_url" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Facebook URL</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-rose-500 transition-colors">
                            <i class="fab fa-facebook text-lg"></i>
                        </span>
                        <input type="url"
                               id="facebook_url"
                               name="social_media[facebook_url]"
                               value="{{ old('social_media.facebook_url', $social->firstWhere('key', 'social_media.facebook_url')?->value) }}"
                               placeholder="https://facebook.com/beautylatory"
                               class="w-full pl-12 pr-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    </div>
                    @error('social_media.facebook_url')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube -->
                <div class="space-y-3">
                    <label for="youtube_url" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">YouTube URL</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-rose-500 transition-colors">
                            <i class="fab fa-youtube text-lg"></i>
                        </span>
                        <input type="url"
                               id="youtube_url"
                               name="social_media[youtube_url]"
                               value="{{ old('social_media.youtube_url', $social->firstWhere('key', 'social_media.youtube_url')?->value) }}"
                               placeholder="https://youtube.com/@beautylatory"
                               class="w-full pl-12 pr-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    </div>
                    @error('social_media.youtube_url')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Shipping Settings -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Shipping Settings</h2>
            </div>

            @php
                $shipping = $settings['shipping'] ?? collect();
            @endphp

            <div class="space-y-3">
                <label for="shipping_origin_city" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Origin City (Kota Asal Pengiriman) *</label>
                <input type="text"
                       id="shipping_origin_city"
                       name="shipping[origin_city]"
                       value="{{ old('shipping.origin_city', $shipping->firstWhere('key', 'shipping.origin_city')?->value ?? 'BANDUNG') }}"
                       placeholder="BANDUNG"
                       class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                       required>
                <p class="text-xs text-gray-400">Nama kota asal pengiriman untuk kalkulasi ongkir (contoh: BANDUNG, JAKARTA SELATAN)</p>
                @error('shipping.origin_city')
                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Contact Info -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Contact Information</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <label for="support_email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Support Email *</label>
                    <input type="email"
                           id="support_email"
                           name="contact[support_email]"
                           value="{{ old('contact.support_email', $contact->firstWhere('key', 'contact.support_email')?->value) }}"
                           placeholder="support@beautylatory.com"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                           required>
                    @error('contact.support_email')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="newsletter_email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Newsletter Sender</label>
                    <input type="email"
                           id="newsletter_email"
                           name="contact[newsletter_email]"
                           value="{{ old('contact.newsletter_email', $contact->firstWhere('key', 'contact.newsletter_email')?->value) }}"
                           placeholder="newsletter@beautylatory.com"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    @error('contact.newsletter_email')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <label for="phone" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Phone / WhatsApp</label>
                    <input type="text"
                           id="phone"
                           name="contact[phone]"
                           value="{{ old('contact.phone', $contact->firstWhere('key', 'contact.phone')?->value) }}"
                           placeholder="08xx-xxxx-xxxx"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    @error('contact.phone')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="business_hours" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Business Hours</label>
                    <input type="text"
                           id="business_hours"
                           name="contact[business_hours]"
                           value="{{ old('contact.business_hours', $contact->firstWhere('key', 'contact.business_hours')?->value) }}"
                           placeholder="Senin - Jumat: 09:00 - 17:00 WIB"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                    <p class="text-xs text-gray-400">Jam operasional yang ditampilkan di halaman kontak</p>
                    @error('contact.business_hours')
                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label for="address" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Business Address</label>
                <textarea id="address"
                          name="contact[address]"
                          rows="2"
                          placeholder="Jl. Contoh No. 123, Kota, Provinsi, Kode Pos"
                          class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">{{ old('contact.address', $contact->firstWhere('key', 'contact.address')?->value) }}</textarea>
                <p class="text-xs text-gray-400">Alamat lengkap untuk pengembalian produk</p>
                @error('contact.address')
                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 space-y-3">
                <label for="google_maps_embed" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Google Maps Embed URL</label>
                <input type="url"
                       id="google_maps_embed"
                       name="contact[google_maps_embed]"
                       value="{{ old('contact.google_maps_embed', $contact->firstWhere('key', 'contact.google_maps_embed')?->value) }}"
                       placeholder="https://www.google.com/maps/embed?pb=..."
                       class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400">
                <p class="text-xs text-gray-400">URL embed dari Google Maps (bukan link biasa). Buka Google Maps → Share → Embed a map → Copy src URL</p>
                @error('contact.google_maps_embed')
                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-8 py-3 flex items-center gap-2 shadow-lg shadow-rose-200 hover:shadow-rose-300">
                <span>Save Changes</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection