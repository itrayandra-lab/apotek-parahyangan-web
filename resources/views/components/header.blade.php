{{-- Header with Alpine.js for scroll detection and mobile menu --}}
@php
    $forceScrolled = $forceScrolled ?? false;
    $isLoggedIn = Auth::guard('web')->check() || Auth::guard('admin')->check();
    $currentUser = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
@endphp
<div
    x-data="{
        forceScrolled: {{ $forceScrolled ? 'true' : 'false' }},
        isScrolled: {{ $forceScrolled ? 'true' : 'false' }},
        isMobileMenuOpen: false,
        cartCount: 0,
        init() {
            if (this.forceScrolled) {
                this.isScrolled = true;
            } else {
                let scrollHandler = () => { this.isScrolled = window.scrollY > 20 };
                scrollHandler();
                window.addEventListener('scroll', scrollHandler);
                this.$el.addEventListener('destroy', () => window.removeEventListener('scroll', scrollHandler));
            }
            this.fetchCartCount();
            window.addEventListener('cart-updated', () => this.fetchCartCount());
        },
        async fetchCartCount() {
            try {
                const response = await axios.get('{{ route('cart.count') }}');
                this.cartCount = response.data.count ?? 0;
            } catch (error) {
                console.error('Failed to fetch cart count', error);
            }
        }
    }"
    x-init="init()"
