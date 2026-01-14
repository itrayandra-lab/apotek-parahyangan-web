<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Beautylatory</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-gray-900 antialiased bg-[url('/images/bg-pattern.png')] bg-repeat">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md animate-fade-in-up">
            <img class="mx-auto h-12 w-auto" src="{{ asset('images/asset-logo.png') }}" alt="Beautylatory">
            <h2 class="mt-6 text-center text-3xl font-display font-medium tracking-tight text-gray-900">Welcome Back</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please sign in to continue
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md animate-fade-in-up delay-100">
            <div class="glass-panel py-8 px-4 shadow sm:rounded-3xl sm:px-10">
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100">
                        <div class="flex items-center gap-3 text-red-700 mb-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold">Login Failed</span>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-600 ml-8">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center gap-3 text-green-700">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email_or_username" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Email or Username</label>
                        <div class="mt-2">
                            <input id="email_or_username" name="email_or_username" type="text" autocomplete="username" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50"
                                value="{{ old('email_or_username') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-xl bg-rose-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600 transition-colors duration-200">
                            Sign in
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500 rounded-full">New to Beautylatory?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="flex w-full justify-center rounded-xl bg-white px-3 py-3 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                            Create an account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
