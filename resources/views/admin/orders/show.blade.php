@extends('admin.layouts.app')

@section('title', 'Order Detail')

@section('content')
<div class="section-container section-padding max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.orders.index') }}" class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Order Detail</p>
            </div>
            <h1 class="text-3xl md:text-5xl font-display font-medium text-gray-900 font-mono tracking-tight">
                #{{ $order->order_number }}
            </h1>
        </div>
        
        <div class="flex items-center gap-3">
            @if($order->payment_status !== 'paid')
                <form action="{{ route('admin.orders.mark-paid', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary px-6 py-2.5 text-xs shadow-lg shadow-rose-200">
                        Mark as Paid
                    </button>
                </form>
            @endif
            
        </div>
    </div>

    <!-- Alert -->
    @if (session('success'))
        <div class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Items & Payment -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Order Items -->
            <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Order Items</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="py-4 flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-xs">
                                    {{ substr($item->product_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Quantity: <span class="font-mono text-gray-700">{{ $item->quantity }}</span></p>
                                </div>
                            </div>
                            <div class="text-sm font-bold text-gray-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Totals -->
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->voucher_discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Voucher Discount</span>
                            <span class="text-emerald-600 font-medium">-Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</span>
                        </div>
                        @if($order->voucher_code)
                            <div class="text-xs text-gray-400 text-right">Kode: {{ $order->voucher_code }}</div>
                        @endif
                    @endif
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Shipping</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                        <span class="text-base font-bold text-gray-900 uppercase tracking-wide">Total</span>
                        <span class="text-2xl font-display font-bold text-rose-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Payment Details</h2>
                </div>

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</p>
                        @if($order->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">Paid</span>
                        @elseif($order->payment_status === 'unpaid')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">Unpaid</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Gateway</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($order->payment_gateway) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Type</p>
                        <p class="font-medium text-gray-900">{{ $order->payment_type ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">External ID</p>
                        <p class="font-mono text-gray-600 text-xs">{{ $order->payment_external_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Paid At</p>
                        <p class="font-medium text-gray-900">{{ $order->paid_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Expired At</p>
                        <p class="font-medium text-gray-900">{{ $order->payment_expired_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                </div>

                @if($order->payment_callback_data)
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.orders.payment-callback', $order) }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                            View raw callback data →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Customer & Shipping -->
        <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.3s;">
            
            <!-- Customer -->
            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Customer</h2>
                </div>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-lg">
                        {{ substr($order->user?->name ?? 'G', 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $order->user?->name ?? 'Guest' }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user?->email ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Phone</p>
                    <p class="font-mono text-sm text-gray-700">{{ $order->phone ?? '-' }}</p>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Shipping</h2>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Courier</p>
                            <p class="text-sm text-gray-900 font-medium">{{ strtoupper($order->shipping_courier ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Service</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_service ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Estimasi</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_etd ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Berat</p>
                            <p class="text-sm text-gray-900 font-mono">{{ number_format($order->shipping_weight ?? 0) }} gram</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">No. Resi (AWB)</p>
                        @if($order->shipping_awb)
                            <p class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg">{{ $order->shipping_awb }}</p>
                        @else
                            <form action="{{ route('admin.orders.update-awb', $order) }}" method="POST" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="shipping_awb" placeholder="Masukkan nomor resi" class="flex-1 text-sm px-3 py-2 rounded-lg border border-gray-200 focus:border-rose-300 focus:ring-2 focus:ring-rose-100">
                                <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-rose-600 transition-colors">Simpan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Alamat</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Address</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $order->shipping_address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">City</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_city }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">District</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_district ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Village</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_village ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Postal Code</p>
                            <p class="text-sm text-gray-900 font-mono">{{ $order->shipping_postal_code }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Province</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $order->shipping_province }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
