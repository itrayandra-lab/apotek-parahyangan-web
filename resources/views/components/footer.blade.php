@php
    $siteName = \App\Models\SiteSetting::getValue('general.site_name', 'Apotek Parahyangan Suite');
    $address = \App\Models\SiteSetting::getValue('contact.address', '');
    $instagramUrl = \App\Models\SiteSetting::getValue('social_media.instagram_url', '');
    $facebookUrl = \App\Models\SiteSetting::getValue('social_media.facebook_url', '');
    $youtubeUrl = \App\Models\SiteSetting::getValue('social_media.youtube_url', '');
    $supportEmail = \App\Models\SiteSetting::getValue('contact.support_email', 'support@Apotek Parahyangan Suite.com');
    $newsletterEmail = \App\Models\SiteSetting::getValue('contact.newsletter_email', 'newsletter@Apotek Parahyangan Suite.com');
@endphp
<footer
    class="bg-gradient-to-r from-[#484A56] via-[#9C6C6D] via-[#B58687] to-[#7A5657] text-white pt-24 pb-12 border-t border-white/10">
    <div class="container mx-auto px-6 md:px-8">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-20">
            {{-- About --}}
            <div class="space-y-8">
                <img
                    src="{{ asset('images/Logo-apotek-parahyangan-suite.png') }}"
                    alt="{{ $siteName }}"
                    class="h-20 w-auto object-contain brightness-0 invert"
                >
                @if(! empty($address))
                    <p class="text-sm text-white/90 leading-relaxed font-light">
                        {{ $address }}
                    </p>
                @endif
            </div>

            {{-- Links --}}
            <div class="space-y-8">
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">Information</h4>
                <ul class="space-y-4 text-sm text-white/80">
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('refund') }}" class="hover:text-white transition-colors">Kebijakan Pengembalian</a></li>
                    <li><a href="{{ route('delivery') }}" class="hover:text-white transition-colors">Kebijakan Pengiriman</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            {{-- Socials --}}
            <div class="space-y-8">
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">Connect</h4>
                <div class="flex gap-4">
                    @if(! empty($instagramUrl))
                        <a href="{{ $instagramUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors group">
                            {{-- Instagram --}}
                            <svg class="w-5 h-5 text-white group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    @endif

                    @if(! empty($facebookUrl))
                        <a href="{{ $facebookUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors group">
                            {{-- Facebook --}}
                            <svg class="w-5 h-5 text-white group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif

                    @if(! empty($youtubeUrl))
                        <a href="{{ $youtubeUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors group">
                            {{-- YouTube --}}
                            <svg class="w-5 h-5 text-white group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Newsletter --}}
            <div class="space-y-8">
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">The Newsletter</h4>
                <p class="text-xs text-white/80">Join our list for scientific insights and exclusive offers.</p>
                <div class="relative">
                    <input type="email" placeholder="EMAIL ADDRESS"
                        class="w-full bg-transparent border-b border-white/30 py-4 text-xs tracking-wide text-white focus:outline-none focus:border-white transition-colors placeholder:text-white/50">
                    <button class="absolute right-0 top-3 text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div
            class="pt-8 border-t border-white/20 flex flex-col md:flex-row justify-center items-center gap-4 text-[10px] tracking-widest uppercase text-white/70">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </div>
    </div>
</footer>
