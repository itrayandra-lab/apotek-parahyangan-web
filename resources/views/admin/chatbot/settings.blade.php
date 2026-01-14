@extends('admin.layouts.app')

@section('title', 'Pengaturan Chatbot')

@section('content')
    <div class="section-container section-padding max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2">
                    Chatbot
                </h1>
                <p class="text-gray-500 font-light">
                    Configure AI behavior and webhook integrations.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-2 text-gray-400 hover:text-rose-500 transition-colors">
                <div
                    class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-rose-200 group-hover:bg-rose-50 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest">Dashboard</span>
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-fade-in-up">
            <!-- Status Card -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4">
                    <div
                        class="p-3 rounded-xl {{ $stats['chatbot_status'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    @if ($stats['chatbot_status'])
                        <span class="flex h-3 w-3 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                    @else
                        <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">System Status</p>
                    <h3
                        class="text-xl font-display font-bold {{ $stats['chatbot_status'] ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $stats['chatbot_status'] ? 'Operational' : 'Offline' }}
                    </h3>
                </div>
            </div>

            <!-- Total Sessions -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 rounded-xl bg-gray-50 text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Sessions</p>
                    <h3 class="text-2xl font-display font-bold text-gray-900">{{ number_format($stats['total_sessions']) }}
                    </h3>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 rounded-xl bg-cyan-50 text-cyan-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Active Now</p>
                    <h3 class="text-2xl font-display font-bold text-gray-900">{{ number_format($stats['active_sessions']) }}
                    </h3>
                </div>
            </div>

            <!-- Today's Messages -->
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Messages Today</p>
                    <h3 class="text-2xl font-display font-bold text-gray-900">{{ number_format($stats['today_messages']) }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Configuration Form -->
        <form method="POST" action="{{ route('admin.chatbot.settings.update') }}" class="space-y-8 animate-fade-in-up"
            style="animation-delay: 0.1s;">
            @csrf

            @if (session('success'))
                <div
                    class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="glass-panel border-l-4 border-rose-500 text-rose-800 px-6 py-4 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- General Settings -->
                <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6 h-full">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-bold text-gray-900">General Configuration</h2>
                    </div>

                    <!-- Enable Toggle -->
                    <div class="bg-white/50 rounded-2xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="chatbot_active" class="block text-sm font-bold text-gray-900">Enable
                                    Chatbot</label>
                                <p class="text-xs text-gray-500 mt-1">Globally enable/disable the chatbot feature.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="chatbot_active" value="0">
                                <input type="checkbox" id="chatbot_active" name="chatbot_active" value="1"
                                    class="sr-only peer"
                                    {{ old('chatbot_active', $configs['chatbot_active'] ?? false) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rose-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Rate Limit -->
                    <div>
                        <label for="rate_limit_per_minute"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Rate Limit (per
                            min)</label>
                        <input type="number" id="rate_limit_per_minute" name="rate_limit_per_minute"
                            value="{{ old('rate_limit_per_minute', $configs['rate_limit_per_minute'] ?? 30) }}"
                            min="10" max="100"
                            class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-900">
                        <p class="text-[10px] text-gray-400 mt-2">Max messages per user/minute (10-100).</p>
                    </div>

                    <!-- Session Expiry -->
                    <div>
                        <label for="guest_session_expiry_days"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Guest Session
                            Expiry (Days)</label>
                        <input type="number" id="guest_session_expiry_days" name="guest_session_expiry_days"
                            value="{{ old('guest_session_expiry_days', $configs['guest_session_expiry_days'] ?? 7) }}"
                            min="1" max="90"
                            class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-900">
                        <p class="text-[10px] text-gray-400 mt-2">Days before a guest session is cleared.</p>
                    </div>
                </div>

                <!-- Webhook Settings -->
                <div class="glass-panel rounded-3xl p-6 md:p-8 space-y-6 h-full">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-display font-bold text-gray-900">Webhook Integration</h2>
                    </div>

                    <!-- Enable Webhook -->
                    <div class="bg-white/50 rounded-2xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="webhook_enabled" class="block text-sm font-bold text-gray-900">Enable
                                    Webhook</label>
                                <p class="text-xs text-gray-500 mt-1">Process messages via n8n/AI endpoint.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="webhook_enabled" value="0">
                                <input type="checkbox" id="webhook_enabled" name="webhook_enabled" value="1"
                                    class="sr-only peer"
                                    {{ old('webhook_enabled', $configs['webhook_enabled'] ?? true) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rose-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Webhook URL -->
                    <div>
                        <label for="webhook_url"
                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Endpoint
                            URL</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </span>
                            <input type="url" id="webhook_url" name="webhook_url"
                                value="{{ old('webhook_url', $configs['webhook_url'] ?? '') }}"
                                placeholder="https://n8n.example.com/webhook/..."
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm text-gray-900 placeholder-gray-400 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Timeout -->
                        <div>
                            <label for="webhook_timeout"
                                class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Timeout
                                (sec)</label>
                            <input type="number" id="webhook_timeout" name="webhook_timeout"
                                value="{{ old('webhook_timeout', $configs['webhook_timeout'] ?? 30) }}" min="5"
                                max="120"
                                class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-900">
                        </div>

                        <!-- Retry -->
                        <div>
                            <label for="webhook_retry_attempts"
                                class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Retries</label>
                            <input type="number" id="webhook_retry_attempts" name="webhook_retry_attempts"
                                value="{{ old('webhook_retry_attempts', $configs['webhook_retry_attempts'] ?? 3) }}"
                                min="1" max="5"
                                class="w-full px-4 py-3 rounded-xl bg-white/50 border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition-all text-sm font-mono text-gray-900">
                        </div>
                    </div>

                    <!-- Test Connection -->
                    <div class="pt-2">
                        <button type="button" id="testWebhookBtn"
                            class="w-full btn-secondary py-3 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Test Connection</span>
                        </button>
                        <div id="webhookTestResult"
                            class="mt-3 hidden rounded-xl border px-4 py-3 text-sm font-medium animate-fade-in-up"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200/60">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="btn-primary px-8 py-3 flex items-center gap-2 shadow-lg shadow-rose-200 hover:shadow-rose-300">
                    <span>Save Configuration</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const testBtn = document.getElementById('testWebhookBtn');
            const resultDiv = document.getElementById('webhookTestResult');
            const webhookUrlInput = document.getElementById('webhook_url');
            const webhookTimeoutInput = document.getElementById('webhook_timeout');

            const showResult = (type, message) => {
                resultDiv.classList.remove('hidden');
                resultDiv.textContent = message;
                resultDiv.className = 'mt-3 rounded-xl border px-4 py-3 text-sm font-medium animate-fade-in-up';

                if (type === 'success') {
                    resultDiv.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                    resultDiv.classList.remove('border-rose-200', 'bg-rose-50', 'text-rose-700');
                } else {
                    resultDiv.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700');
                    resultDiv.classList.remove('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                }
            };

            testBtn?.addEventListener('click', async () => {
                const webhookUrl = webhookUrlInput.value;
                const webhookTimeout = webhookTimeoutInput.value || 30;

                if (!webhookUrl) {
                    showResult('error', 'Please enter a Webhook URL first.');
                    return;
                }

                const originalText = testBtn.innerHTML;
                testBtn.disabled = true;
                testBtn.innerHTML = '<span class="animate-pulse">Testing...</span>';
                resultDiv.classList.add('hidden');

                try {
                    const response = await fetch('{{ route('admin.chatbot.test-webhook') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                        },
                        body: JSON.stringify({
                            webhook_url: webhookUrl,
                            webhook_timeout: webhookTimeout,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        const latency = data.latency ? ` (${data.latency} ms)` : '';
                        showResult('success', (data.message || 'Connection successful') + latency);
                    } else {
                        showResult('error', data.message || 'Webhook connection failed.');
                    }
                } catch (error) {
                    showResult('error', 'Network error: ' + error.message);
                } finally {
                    testBtn.disabled = false;
                    testBtn.innerHTML = originalText;
                }
            });
        });
    </script>
@endsection
