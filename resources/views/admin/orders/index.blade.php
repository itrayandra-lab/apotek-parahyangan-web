@extends('admin.layouts.app')

@section('title', 'Orders Management')

@section('content')
<div class="section-container section-padding">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Orders
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Monitor and manage customer orders and payments.
            </p>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col md:flex-row gap-4">
            
            <!-- Search -->
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search order # or customer..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300"
                >
            </div>

            <!-- Order Status Filter -->
            <div class="relative min-w-[180px]">
                <select
                    name="status"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    @foreach(['pending_payment','confirmed','processing','shipped','delivered','cancelled','expired'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Payment Status Filter -->
            <div class="relative min-w-[180px]">
                <select
                    name="payment_status"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">Payment Status</option>
                    @foreach(['unpaid','paid','failed','expired','refunded'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(($filters['payment_status'] ?? '') === $paymentStatus)>
                            {{ ucwords(str_replace('_', ' ', $paymentStatus)) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200">
                    Filter
                </button>
                
                @if(($filters['search'] ?? '') || ($filters['status'] ?? '') || ($filters['payment_status'] ?? ''))
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl border border-transparent hover:border-rose-100" title="Reset">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-white/30 backdrop-blur-sm">
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Order #</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Customer</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Total</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Status</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Payment</th>
                        <th class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                            <!-- Order Number -->
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-mono text-xs font-bold shadow-inner">
                                        #
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 font-mono tracking-wide">
                                        {{ $order->order_number }}
                                    </span>
                                </div>
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-6">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 mb-0.5">{{ $order->user?->name ?? 'Guest' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user?->email ?? '-' }}</div>
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-6">
                                <span class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Order Status -->
                            <td class="px-6 py-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>

                            <!-- Payment Status -->
                            <td class="px-6 py-6">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Paid
                                    </span>
                                @elseif($order->payment_status === 'unpaid')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Unpaid
                                    </span>
                                @elseif($order->payment_status === 'expired')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 border border-gray-200">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-100">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-all shadow-sm group-hover:bg-white group-hover:border-gray-300"
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold font-display text-gray-900 mb-2">No Orders Found</h3>
                                    <p class="text-gray-500 mb-0 font-light">It seems there are no orders matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection