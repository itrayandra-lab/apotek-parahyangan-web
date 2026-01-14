<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Beautylatory</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-gray-900 antialiased bg-[url('/images/bg-pattern.png')] bg-repeat">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md animate-fade-in-up">
            <img class="mx-auto h-12 w-auto" src="{{ asset('images/asset-logo.png') }}" alt="Beautylatory">
            <h2 class="mt-6 text-center text-3xl font-display font-medium tracking-tight text-gray-900">Create Account</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join Beautylatory today
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
                            <span class="font-bold">Registration Failed</span>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-600 ml-8">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Full Name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" autocomplete="name" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50"
                                value="{{ old('name') }}">
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Username</label>
                        <div class="mt-2">
                            <input id="username" name="username" type="text" autocomplete="username" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50"
                                value="{{ old('username') }}">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Email Address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50"
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="new-password" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Confirm Password</label>
                        <div class="mt-2">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm py-3 px-4 bg-white/50">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-xl bg-rose-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600 transition-colors duration-200">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white px-2 text-gray-500 rounded-full">Already have an account?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('login') }}" class="flex w-full justify-center rounded-xl bg-white px-3 py-3 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-200">
                            Sign in
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
