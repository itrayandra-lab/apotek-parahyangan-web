<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! seo() !!}

    {{-- Tailwind CSS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
    {{-- <x-rich-text::styles theme="richtextlaravel" data-turbo-track="false" /> --}}
</head>

<body class="antialiased">
    {{-- Header --}}
    @include('components.header', ['forceScrolled' => true])

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-3"></div>

    {{-- Chatbot --}}
    @include('components.floating-chat')

    <script>
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = [
                'px-4', 'py-3', 'rounded-xl', 'shadow-lg', 'border', 'backdrop-blur',
                type === 'error' ? 'bg-rose-50/90 border-rose-100 text-rose-700' : 'bg-emerald-50/90 border-emerald-100 text-emerald-700'
            ].join(' ');
            toast.textContent = message;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-1', 'transition', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        };
    </script>

    @yield('scripts')
</body>

</html>
