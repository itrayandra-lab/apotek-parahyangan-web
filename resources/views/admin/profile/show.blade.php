@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="section-container section-padding max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                My Profile
            </h1>
            <p class="text-gray-500 font-light">
                View and manage your account details.
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

    <!-- Alert -->
    @if (session('success'))
        <div class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Profile Card -->
    <div class="glass-panel rounded-[2.5rem] overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s;">
        <!-- Cover Background -->
        <div class="h-48 bg-gradient-to-br from-rose-100 via-white to-indigo-50 relative">
            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="px-8 pb-8 relative">
            <!-- Avatar -->
            <div class="relative -mt-16 mb-6 flex justify-center md:justify-start">
                <div class="h-32 w-32 rounded-[2rem] bg-white p-2 shadow-xl shadow-rose-500/10">
                    <div class="h-full w-full rounded-[1.5rem] bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center text-white text-4xl font-display font-bold border border-gray-700">
                        {{ substr($user->username, 0, 1) }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                <div>
                    <h2 class="text-3xl font-display font-bold text-gray-900 mb-1">{{ $user->username }}</h2>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-xs font-bold uppercase tracking-wider border border-rose-100">
                            Administrator
                        </span>
                        <span class="text-gray-400 text-xs">•</span>
                        <span class="text-gray-500 text-xs">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>

                <a href="{{ route('admin.profile.edit') }}" class="btn-primary shadow-lg shadow-rose-200/50 flex items-center gap-2 px-6 py-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit Profile</span>
                </a>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 pt-8">
                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Username</p>
                    <p class="text-lg font-medium text-gray-900">{{ $user->username }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Account Created</p>
                    <p class="text-lg font-medium text-gray-900">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
                
                <!-- Additional Stats (Optional) -->
                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Role</p>
                    <p class="text-lg font-medium text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Super Admin
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</p>
                    <p class="text-lg font-medium text-emerald-600">Active</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection