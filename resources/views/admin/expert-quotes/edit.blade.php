@extends('admin.layouts.app')

@section('title', 'Edit Expert Quote')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-display font-medium text-gray-900">Edit Expert Quote</h1>
            <p class="mt-2 text-gray-600 font-light">Update the featured quote and author details.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.expert-quotes.index') }}" class="btn-secondary inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="glass-panel p-8 rounded-3xl max-w-4xl mx-auto">
        @include('admin.expert-quotes.form', ['expertQuote' => $expertQuote])
    </div>
@endsection
