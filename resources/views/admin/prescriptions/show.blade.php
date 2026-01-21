@extends('admin.layouts.app')

@section('title', 'Prescription #' . $prescription->id)

@section('content')
<div class="section-container section-padding">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.prescriptions.index') }}" class="inline-flex items-center text-rose-600 hover:text-rose-700 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Prescriptions
        </a>
        <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
            Prescription #{{ $prescription->id }}
        </h1>
        <p class="text-gray-500 font-light text-lg">
            Verify prescription and create order
        </p>
    </div>

    @if(session('success'))
        <div class="glass-panel rounded-3xl p-4 mb-6 bg-green-50 border-l-4 border-green-500">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Left Column: Info & Image -->
        <div class="space-y-6">
            <div class="glass-panel rounded-3xl p-6">
                <h2 class="text-xl font-display font-medium uppercase text-gray-900 mb-4 tracking-wide text-center">Patient Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500 text-sm">Name</span>
                        <span class="font-bold text-gray-900">{{ $prescription->user->name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500 text-sm">WhatsApp</span>
                        <span class="font-medium text-gray-900">{{ $prescription->user->whatsapp ?? '-' }}</span>
                    </div>
                    @if($prescription->user_notes)
                        <div class="mt-4 p-4 bg-rose-50/50 rounded-2xl border border-rose-100/50">
                            <p class="text-[10px] uppercase font-black text-rose-500 mb-1">Patient Notes</p>
                            <p class="text-sm text-rose-900 leading-relaxed italic">"{{ $prescription->user_notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="glass-panel rounded-3xl p-6">
                <h2 class="text-xl font-display font-medium uppercase text-gray-900 mb-4 tracking-wide text-center">Prescription Image</h2>
                <div class="relative group">
                    <img src="{{ $prescription->image_url }}" alt="Prescription" class="w-full rounded-2xl shadow-lg cursor-zoom-in" onclick="openImageModal()">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center pointer-events-none">
                        <span class="text-white text-xs font-bold uppercase tracking-widest">Click to Zoom</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Verification Form / Order Details -->
        <div class="space-y-6">
            @if($prescription->status === 'pending')
                <div class="glass-panel rounded-3xl p-8" x-data="prescriptionVerification()">
                    <h2 class="text-2xl font-display font-medium uppercase text-gray-900 mb-8 tracking-wide border-b pb-4 text-center">System Order</h2>
                    
                    <form action="{{ route('admin.prescriptions.verify', $prescription) }}" method="POST" @submit="validateForm($event)">
                        @csrf
                        
                        <!-- Single Unified Dropdown with Add Button -->
                        <div class="mb-10">
                            <label class="block text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Pilih Produk atau Obat</label>
                            <div class="flex gap-2">
                                <select x-model="selectedItem" class="flex-1 px-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 focus:ring-2 focus:ring-rose-200 outline-none transition-all">
                                    <option value="">-- Pilih dari Database --</option>
                                    
                                    <optgroup label="OBAT (PHARMACY)">
                                        @foreach($all_medicines as $med)
                                            <option value="{{ json_encode(['id' => $med->id, 'name' => $med->name, 'price' => $med->price, 'type' => 'medicine', 'classification' => $med->classification]) }}">
                                                [{{ $med->classification ?? 'Obat' }}] {{ $med->name }} - Rp {{ number_format($med->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </optgroup>

                                    <optgroup label="PRODUK (GENERAL)">
                                        @foreach($all_products as $prod)
                                            <option value="{{ json_encode(['id' => $prod->id, 'name' => $prod->name, 'price' => $prod->discount_price ?? $prod->price, 'type' => 'product']) }}">
                                                {{ $prod->name }} - Rp {{ number_format($prod->discount_price ?? $prod->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <button type="button" @click="addItem()" class="px-8 bg-rose-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-rose-600 transition-all active:scale-95 shadow-lg shadow-rose-100">
                                    Add
                                </button>
                            </div>
                        </div>

                        <!-- Order Items List -->
                        <div class="mb-8">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide flex justify-between">
                                Daftar Pesanan
                                <span class="text-rose-500 italic lowercase font-normal" x-show="items.length > 0" x-text="items.length + ' item terpilih'"></span>
                            </h3>
                            
                            <div class="space-y-3 min-h-[120px]">
                                <template x-if="items.length === 0">
                                    <div class="p-10 text-center border-2 border-dashed border-gray-100 rounded-3xl bg-gray-50/50">
                                        <div class="mb-2 text-gray-300"><i class="fas fa-shopping-basket text-3xl"></i></div>
                                        <p class="text-gray-400 text-sm italic">Pilih barang dari dropdown di atas</p>
                                    </div>
                                </template>

                                <template x-for="(item, index) in items" :key="item.key">
                                    <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="font-bold text-sm text-gray-900 truncate" x-text="item.name"></p>
                                                <span class="px-2 py-0.5 text-[8px] font-black uppercase rounded text-white" 
                                                      :class="item.type === 'medicine' ? 'bg-blue-600' : 'bg-gray-800'"
                                                      x-text="item.classification || item.type"></span>
                                            </div>
                                            <p class="text-xs font-bold text-rose-500">Rp <span x-text="formatPrice(item.price)"></span></p>
                                        </div>

                                        <div class="flex items-center bg-gray-50 rounded-xl p-1 px-2 border border-gray-100">
                                            <button type="button" @click="updateQty(item.key, -1)" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white text-gray-500 transition-colors"><i class="fas fa-minus text-[10px]"></i></button>
                                            <span class="w-8 text-center text-sm font-bold px-1" x-text="item.quantity"></span>
                                            <button type="button" @click="updateQty(item.key, 1)" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white text-gray-500 transition-colors"><i class="fas fa-plus text-[10px]"></i></button>
                                        </div>

                                        <button type="button" @click="removeItem(item.key)" class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-500 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <!-- Hidden Inputs for Form Submission -->
                                        <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.type === 'product' ? item.id : ''">
                                        <input type="hidden" :name="'items['+index+'][medicine_id]'" :value="item.type === 'medicine' ? item.id : ''">
                                        <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                                        <input type="hidden" :name="'items['+index+'][price]'" :value="item.price">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Summary & Notes -->
                        <div class="space-y-6 pt-6 border-t border-gray-100">
                            <div class="flex justify-between items-end bg-rose-50/50 p-4 rounded-2xl">
                                <span class="text-gray-500 uppercase text-[10px] font-black tracking-widest">Grand Total</span>
                                <span class="text-3xl font-display font-bold text-rose-600">Rp <span x-text="formatPrice(itemTotal)"></span></span>
                            </div>

                            <textarea name="admin_notes" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-rose-200 outline-none" placeholder="Catatan Apoteker..."></textarea>

                            <div class="grid grid-cols-2 gap-4">
                                <button type="submit" class="col-span-1 px-6 py-4 bg-gray-900 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-rose-600 transition-all shadow-xl active:scale-95">Verify & Create Order</button>
                                <button type="button" onclick="showRejectModal()" class="col-span-1 px-6 py-4 border-2 border-red-50 text-red-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-red-50 transition-all active:scale-95">Reject Prescription</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <!-- Verified View (Existing) -->
                @if($prescription->order)
                    <div class="glass-panel rounded-3xl p-8 bg-white overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                            <i class="fas fa-file-invoice text-9xl"></i>
                        </div>
                        
                        <div class="flex justify-between items-start mb-10 border-b border-gray-50 pb-6">
                            <div>
                                <h1 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-1">Order Summary</h1>
                                <p class="text-2xl font-display font-bold text-gray-900">#{{ $prescription->order->id }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-4 py-1.5 bg-rose-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-200">
                                    {{ $prescription->status }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4 mb-10">
                            @foreach($prescription->order->items as $item)
                                <div class="flex justify-between items-center group">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-gray-900 text-sm">{{ $item->name }}</p>
                                            <span class="text-[8px] text-gray-400 font-bold uppercase">{{ $item->medicine_id ? 'Obat' : 'Produk' }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium tracking-tighter">Qty: {{ $item->quantity }} × <span class="text-rose-500">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span></p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                            <div class="h-px bg-gray-50 my-6"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black uppercase tracking-widest text-gray-900">Total Price</span>
                                <span class="text-2xl font-display font-bold text-rose-600">Rp {{ number_format($prescription->order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-10">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <p class="text-[9px] uppercase font-black text-gray-400 mb-1">Payment</p>
                                <p class="text-xs font-bold {{ $prescription->order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-500' }}">
                                    <i class="fas {{ $prescription->order->payment_status === 'paid' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                    {{ strtoupper($prescription->order->payment_status) }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <p class="text-[9px] uppercase font-black text-gray-400 mb-1">Pickup</p>
                                <p class="text-xs font-bold text-blue-600 truncate">
                                    <i class="fas fa-box-open mr-1"></i>
                                    {{ strtoupper(str_replace('_', ' ', $prescription->order->pickup_status)) }}
                                </p>
                            </div>
                        </div>

                        <!-- Scan & Action -->
                        <div class="text-center p-8 bg-rose-50/30 rounded-3xl border border-rose-100/50 mb-10">
                            <p class="text-[10px] uppercase font-black text-rose-500 mb-6 tracking-widest">Verification QR Code</p>
                            <div class="inline-block p-4 bg-white rounded-2xl shadow-xl ring-1 ring-rose-100">
                                {!! $prescription->order->qr_code_data_url !!}
                            </div>
                            <p class="mt-4 font-mono text-sm font-bold text-gray-900 uppercase tracking-tighter">{{ $prescription->order->qr_code_token }}</p>
                        </div>

                        <form action="{{ route('admin.prescriptions.update-order-status', $prescription->order) }}" method="POST" class="space-y-3">
                            @csrf
                            @if($prescription->order->payment_status === 'unpaid')
                                <button type="submit" name="action" value="mark_paid" class="w-full py-4 bg-emerald-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-600">Mark as Lunas</button>
                            @endif
                            @if($prescription->order->pickup_status === 'waiting' && $prescription->order->payment_status === 'paid')
                                <button type="submit" name="action" value="mark_ready" class="w-full py-4 bg-blue-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-lg shadow-blue-200 transition-all">Mark Ready for Pickup</button>
                            @endif
                            @if($prescription->order->pickup_status === 'ready')
                                <button type="submit" name="action" value="mark_picked_up" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-xl transition-all">Confirm Picked Up</button>
                            @endif
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Zoom Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center p-4 backdrop-blur-sm" onclick="closeImageModal()">
    <button onclick="closeImageModal()" class="absolute top-10 right-10 text-white hover:text-rose-500 transition-colors">
        <i class="fas fa-times text-4xl"></i>
    </button>
    <img src="{{ $prescription->image_url }}" alt="Prescription Detail" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl ring-4 ring-white/10 overflow-auto">
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeRejectModal()">
    <div class="glass-panel rounded-3xl p-8 max-w-md w-full" onclick="event.stopPropagation()">
        <h3 class="text-xl font-display font-medium uppercase text-gray-900 mb-6 tracking-wide text-center">Prescription Rejection</h3>
        <form action="{{ route('admin.prescriptions.reject', $prescription) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Reason</label>
                <textarea name="admin_notes" rows="4" required class="w-full px-4 py-3 bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-rose-200 outline-none" placeholder="Provide reason for rejection..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold text-[10px] uppercase transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-red-500 text-white rounded-2xl font-bold text-[10px] uppercase shadow-lg shadow-red-200 transition-all">Reject Resep</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function prescriptionVerification() {
    return {
        selectedItem: '',
        items: [],
        
        addItem() {
            if (!this.selectedItem) {
                alert('Silakan pilih produk atau obat terlebih dahulu');
                return;
            }
            
            const item = JSON.parse(this.selectedItem);
            const key = `${item.type}-${item.id}`;
            
            if (this.items.find(i => i.key === key)) {
                alert('Item ini sudah ditambahkan ke daftar');
                return;
            }
            
            this.items.push({
                ...item,
                key: key,
                quantity: 1
            });
            
            this.selectedItem = '';
        },
        
        removeItem(key) {
            this.items = this.items.filter(i => i.key !== key);
        },
        
        updateQty(key, delta) {
            const item = this.items.find(i => i.key === key);
            if (item) {
                item.quantity = Math.max(1, item.quantity + delta);
            }
        },
        
        get itemTotal() {
            return this.items.reduce((total, i) => total + (i.price * i.quantity), 0);
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        },
        
        validateForm(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Silakan tambah setidaknya satu item ke pesanan');
            }
        }
    }
}

function openImageModal() { document.getElementById('imageModal').classList.remove('hidden'); document.getElementById('imageModal').classList.add('flex'); }
function closeImageModal() { document.getElementById('imageModal').classList.add('hidden'); document.getElementById('imageModal').classList.remove('flex'); }
function showRejectModal() { document.getElementById('rejectModal').classList.remove('hidden'); document.getElementById('rejectModal').classList.add('flex'); }
function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); document.getElementById('rejectModal').classList.remove('flex'); }
</script>
@endpush
@endsection