>
    <header
        :class="(isScrolled && !isMobileMenuOpen ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100' : 'bg-white border-b border-gray-100') + ' ' + (isScrolled ? 'py-3' : 'py-4') + ' text-gray-900'"
        class="fixed top-0 left-0 w-full z-50 transition-all duration-300"
    >
        <div class="container mx-auto px-6 md:px-8">
            <div class="flex items-center justify-between">

                {{-- Logo --}}
                <a
                    href="{{ url('/') }}"
                    class="flex items-center gap-2 group"
                >
                    <img
                        src="{{ asset('images/Logo-apotek-parahyangan-suite.png') }}"
                        alt="Apotek Parahyangan Suite"
                        class="h-16 md:h-24 w-auto object-contain transition-all duration-500 brightness-100 invert-0"
                    >
                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-8">
                    <a
                        href="{{ url('/') }}"
                        class="text-xs font-medium tracking-widest transition-colors uppercase text-gray-700 hover:text-primary"
                    >
                        Home
                    </a>
                    <a
                        href="{{ route('products.index') }}"
                        class="text-xs font-medium tracking-widest transition-colors uppercase text-gray-700 hover:text-primary"
                    >
                        Shop
                    </a>
                    <a
                        href="{{ route('articles.index') }}"
                        class="text-xs font-medium tracking-widest transition-colors uppercase text-gray-700 hover:text-primary"
                    >
                        Articles
                    </a>
                </nav>

                {{-- Actions --}}
                <div class="flex items-center gap-4">
                    {{-- Search (Desktop) --}}
                    <form action="{{ route('products.index') }}" method="GET" :class="isScrolled ? 'bg-white/50 border-white/60' : 'bg-white/80 border-white'" class="hidden md:flex items-center rounded-full px-4 py-2 transition-all border">
                        <button type="submit" class="text-gray-400 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <input
                            type="text"
                            name="search"
                            placeholder="SEARCH"
                            class="bg-transparent border-none outline-none text-[10px] tracking-widest ml-2 w-24 placeholder:text-gray-400 text-gray-900 font-medium uppercase"
                        >
                    </form>

                    {{-- User Icon --}}
                    @if(!$isLoggedIn)
                    <a href="{{ route('login') }}" class="p-2 hover:bg-primary/10 rounded-full transition-colors group" title="Login">
                        <svg
                            class="w-5 h-5 group-hover:text-primary transition-colors"
                            :class="(isScrolled || isMobileMenuOpen) ? 'text-gray-700' : 'text-white'"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                    @else
                        @if($currentUser->role === 'user')
                        <a href="{{ route('customer.dashboard') }}" class="p-2 hover:bg-primary/10 rounded-full transition-colors group" title="My Account">
                            <svg
                                class="w-5 h-5 group-hover:text-primary transition-colors"
                                :class="(isScrolled || isMobileMenuOpen) ? 'text-gray-700' : 'text-white'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                        @else
                        <a href="{{ route('admin.dashboard') }}" class="p-2 hover:bg-primary/10 rounded-full transition-colors group" title="Admin Dashboard">
                            <svg
                                class="w-5 h-5 group-hover:text-primary transition-colors"
                                :class="(isScrolled || isMobileMenuOpen) ? 'text-gray-700' : 'text-white'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 hover:bg-primary/10 rounded-full transition-colors group" title="Logout">
                                <svg
                                    class="w-5 h-5 group-hover:text-primary transition-colors text-gray-700"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    @endif

                    {{-- Cart Icon --}}
                    <a href="{{ route('cart.index') }}" class="p-2 hover:bg-primary/10 rounded-full transition-colors relative group" title="Keranjang">
                        <svg
                            class="w-5 h-5 group-hover:text-primary transition-colors"
                            :class="(isScrolled || isMobileMenuOpen) ? 'text-gray-700' : 'text-white'"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <template x-if="cartCount > 0">
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full shadow-lg shadow-rose-500/30 px-1">
                                <span x-text="cartCount"></span>
                            </span>
                        </template>
                    </a>

                    {{-- Mobile Menu Toggle --}}
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="lg:hidden p-2 hover:bg-primary/10 rounded-full relative z-50"
                    >
                        <svg
                            x-show="!isMobileMenuOpen"
                            class="w-6 h-6 transition-colors text-gray-900"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg
                            x-show="isMobileMenuOpen"
                            x-cloak
                            class="w-6 h-6 transition-colors text-gray-900"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Menu Overlay --}}
    <div
        x-show="isMobileMenuOpen"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
        @keydown.escape.window="isMobileMenuOpen = false"
        class="fixed inset-0 bg-white/98 backdrop-blur-xl z-40 lg:hidden flex flex-col"
        x-cloak
    >
        {{-- Mobile Search --}}
        <div class="pt-24 px-8 pb-6">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    placeholder="SEARCH PRODUCTS..."
                    class="w-full bg-gray-50 border-b-2 border-gray-200 py-4 pl-4 pr-12 text-lg font-display focus:border-primary focus:outline-none transition-colors placeholder:text-gray-400"
                >
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Mobile Links --}}
        <div class="flex-1 flex flex-col justify-center px-8 gap-6 overflow-y-auto">
            <a
                href="{{ url('/') }}"
                @click="isMobileMenuOpen = false"
                class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors transform translate-x-0 opacity-100"
                x-show="isMobileMenuOpen"
                x-transition:enter="transition ease-out duration-500 delay-100"
                x-transition:enter-start="translate-x-10 opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
            >
                Home
            </a>
            <a
                href="{{ route('products.index') }}"
                @click="isMobileMenuOpen = false"
                class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors"
                x-show="isMobileMenuOpen"
                x-transition:enter="transition ease-out duration-500 delay-200"
                x-transition:enter-start="translate-x-10 opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
            >
                Shop
            </a>
            <a
                href="#phytosync"
                @click="isMobileMenuOpen = false"
                class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors"
                x-show="isMobileMenuOpen"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="translate-x-10 opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
            >
                Collection
            </a>
            @if(!$isLoggedIn)
            <a
                href="{{ route('login') }}"
                @click="isMobileMenuOpen = false"
                class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors"
                x-show="isMobileMenuOpen"
                x-transition:enter="transition ease-out duration-500 delay-600"
                x-transition:enter-start="translate-x-10 opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
            >
                Login
            </a>
            @else
                @if($currentUser->role === 'user')
                <a
                    href="{{ route('customer.dashboard') }}"
                    @click="isMobileMenuOpen = false"
                    class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors"
                    x-show="isMobileMenuOpen"
                    x-transition:enter="transition ease-out duration-500 delay-600"
                    x-transition:enter-start="translate-x-10 opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                >
                    My Account
                </a>
                @else
                <a
                    href="{{ route('admin.dashboard') }}"
                    @click="isMobileMenuOpen = false"
                    class="text-3xl font-display font-medium text-gray-900 hover:text-primary transition-colors"
                    x-show="isMobileMenuOpen"
                    x-transition:enter="transition ease-out duration-500 delay-600"
                    x-transition:enter-start="translate-x-10 opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                >
                    Admin Panel
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button
                        type="submit"
                        class="text-3xl font-display font-medium text-red-500 hover:text-red-600 transition-colors w-full text-left"
                        x-show="isMobileMenuOpen"
                        x-transition:enter="transition ease-out duration-500 delay-700"
                        x-transition:enter-start="translate-x-10 opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                    >
                        Logout
                    </button>
                </form>
            @endif
        </div>
        
        {{-- Mobile Footer Info --}}
        <div class="p-8 border-t border-gray-100">
             <p class="text-xs text-gray-400 uppercase tracking-widest">© 2024 Apotek Parahyangan Suite</p>
        </div>
    </div>
</div>
