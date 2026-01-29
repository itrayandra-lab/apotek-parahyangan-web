<div
    x-data="chatbotWidget()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-50"
    x-cloak
>
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="w-[350px] md:w-[420px] h-[600px] max-h-[85vh] bg-white rounded-3xl shadow-2xl border border-gray-100 glass-panel overflow-hidden flex flex-col"
    >
        <div class="bg-gradient-to-r from-rose-500 to-rose-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] font-bold opacity-80">Online Support</p>
                    <h3 class="text-sm font-bold">Konsultasi Apoteker</h3>
                </div>
            </div>
            <button
                type="button"
                class="p-2 rounded-full hover:bg-white/20 transition"
                @click="close()"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-hidden flex flex-col bg-gray-50/50">
            <template x-if="statusMessage">
                <div class="m-4 px-3 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest"
                    :class="statusMessage.type === 'error'
                        ? 'bg-rose-50 text-rose-700 border border-rose-100'
                        : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
                    <span x-text="statusMessage.text"></span>
                </div>
            </template>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth" x-ref="messages">
                <template x-if="messages.length === 0">
                    <div class="text-center mt-10 space-y-4">
                        <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-.904 0-1.776-.117-2.588-.336L3 20l1.316-3.947C3.481 14.992 3 13.553 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <p class="text-xs text-gray-400 px-8">
                            Halo! Tanyakan keluhan kesehatan atau informasi obat kepada Apoteker kami secara langsung.
                        </p>
                    </div>
                </template>

                <template x-for="message in messages" :key="message.id ?? message.tempId">
                    <div class="flex flex-col" :class="message.type === 'user' ? 'items-end' : 'items-start'">
                        {{-- Name Label --}}
                        <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 mb-1 px-2" 
                              x-text="message.type === 'user' ? 'Anda' : (message.type === 'bot' ? 'Beauty Bot' : 'Apoteker')"></span>
                        
                        {{-- Message Bubble --}}
                        <div
                            class="max-w-[85%] px-4 py-3 rounded-2xl text-sm shadow-sm"
                            :class="message.type === 'user'
                                ? 'bg-gray-900 text-white rounded-tr-none'
                                : (message.type === 'product' ? 'bg-transparent shadow-none p-0 w-full' : 'bg-white text-gray-900 border border-gray-100 rounded-tl-none')"
                        >
                            {{-- Text Content --}}
                            <template x-if="message.type !== 'product'">
                                <div class="whitespace-pre-wrap break-words leading-relaxed" x-html="formatMessage(message.content, message.type)"></div>
                            </template>

                            {{-- Product Injection --}}
                            <template x-if="message.type === 'product' && message.product">
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                                    <div class="relative aspect-video bg-gray-50 flex items-center justify-center overflow-hidden">
                                        <template x-if="message.product.image_url">
                                            <img :src="message.product.image_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!message.product.image_url">
                                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </template>
                                        <div class="absolute top-2 right-2">
                                            <span class="bg-rose-500 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase">Rekomendasi</span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h4 class="text-xs font-bold text-gray-900 line-clamp-1 mb-1" x-text="message.product.name"></h4>
                                        <p class="text-rose-500 font-bold text-xs mb-3" x-text="message.product.formatted_price"></p>
                                        <a :href="'/shop/' + message.product.slug" 
                                           class="block w-full text-center bg-gray-900 text-white py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-rose-500 transition-colors">
                                           Beli Sekarang
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="isTyping">
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-100 px-4 py-3 rounded-2xl shadow-sm rounded-tl-none">
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-rose-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                                <span class="w-1.5 h-1.5 bg-rose-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                                <span class="w-1.5 h-1.5 bg-rose-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <form class="border-t border-gray-100 p-4 bg-white" @submit.prevent="sendMessage()">
            <div class="relative">
                <input
                    type="text"
                    x-model="form.message"
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:bg-white transition-all pr-12"
                    placeholder="Ketik pesan..."
                    :disabled="sending"
                >
                <button
                    type="submit"
                    class="absolute right-2 top-1.5 bg-gray-900 text-white p-2 rounded-xl hover:bg-rose-500 transition-colors disabled:opacity-50"
                    :disabled="sending || form.message.trim() === ''"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9-2-9-18-9 18 9 2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
            <div class="flex justify-between mt-3 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full" :class="isTabFocused ? 'bg-emerald-500 animate-pulse' : 'bg-gray-300'"></div>
                    <span class="text-[8px] font-bold uppercase tracking-[0.1em] text-gray-400" x-text="isTabFocused ? 'Terhubung' : 'Idle'"></span>
                </div>
                <button type="button" class="text-[8px] font-bold uppercase tracking-[0.1em] text-gray-300 hover:text-rose-500 transition-colors" @click="resetChat()" :disabled="sending">Mulai Ulang Chat</button>
            </div>
        </form>
    </div>

    <button
        type="button"
        class="w-16 h-16 rounded-3xl bg-gray-900 shadow-2xl shadow-gray-200 text-white flex items-center justify-center hover:scale-105 hover:bg-rose-600 transition-all duration-300 group relative"
        @click="toggle()"
        x-show="!isOpen"
    >
        <div class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 border-2 border-white rounded-full flex items-center justify-center animate-bounce shadow-sm">
            <span class="text-[10px] font-bold">!</span>
        </div>
        <svg class="w-7 h-7 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-.904 0-1.776-.117-2.588-.336L3 20l1.316-3.947C3.481 14.992 3 13.553 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </button>
