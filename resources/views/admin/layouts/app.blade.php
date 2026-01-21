<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Beautylatory</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full font-sans text-gray-900 antialiased bg-gray-50 selection:bg-rose-200 selection:text-rose-900"
    x-data="{ mobileMenuOpen: false }">

    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 lg:hidden"
        @click="mobileMenuOpen = false" style="display: none;"></div>

    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-gray-900 text-white transition-transform duration-300 ease-in-out flex flex-col lg:translate-x-0 lg:fixed lg:inset-y-0 border-r border-white/5 shadow-2xl">

        <div class="flex h-24 shrink-0 items-center px-8 bg-gray-900/50">
            <img src="{{ asset('images/asset-logo-white.png') }}" alt="Beautylatory"
                class="h-8 w-auto opacity-90 hover:opacity-100 transition-opacity">
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1 custom-scrollbar">
            @php
                $adminUser = auth()->user();
                $isAdmin = $adminUser?->role === 'admin';
            @endphp

            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <div class="pt-6 pb-2">
                <p class="px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 font-display">Management
                </p>
            </div>

            @if ($isAdmin)
                <a href="{{ route('admin.slider.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.slider.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.slider.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Sliders
                </a>

                <a href="{{ route('admin.expert-quotes.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.expert-quotes.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.expert-quotes.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Expert Quote
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.orders.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Orders
                </a>

                <a href="{{ route('admin.prescriptions.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.prescriptions.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.prescriptions.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Prescriptions
                </a>

                <a href="{{ route('admin.vouchers.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.vouchers.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.vouchers.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Vouchers
                </a>
            @endif

            @if ($isAdmin)
                <div x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.medicines.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full group flex items-center justify-between px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.medicines.*') || request()->routeIs('admin.categories.*') ? 'text-white bg-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.medicines.*') || request()->routeIs('admin.categories.*') ? 'text-white' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </div>
                        <svg class="h-4 w-4 transition-transform duration-300 text-gray-500"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="space-y-1 pl-12 pr-4 pt-1">
                        <a href="{{ route('admin.products.index') }}"
                            class="block py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.products.*') ? 'text-rose-400' : 'text-gray-500 hover:text-white' }}">
                            All Products
                        </a>
                        <a href="{{ route('admin.medicines.index') }}"
                            class="block py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.medicines.*') ? 'text-rose-400' : 'text-gray-500 hover:text-white' }}">
                            Pharmacy (Medicines)
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="block py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.categories.*') ? 'text-rose-400' : 'text-gray-500 hover:text-white' }}">
                            Categories
                        </a>
                    </div>
                </div>
            @endif

            <div x-data="{ open: {{ request()->routeIs('admin.articles.*') || request()->routeIs('admin.article-categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full group flex items-center justify-between px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.articles.*') || request()->routeIs('admin.article-categories.*') ? 'text-white bg-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.articles.*') || request()->routeIs('admin.article-categories.*') ? 'text-white' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        Articles
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-300 text-gray-500"
                        :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-collapse class="space-y-1 pl-12 pr-4 pt-1">
                    <a href="{{ route('admin.articles.index') }}"
                        class="block py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.articles.*') ? 'text-rose-400' : 'text-gray-500 hover:text-white' }}">
                        All Articles
                    </a>
                    <a href="{{ route('admin.article-categories.index') }}"
                        class="block py-2.5 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.article-categories.*') ? 'text-rose-400' : 'text-gray-500 hover:text-white' }}">
                        Categories
                    </a>
                </div>
            </div>

            @if ($isAdmin)
                <a href="{{ route('admin.users.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.users.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>
            @endif

            <div class="pt-6 pb-2">
                <p class="px-4 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 font-display">System</p>
            </div>

            @if ($isAdmin)
                @php
                    $unreadContactCount = \App\Models\ContactMessage::unread()->count();
                @endphp
                <a href="{{ route('admin.contact-messages.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.contact-messages.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.contact-messages.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contact Messages
                    @if($unreadContactCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-rose-500 text-white">
                            {{ $unreadContactCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.chatbot.settings') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.chatbot.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.chatbot.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Chatbot Settings
                </a>

                <a href="{{ route('admin.site-settings.index') }}"
                    class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.site-settings.*') ? 'bg-gradient-to-r from-rose-500/10 to-transparent text-rose-400 border-l-2 border-rose-500' : 'text-gray-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.site-settings.*') ? 'text-rose-400' : 'text-gray-500 group-hover:text-white' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Site Settings
                </a>
            @endif

        </nav>

        <div class="border-t border-white/5 p-4 bg-gray-900/50 backdrop-blur">
            <div class="flex items-center justify-between px-2 mb-4">
                <a href="{{ route('admin.profile.show') }}" class="flex items-center group">
                    <div
                        class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 border border-white/10 flex items-center justify-center text-white font-bold font-display group-hover:from-rose-500 group-hover:to-rose-600 transition-all shadow-lg">
                        {{ substr(auth()->user()->username ?? 'A', 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white group-hover:text-rose-300 transition-colors">
                            {{ auth()->user()->username }}</p>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 group-hover:text-gray-400">View
                            Profile</p>
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center justify-center px-3 py-2.5 text-xs font-bold uppercase tracking-wide text-gray-400 bg-white/5 border border-white/5 rounded-xl hover:bg-white/10 hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Site
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-3 py-2.5 text-xs font-bold uppercase tracking-wide text-white bg-rose-600 rounded-xl hover:bg-rose-500 hover:shadow-lg hover:shadow-rose-500/20 transition-all">
                        <svg class="w-3.5 h-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72 flex flex-col min-h-screen transition-all duration-300 bg-gray-50">

        <div
            class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:hidden">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:text-rose-600 transition-colors"
                @click="mobileMenuOpen = true">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                </div>
                <div class="flex flex-1 items-center justify-end gap-x-4 lg:gap-x-6">
                    <span class="text-sm font-bold font-display tracking-wider text-gray-900 uppercase">Admin
                        Panel</span>
                </div>
            </div>
        </div>

        <main class="flex-1 py-8 md:py-10">
            <div class="px-4 sm:px-6 lg:px-10">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
