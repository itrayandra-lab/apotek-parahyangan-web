@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="section-container section-padding max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                Edit Profile
            </h1>
            <p class="text-gray-500 font-light">
                Update your account information and security settings.
            </p>
        </div>
        
        <a href="{{ route('admin.profile.show') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Back to Profile</span>
        </a>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-bold font-display uppercase tracking-wider text-sm">Please check the form</h3>
            </div>
            <ul class="list-disc list-inside text-sm opacity-80 pl-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-xl font-display font-bold text-gray-900">Basic Information</h2>
            </div>

            <div>
                <label for="username" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Username *</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                    </span>
                    <input type="text" 
                           id="username" 
                           name="username"
                           value="{{ old('username', $user->username) }}"
                           class="w-full pl-12 pr-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400 font-medium"
                           required>
                </div>
                <p class="text-xs text-gray-400 mt-2">This will be your display name and login identifier.</p>
            </div>
        </div>

        <!-- Security -->
        <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Change Password</h2>
                    <p class="text-xs text-gray-500">Leave blank to keep current password.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                           placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Confirm Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400"
                           placeholder="••••••••">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200/60">
            <a href="{{ route('admin.profile.show') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="btn-primary px-8 py-3 flex items-center gap-2 shadow-lg shadow-rose-200 hover:shadow-rose-300">
                <span>Update Profile</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>

    </form>
</div>
@endsection