</div>

<script>
    function chatbotWidget() {
        return {
            isOpen: false,
            sending: false,
            isTyping: false,
            statusMessage: null,
            sessionId: null,
            messages: [],
            lastId: 0,
            pollTimeout: null,
            isTabFocused: true,
            idleTimer: 0,
            pollInterval: 3000,
            form: {
                message: '',
            },
            storageKey: 'consultation_session_id',
            async init() {
                this.restoreSession();
                await this.checkStatus();
                
                // Track focus
                window.addEventListener('focus', () => { 
                    this.isTabFocused = true; 
                    this.pollInterval = 3000;
                    this.resetIdle();
                });
                window.addEventListener('blur', () => { 
                    this.isTabFocused = false; 
                    this.pollInterval = 10000; // Slow down when inactive
                });

                // Idle management
                setInterval(() => {
                    this.idleTimer += 1000;
                    if (this.idleTimer > 300000) { // 5 minutes
                        this.pollInterval = 30000; // Very slow
                    }
                }, 1000);

                // Start polling if session exists
                if (this.sessionId) {
                    this.startPolling();
                }
            },
            resetIdle() {
                this.idleTimer = 0;
                if (this.isTabFocused) this.pollInterval = 3000;
            },
            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.ensureSession();
                    this.resetIdle();
                }
            },
            close() {
                this.isOpen = false;
            },
            async checkStatus() {
                try {
                    const response = await axios.get('/api/chatbot/status');
                    const active = response.data?.active;
                    this.statusMessage = active
                        ? { type: 'info', text: 'Apoteker online & siap membantu.' }
                        : { type: 'error', text: 'Layanan konsultasi sedang sibuk.' };
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Tidak dapat terhubung ke server.' };
                }
            },
            restoreSession() {
                const stored = window.sessionStorage.getItem(this.storageKey);
                if (stored) {
                    this.sessionId = stored;
                }
            },
            async ensureSession() {
                if (this.sessionId) {
                    await this.loadHistory();
                    return;
                }
                await this.createSession();
            },
            async createSession() {
                try {
                    const response = await axios.get('/api/chatbot/session');
                    this.sessionId = response.data?.session_id;
                    if (this.sessionId) {
                        window.sessionStorage.setItem(this.storageKey, this.sessionId);
                        this.startPolling();
                    }
                    await this.loadHistory();
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Gagal membuat sesi konsultasi.' };
                }
            },
            async loadHistory() {
                if (!this.sessionId) return;
                try {
                    const response = await axios.get('/api/chatbot/history', {
                        params: { session_id: this.sessionId, limit: 50 },
                    });
                    this.messages = response.data?.messages ?? [];
                    if (this.messages.length > 0) {
                        this.lastId = Math.max(...this.messages.map(m => m.id || 0));
                    }
                    this.$nextTick(() => this.scrollToBottom());
                } catch (error) {}
            },
            async startPolling() {
                if (this.pollTimeout) clearTimeout(this.pollTimeout);
                
                const poll = async () => {
                    if (this.sessionId) {
                        try {
                            const response = await axios.get('/api/chat/sync', {
                                params: { session_id: this.sessionId, last_id: this.lastId }
                            });
                            
                            if (response.data.new_messages && response.data.new_messages.length > 0) {
                                // Filter out messages already in the list (e.g. from user's own send)
                                const currentIds = this.messages.map(m => m.id);
                                const trulyNew = response.data.new_messages.filter(m => !currentIds.includes(m.id));
                                
                                if (trulyNew.length > 0) {
                                    this.messages.push(...trulyNew);
                                    this.lastId = response.data.last_id;
                                    this.$nextTick(() => this.scrollToBottom());
                                    
                                    // Play notification sound for admin replies (pharmacist or product)
                                    if (trulyNew.some(m => m.type === 'pharmacist' || m.type === 'product')) {
                                        this.playNotificationSound();
                                    }

                                    // Browser Notification if blurred
                                    if (!this.isTabFocused || !this.isOpen) {
                                        this.notifyNewMessage();
                                    }
                                }
                            }
                        } catch (e) {}
                    }
                    this.pollTimeout = setTimeout(poll, this.pollInterval);
                };
                
                this.pollTimeout = setTimeout(poll, this.pollInterval);
            },
            playNotificationSound() {
                const audio = new Audio('/audio/notif-chat.mp3');
                audio.currentTime = 0;
                audio.play().catch(e => {
                    if (e.name !== 'NotAllowedError') {
                        console.error('Audio play failed:', e);
                    }
                });
            },
            notifyNewMessage() {
                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("Apotek Parahyangan", {
                        body: "Anda menerima pesan konsultasi baru.",
                        icon: "/favicon.ico"
                    });
                } else if ("Notification" in window && Notification.permission !== "denied") {
                    Notification.requestPermission();
                }
            },
            async sendMessage() {
                const text = this.form.message.trim();
                if (!text || this.sending) return;

                if (!this.sessionId) {
                    await this.createSession();
                    if (!this.sessionId) return;
                }

                this.resetIdle();
                const tempId = `temp-${Date.now()}`;
                this.messages.push({ id: null, tempId, type: 'user', content: text, sent_at: new Date().toISOString() });
                this.form.message = '';
                this.sending = true;
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const response = await axios.post('/api/chat/send', {
                        session_id: this.sessionId,
                        message: text,
                    });
                    
                    const sentMsg = response.data?.message;
                    // Find and update temp message
                    const idx = this.messages.findIndex(m => m.tempId === tempId);
                    if (idx !== -1 && sentMsg) {
                        this.messages[idx].id = sentMsg.id;
                        this.lastId = Math.max(this.lastId, sentMsg.id);
                    }
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Gagal mengirim pesan.' };
                } finally {
                    this.sending = false;
                    this.$nextTick(() => this.scrollToBottom());
                }
            },
            async resetChat() {
                if (!this.sessionId || this.sending) return;
                if (!confirm('Hapus riwayat chat dan mulai percakapan baru?')) return;

                try {
                    const response = await axios.post('/api/chatbot/reset', {
                        session_id: this.sessionId,
                    });
                    this.sessionId = response.data?.session_id;
                    this.messages = [];
                    this.lastId = 0;
                    window.sessionStorage.setItem(this.storageKey, this.sessionId);
                    this.statusMessage = { type: 'info', text: 'Percakapan baru dimulai.' };
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Gagal mereset chat.' };
                }
            },
            scrollToBottom() {
                if (this.$refs.messages) {
                    this.$refs.messages.scrollTo({
                        top: this.$refs.messages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            },
            formatMessage(content, type) {
                if (!content) return '';
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                };
                let formatted = escapeHtml(content);
                formatted = formatted.replace(/\n/g, '<br>');
                return formatted;
            },
        };
    }
</script>
