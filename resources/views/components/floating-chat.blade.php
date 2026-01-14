<div
    x-data="chatbotWidget()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-50"
    x-cloak
>
    <div
        x-show="isOpen"
        x-transition
        class="w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 glass-panel overflow-hidden"
    >
        <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-4 flex items-center justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-[0.2em] font-bold opacity-80">Beauty Bot</p>
                <h3 class="text-lg font-semibold">Personal Beauty Advisor</h3>
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

        <div class="p-4 bg-gray-50">
            <template x-if="statusMessage">
                <div class="mb-3 px-3 py-2 rounded-xl text-xs font-medium"
                    :class="statusMessage.type === 'error'
                        ? 'bg-rose-50 text-rose-700 border border-rose-100'
                        : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
                    <span x-text="statusMessage.text"></span>
                </div>
            </template>

            <div class="h-72 overflow-y-auto space-y-3 pr-1" x-ref="messages">
                <template x-if="messages.length === 0">
                    <p class="text-xs text-gray-500 text-center mt-8">
                        Mulai percakapan untuk rekomendasi skincare dan makeup terbaik.
                    </p>
                </template>

                <template x-for="message in messages" :key="message.id ?? message.tempId">
                    <div class="flex" :class="message.type === 'user' ? 'justify-end' : 'justify-start'">
                        <div
                            class="max-w-[85%] px-3 py-2 rounded-2xl text-sm shadow-sm border"
                            :class="message.type === 'user'
                                ? 'bg-primary text-white border-primary-dark'
                                : 'bg-white text-gray-900 border-gray-100'"
                        >
                            <div class="whitespace-pre-wrap break-words" x-html="formatMessage(message.content, message.type)"></div>
                            <p class="text-[10px] uppercase tracking-wide mt-1 text-white/80" x-show="message.type === 'user'">You</p>
                        </div>
                    </div>
                </template>

                <template x-if="isTyping">
                    <div class="flex justify-start">
                        <div class="bg-white text-gray-900 border border-gray-100 px-4 py-3 rounded-2xl shadow-sm">
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                                <span class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                                <span class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <form class="border-t border-gray-100 p-4 bg-white space-y-2" @submit.prevent="sendMessage()">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    x-model="form.message"
                    class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary"
                    placeholder="Tanya Beauty Bot..."
                    :disabled="sending"
                >
                <button
                    type="submit"
                    class="bg-gray-900 text-white px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-primary transition disabled:opacity-50"
                    :disabled="sending || form.message.trim() === ''"
                >
                    <span x-show="!sending">Kirim</span>
                    <span x-show="sending">...</span>
                </button>
            </div>
            <div class="flex justify-between text-[10px] uppercase tracking-[0.16em] text-gray-400">
                <button type="button" class="hover:text-primary" @click="resetChat()" :disabled="sending">Reset</button>
            </div>
        </form>
    </div>

    <button
        type="button"
        class="w-14 h-14 rounded-full bg-gradient-to-br from-primary to-primary-dark shadow-xl shadow-rose-200 text-white flex items-center justify-center hover:scale-105 transition-transform"
        @click="toggle()"
        x-show="!isOpen"
        style="display: none;"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-.904 0-1.776-.117-2.588-.336L3 20l1.316-3.947C3.481 14.992 3 13.553 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
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
            form: {
                message: '',
            },
            storageKey: 'chatbot_session_id',
            async init() {
                this.restoreSession();
                await this.checkStatus();
            },
            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.ensureSession();
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
                        ? { type: 'info', text: 'Beauty Bot is online.' }
                        : { type: 'error', text: 'Beauty Bot sedang offline.' };
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Tidak dapat memeriksa status chatbot.' };
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
                    }
                    await this.loadHistory();
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Gagal membuat sesi chatbot.' };
                }
            },
            async loadHistory() {
                if (!this.sessionId) {
                    return;
                }
                try {
                    const response = await axios.get('/api/chatbot/history', {
                        params: { session_id: this.sessionId, limit: 50 },
                    });
                    this.messages = response.data?.messages ?? [];
                    this.$nextTick(() => this.scrollToBottom());
                } catch (error) {
                    // ignore silently; user can still send
                }
            },
            async sendMessage() {
                const text = this.form.message.trim();
                if (!text || this.sending) {
                    return;
                }

                if (!this.sessionId) {
                    await this.createSession();
                    if (!this.sessionId) return;
                }

                const tempId = `temp-${Date.now()}`;
                this.messages.push({ tempId, type: 'user', content: text, sent_at: new Date().toISOString() });
                this.form.message = '';
                this.sending = true;
                this.isTyping = true;
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const response = await axios.post('/api/chatbot/send', {
                        session_id: this.sessionId,
                        message: text,
                    });

                    const botMessages = response.data?.messages ?? [];
                    // Replace temp with persisted user message if returned
                    this.messages = this.messages.filter((msg) => msg.tempId !== tempId);
                    this.messages.push(...botMessages);
                } catch (error) {
                    this.statusMessage = { type: 'error', text: error?.response?.data?.message ?? 'Gagal mengirim pesan.' };
                } finally {
                    this.sending = false;
                    this.isTyping = false;
                    this.$nextTick(() => this.scrollToBottom());
                }
            },
            async resetChat() {
                if (!this.sessionId || this.sending) {
                    return;
                }

                try {
                    const response = await axios.post('/api/chatbot/reset', {
                        session_id: this.sessionId,
                    });
                    this.sessionId = response.data?.session_id;
                    this.messages = [];
                    window.sessionStorage.setItem(this.storageKey, this.sessionId);
                    this.statusMessage = { type: 'info', text: 'Percakapan direset.' };
                } catch (error) {
                    this.statusMessage = { type: 'error', text: 'Gagal mereset percakapan.' };
                }
            },
            scrollToBottom() {
                if (this.$refs.messages) {
                    this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                }
            },
            onScrollBottom() {
                // reserved for future lazy loading
            },
            formatMessage(content, type) {
                if (!content) return '';

                // Escape HTML to prevent XSS
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                };

                // Escape the content first
                let formatted = escapeHtml(content);

                // Convert \n to <br> for line breaks
                formatted = formatted.replace(/\n/g, '<br>');

                return formatted;
            },
        };
    }
</script>
