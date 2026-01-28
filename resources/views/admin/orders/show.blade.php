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
                        @php
                            $isKeras = $item->medicine_id && $item->medicine?->classification === 'Obat Keras';
                        @endphp
                        <div class="py-4 flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-xs relative overflow-hidden">
                                    {{ substr($item->product_name, 0, 1) }}
                                    @if($isKeras)
                                        <div class="absolute inset-0 bg-rose-500/10 border border-rose-500/20 rounded-lg"></div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $item->product_name }}</p>
                                        @if($isKeras)
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest bg-rose-500 text-white">Keras</span>
                                        @endif
                                        @if($item->status === 'cancelled')
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest bg-gray-200 text-gray-500">Ditolak</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">Quantity: <span class="font-mono text-gray-700">{{ $item->quantity }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                                @if($isKeras && $item->status !== 'cancelled' && $order->payment_status !== 'refunded')
                                    @php
                                        $confirmMsg = $order->payment_status === 'paid' 
                                            ? 'Apakah Anda yakin ingin MENOLAK obat keras ini? Dana akan dikembalikan saat pengambilan.' 
                                            : 'Apakah Anda yakin ingin MENOLAK obat keras ini? Tagihan akan diperbarui dan user hanya perlu membayar sisanya.';
                                    @endphp
                                    <form action="{{ route('admin.order-items.reject', $item) }}" method="POST" onsubmit="return confirm('{{ $confirmMsg }}')">
                                        @csrf
                                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 transition-colors" title="Tolak Item">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
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
                        <span class="text-base font-bold text-gray-900 uppercase tracking-wide">
                            {{ $order->payment_status === 'paid' ? 'Total Terbayar' : 'Total Tagihan' }}
                        </span>
                        <span class="text-2xl font-display font-bold text-rose-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    @if($order->refund_amount > 0 && $order->isPaid())
                        <div class="mt-4 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-1">Total Refund (Offline)</p>
                                <p class="text-lg font-display font-bold text-rose-600">Rp {{ number_format($order->refund_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-500">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-rose-400 mt-2 px-1">*) Refund dikembalikan secara manual di Apotek saat pengambilan obat.</p>
                    @endif

                    @if(!$order->isPaid() && $order->items()->where('status', 'cancelled')->exists())
                        <div class="mt-6">
                            <form action="{{ route('admin.orders.sync-total', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2.5 border-2 border-rose-100 text-rose-500 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-rose-50 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Sinkronkan Total Tagihan
                                </button>
                            </form>
                            <p class="text-[10px] text-gray-400 mt-2 text-center leading-tight">Gunakan ini jika total tagihan belum berkurang setelah ada item yang ditolak.</p>
                        </div>
                    @endif
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
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Order Status</p>
                        @php
                            $statusLabel = match($order->status) {
                                'delivered' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'pending_payment' => 'Menunggu Pembayaran',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'confirmed' => 'Sudah Bayar',
                                'expired' => 'Kadaluarsa',
                                default => ucwords(str_replace('_',' ',$order->status))
                            };
                            $statusColor = match($order->status) {
                                'delivered', 'confirmed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'cancelled', 'expired' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'pending_payment' => 'bg-amber-50 text-amber-600 border-amber-100',
                                default => 'bg-blue-50 text-blue-600 border-blue-100'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $statusColor }} border">
                            {{ $statusLabel }}
                        </span>
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

            <!-- Prescription Management -->
            @if($order->prescription)
                <div class="glass-panel rounded-3xl p-6 md:p-8 animate-fade-in-up">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-500">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-display font-bold text-gray-900">Resep Dokter</h2>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $order->prescription->status === 'verified' ? 'bg-emerald-50 text-emerald-600' : ($order->prescription->status === 'rejected' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600') }}">
                            {{ $order->prescription->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Foto Resep</p>
                            <a href="{{ asset('storage/' . $order->prescription->image_path) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                                <img src="{{ asset('storage/' . $order->prescription->image_path) }}" alt="Resep" class="w-full h-auto group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-xs font-bold tracking-widest uppercase">Lihat Fullscreen</span>
                                </div>
                            </a>
                        </div>
                        <div>
                            <form action="{{ route('admin.orders.update-prescription-status', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Update Status Resep</label>
                                    <select name="status" class="w-full text-sm rounded-xl border-gray-200 focus:border-violet-300 focus:ring-4 focus:ring-violet-50">
                                        <option value="pending" {{ $order->prescription->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="verified" {{ $order->prescription->status === 'verified' ? 'selected' : '' }}>Verified (Acc)</option>
                                        <option value="rejected" {{ $order->prescription->status === 'rejected' ? 'selected' : '' }}>Rejected (Tolak)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Catatan Admin</label>
                                    <textarea name="admin_notes" rows="3" class="w-full text-sm rounded-xl border-gray-200 focus:border-violet-300 focus:ring-4 focus:ring-violet-50" placeholder="Info kenapa ditolak atau reupload...">{{ $order->prescription->admin_notes }}</textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-violet-600 text-white text-xs font-bold rounded-xl tracking-widest uppercase hover:bg-violet-700 transition-all shadow-lg shadow-violet-100">
                                    Update Status Resep
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
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

            <!-- Order Status Management -->
            <div class="glass-panel rounded-3xl p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-display font-bold text-gray-900">Manage Status</h2>
                </div>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Order Status</label>
                        <select name="status" class="w-full text-sm rounded-xl border-gray-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-50">
                            <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Sudah Bayar / Dikonfirmasi</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses (Siapkan Obat)</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim (Input Resi)</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai (Diterima User)</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="expired" {{ $order->status === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full btn-primary py-3 text-xs shadow-lg shadow-rose-100">
                        Update Order Status
                    </button>
                </form>
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
