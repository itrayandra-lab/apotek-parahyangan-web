@extends('admin.layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Add New User')

@section('content')
<div class="section-container section-padding">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                {{ isset($user) ? 'Edit User' : 'New User' }}
            </h1>
            <p class="text-gray-500 font-light">
                {{ isset($user) ? 'Update user details and permissions.' : 'Create a new user account.' }}
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
            <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Back to Users</span>
        </a>
    </div>

    @if($errors->any())
        <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="font-bold font-display uppercase tracking-wider text-sm">Please check the form</h3>
            </div>
            <ul class="list-disc list-inside text-sm opacity-80 pl-8">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
          method="POST"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="lg:col-span-2 space-y-6">
            <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    User Information
                </h3>

                <div>
                    <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Full Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $user->name ?? '') }}"
                           class="w-full px-0 py-2 text-2xl md:text-3xl font-display font-medium text-gray-900 bg-transparent border-0 border-b-2 border-gray-100 focus:border-rose-400 focus:ring-0 placeholder-gray-300 transition-colors"
                           placeholder="John Doe"
                           required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Username *</label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="{{ old('username', $user->username ?? '') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-gray-700"
                               placeholder="johndoe"
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address *</label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email ?? '') }}"
                               class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-gray-700"
                               placeholder="john@example.com"
                               required>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security
                </h3>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Password {{ isset($user) ? '(leave blank to keep current)' : '*' }}
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-gray-700"
                           placeholder="••••••••"
                           {{ isset($user) ? '' : 'required' }}>
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:sticky lg:top-8">
            <div class="glass-panel rounded-3xl p-6 border-t-4 border-rose-400">
                <h3 class="font-display text-lg font-medium text-gray-900 mb-6">Role & Access</h3>

                <div class="space-y-3 mb-6">
                    @php
                        $currentRole = old('role', $user->role ?? '');
                    @endphp

                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $currentRole === 'admin' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="role" value="admin" {{ $currentRole === 'admin' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300" required>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 block">Admin</span>
                            <span class="text-xs text-gray-400">Full access to all features</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $currentRole === 'content_manager' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="role" value="content_manager" {{ $currentRole === 'content_manager' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 block">Content Manager</span>
                            <span class="text-xs text-gray-400">Manage articles only</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-rose-50/50 transition-colors {{ $currentRole === 'user' ? 'bg-rose-50 border-rose-200' : '' }}">
                        <input type="radio" name="role" value="user" {{ $currentRole === 'user' ? 'checked' : '' }} class="text-rose-500 focus:ring-rose-300 border-gray-300">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 block">User</span>
                            <span class="text-xs text-gray-400">Shop and place orders</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full btn-primary group flex items-center justify-center gap-2">
                    <span>{{ isset($user) ? 'Save Changes' : 'Create User' }}</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>

            @if(isset($user))
                <div class="glass-panel rounded-3xl p-6">
                    <h3 class="font-display text-lg font-medium text-gray-900 mb-4">User Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Created</span>
                            <span class="text-gray-700">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Last Updated</span>
                            <span class="text-gray-700">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                        @if($user->orders_count ?? false)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Total Orders</span>
                                <span class="text-gray-700">{{ $user->orders_count }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
