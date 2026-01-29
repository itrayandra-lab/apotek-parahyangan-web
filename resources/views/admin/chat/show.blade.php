@extends('admin.layouts.app')

@section('title', 'Chat Konsultasi - ' . ($session->user ? $session->user->name : 'Guest'))

@push('styles')
<style>
    .chat-main-container {
        height: 750px !important;
        max-height: 85vh !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }
    
    .chat-messages-container {
        flex: 1 !important;
        overflow-y: auto !important;
    }
    
    .chat-fixed-header, 
    .chat-fixed-footer {
        flex-shrink: 0 !important;
    }
    
    /* Ensure the body doesn't scroll if possible */
    body {
        overflow: hidden !important;
    }
    
    main.flex-1 {
        height: 100vh !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto p-2">
    <!-- Chat Area (Unified WhatsApp Style) -->
    <div class="glass-panel rounded-3xl overflow-hidden flex flex-col shadow-2xl animate-fade-in-up chat-main-container" 
         x-data="adminChat({{ $session->id }}, {{ $messages->last()?->id ?? 0 }})"
         x-init="init()">
        
        <!-- Internal Header -->
        <div class="px-6 py-4 bg-white border-b border-gray-100 flex justify-between items-center z-10 shrink-0 chat-fixed-header">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.chats.index') }}" class="w-8 h-8 rounded-full border border-gray-100 flex items-center justify-center hover:bg-gray-50 transition-all">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-500 font-bold uppercase overflow-hidden">
                        {{ substr($session->user ? $session->user->name : 'G', 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-none">
                            {{ $session->user ? $session->user->name : 'Guest User ' . substr($session->session_id, 0, 8) }}
                        </h2>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="flex h-1.5 w-1.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $session->status === 'active' ? 'bg-emerald-400' : 'bg-gray-400' }} opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $session->status === 'active' ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                            </span>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400">{{ $session->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                 @if($session->isGuest())
                    <span class="text-[9px] font-bold uppercase tracking-widest text-gray-300">IP: {{ $session->ip_address }}</span>
                 @endif
            </div>
        </div>

        <!-- Messages History -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/30 custom-scrollbar chat-messages-container" x-ref="messageContainer">
            @php $currentDate = null; @endphp
            @foreach($messages as $message)
                @php 
                    $messageDate = $message->sent_at->format('d/m/Y');
                    if ($messageDate === now()->format('d/m/Y')) $messageDate = 'Today';
                @endphp
                
                @if($currentDate !== $messageDate)
                    <div class="flex justify-center my-4">
                        <span class="px-3 py-1 bg-white/80 backdrop-blur-sm border border-gray-100 rounded-lg text-[10px] font-bold text-gray-400 uppercase tracking-widest shadow-sm">
                            {{ $messageDate }}
                        </span>
                    </div>
                    @php $currentDate = $messageDate; @endphp
                @endif

                <div class="flex {{ $message->type === 'pharmacist' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] flex flex-col {{ $message->type === 'pharmacist' ? 'items-end' : 'items-start' }}">
                        @if($message->type === 'product')
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-md max-w-[250px] transition-transform hover:scale-[1.02]">
                                <div class="p-3 bg-gray-50 border-b border-gray-100">
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-rose-500">Rekomendasi Produk</span>
                                </div>
                                <div class="p-4">
                                    <h4 class="text-xs font-bold text-gray-900 mb-1 line-clamp-2">{{ $message->content }}</h4>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-rose-500 font-bold text-[10px]">
                                            @if(isset($message->metadata['price']))
                                                Rp {{ number_format($message->metadata['price'], 0, ',', '.') }}
                                            @endif
                                        </span>
                                        <span class="text-[8px] text-gray-400 uppercase font-black">{{ $message->metadata['model_type'] ?? 'Item' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="group relative px-4 py-3 shadow-sm {{ 
                                $message->type === 'pharmacist' ? 'bg-rose-500 text-white rounded-2xl rounded-tr-none' : 
                                ($message->type === 'user' ? 'bg-white text-gray-900 border border-gray-100 rounded-2xl rounded-tl-none' : 'bg-blue-50 text-blue-900 border border-blue-100 rounded-2xl') 
                            }}">
                                <p class="text-sm leading-relaxed">{!! $message->getFormattedContentAttribute() !!}</p>
                                <div class="flex justify-end mt-1 gap-1">
                                    <span class="text-[8px] opacity-70 font-semibold">{{ $message->sent_at->format('H:i') }}</span>
                                    @if($message->type === 'pharmacist')
                                        <svg class="w-3 h-3 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Dynamically loaded messages -->
            <template x-for="msg in newMessages" :key="msg.id">
                <div class="flex" :class="msg.type === 'pharmacist' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] flex flex-col" :class="msg.type === 'pharmacist' ? 'items-end' : 'items-start'">
                        <template x-if="msg.type === 'product'">
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-md max-w-[250px]">
                                <div class="p-3 bg-gray-50 border-b border-gray-100">
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-rose-500">Rekomendasi Produk</span>
                                </div>
                                <div class="p-4">
                                    <h4 class="text-xs font-bold text-gray-900 mb-1 line-clamp-2" x-text="msg.content"></h4>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-rose-500 font-bold text-[10px]" x-text="'Rp ' + (msg.metadata?.price ? msg.metadata.price.toLocaleString('id-ID') : '0')"></span>
                                        <span class="text-[8px] text-gray-400 uppercase font-black" x-text="msg.metadata?.model_type || 'Item'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="msg.type !== 'product'">
                            <div class="px-4 py-3 shadow-sm" 
                                 :class="{
                                   'bg-rose-500 text-white rounded-2xl rounded-tr-none': msg.type === 'pharmacist',
                                   'bg-white text-gray-900 border border-gray-100 rounded-2xl rounded-tl-none': msg.type === 'user',
                                   'bg-blue-50 text-blue-900 border border-blue-100 rounded-2xl': msg.type !== 'pharmacist' && msg.type !== 'user'
                                 }">
                                <p class="text-sm leading-relaxed" x-html="formatMessage(msg.content)"></p>
                                <div class="flex justify-end mt-1 gap-1">
                                    <span class="text-[8px] opacity-70 font-semibold" x-text="formatTime(msg.sent_at)"></span>
                                    <template x-if="msg.type === 'pharmacist'">
                                        <svg class="w-3 h-3 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100 shrink-0 chat-fixed-footer">
            <form @submit.prevent="sendMessage()" class="flex items-end gap-3 max-w-4xl mx-auto">
                <button type="button" 
                        @click="openProductModal"
                        class="mb-1 w-12 h-12 flex items-center justify-center bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 hover:text-rose-500 transition-all active:scale-95 shrink-0 shadow-sm border border-gray-100"
                        title="Rekomendasikan Produk">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
                
                <div class="flex-1 relative">
                    <textarea x-model="replyText" 
                              @keydown.enter.prevent="sendMessage()"
                              rows="1" 
                              @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-rose-200 focus:bg-white outline-none resize-none transition-all max-h-32" 
                              placeholder="Ketik balasan Anda di sini..."></textarea>
                </div>

                <button type="submit" 
                        :disabled="!replyText.trim() || sending"
                        class="mb-1 w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center hover:bg-rose-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-rose-200 shrink-0">
                    <svg x-show="!sending" class="w-5 h-5 translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <div x-show="sending" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </form>
        </div>
    </div>

    <!-- Product Search Modal Move outside to be sure -->
    <template x-teleport="body">
        <div x-show="showProductModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @keydown.escape.window="closeProductModal()"
             x-cloak>
            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]" @click.away="closeProductModal()">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-display font-bold text-gray-900 uppercase tracking-wide">Cari Produk</h3>
                    <button @click="closeProductModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 flex-1 overflow-y-auto">
                    <div class="relative mb-6">
                        <input type="text" 
                               x-ref="productSearchInput"
                               x-model="productSearchQuery" 
                               @input.debounce.300ms="searchProducts()"
                               placeholder="Cari nama produk atau obat..."
                               class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-rose-200 outline-none pr-12 transition-all">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <template x-if="searchingProducts">
                            <div class="text-center py-10">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-rose-500 mx-auto"></div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-4">Mencari produk...</p>
                            </div>
                        </template>

                        <template x-if="!searchingProducts && productResults.length === 0 && productSearchQuery">
                            <div class="text-center py-10 opacity-50">
                                <p class="text-sm italic text-gray-500">Tidak ada produk ditemukan.</p>
                            </div>
                        </template>

                        <template x-for="product in productResults" :key="product.model_type + '-' + product.id">
                            <button @click="recommendProduct(product)" 
                                    class="w-full text-left p-4 rounded-2xl border border-gray-100 hover:border-rose-200 hover:bg-rose-50/30 transition-all flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <template x-if="product.image_url">
                                        <img :src="product.image_url" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                    </template>
                                    <template x-if="!product.image_url">
                                        <div class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center text-gray-200 border border-gray-100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </template>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm group-hover:text-rose-600 transition-colors" x-text="product.name"></h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-black uppercase text-gray-400" x-text="product.model_type"></span>
                                            <span class="text-rose-500 font-bold text-[10px]" x-text="'Rp ' + product.price.toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-rose-500 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </div>
</div>

@push('scripts')
<script>
function adminChat(sessionId, initialLastId) {
    return {
        sessionId: sessionId,
        lastId: initialLastId,
        replyText: '',
        sending: false,
        newMessages: [],
        pollTimeout: null,
        pollInterval: 3000,
        
        // Product Injection State
        showProductModal: false,
        productSearchQuery: '',
        searchingProducts: false,
        productResults: [],

        init() {
            // Instant scroll to bottom on load
            this.$nextTick(() => {
                this.scrollToBottom(false);
                // Extra checks for late-rendering elements or images
                setTimeout(() => this.scrollToBottom(false), 100);
                setTimeout(() => this.scrollToBottom(true), 400);
            });
            this.startPolling();
        },

        scrollToBottom(smooth = true) {
            this.$nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: smooth ? 'smooth' : 'auto'
                    });
                }
            });
        },

        startPolling() {
            if (this.pollTimeout) clearTimeout(this.pollTimeout);
            
            const poll = async () => {
                await this.syncMessages();
                this.pollTimeout = setTimeout(poll, this.pollInterval);
            };
            
            this.pollTimeout = setTimeout(poll, this.pollInterval);
        },

        async syncMessages() {
            try {
                const response = await axios.get(`/admin/api/chats/${this.sessionId}/sync`, {
                    params: { last_id: this.lastId }
                });
                
                const data = response.data;

                if (data.new_messages && data.new_messages.length > 0) {
                    const currentIds = this.newMessages.map(m => m.id);
                    const trulyNew = data.new_messages.filter(msg => !currentIds.includes(msg.id));
                    
                    if (trulyNew.length > 0) {
                        this.newMessages.push(...trulyNew);
                        this.lastId = data.last_id;
                        this.scrollToBottom();

                        // Play sound if any message is from user
                        if (trulyNew.some(msg => msg.type === 'user')) {
                            this.playNotificationSound();
                        }
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        },

        playNotificationSound() {
            const audio = new Audio('/audio/notif-chat.mp3');
            audio.currentTime = 0;
            audio.play().catch(e => {
                // Ignore errors related to autoplay policy if the user hasn't interacted yet
                if (e.name !== 'NotAllowedError') {
                    console.error('Audio play failed:', e);
                }
            });
        },

        async sendMessage() {
            const content = this.replyText.trim();
            if (!content || this.sending) return;

            this.sending = true;
            
            try {
                const response = await axios.post(`/admin/chats/${this.sessionId}/reply`, {
                    content: content,
                    type: 'pharmacist'
                });

                if (response.data.success) {
                    this.replyText = '';
                    await this.syncMessages();
                }
            } catch (error) {
                alert('Gagal mengirim pesan. Silakan coba lagi.');
            } finally {
                this.sending = false;
            }
        },

        // Product Recommendation Logic
        openProductModal() {
            this.showProductModal = true;
            this.$nextTick(() => {
                if (this.$refs.productSearchInput) {
                    this.$refs.productSearchInput.focus();
                }
            });
        },

        closeProductModal() {
            this.showProductModal = false;
            this.productSearchQuery = '';
            this.productResults = [];
        },

        async searchProducts() {
            const query = this.productSearchQuery.trim();
            if (query.length < 2) {
                this.productResults = [];
                return;
            }

            this.searchingProducts = true;
            try {
                const response = await axios.get('/admin/api/chats/search-products', {
                    params: { q: query }
                });
                this.productResults = response.data;
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.searchingProducts = false;
            }
        },

        async recommendProduct(product) {
            if (this.sending) return;

            this.sending = true;
            try {
                const response = await axios.post(`/admin/chats/${this.sessionId}/reply`, {
                    content: product.name,
                    type: 'product',
                    metadata: {
                        product_id: product.id,
                        model_type: product.model_type,
                        product_name: product.name,
                        price: product.price,
                        slug: product.slug,
                        image_url: product.image_url
                    }
                });

                if (response.data.success) {
                    this.closeProductModal();
                    await this.syncMessages();
                }
            } catch (error) {
                alert('Gagal merekomendasikan produk.');
            } finally {
                this.sending = false;
            }
        },

        formatMessage(content) {
            if (!content) return '';
            let escaped = content.replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[m];
            });
            return escaped.replace(/\n/g, '<br>').replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>');
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        },

        destroy() {
            if (this.pollTimeout) clearTimeout(this.pollTimeout);
        }
    }
}
</script>
@endpush
@endsection
