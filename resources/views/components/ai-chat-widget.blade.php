<div
    x-data="aiChat()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-50 flex flex-col items-end"
>
    {{-- Chat panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="mb-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
        style="height: 480px;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-green-600 text-white">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                        </svg>
                    </div>
                <div>
                    <p class="font-semibold text-sm">ChorvaAI Yordamchi</p>
                    <p class="text-xs text-green-100">Har doim tayyor</p>
                </div>
            </div>
            <button @click="open = false" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
        >
            {{-- Welcome message --}}
            <template x-if="messages.length === 0">
                <div class="flex items-start gap-2">
                    <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                            </svg>
                        </div>
                    <div class="bg-white rounded-2xl rounded-tl-none px-3 py-2 text-sm text-gray-700 shadow-sm max-w-[80%]">
                        Salom! Men ChorvaAI yordamchisiman. Chorva mollari, narxlar yoki parvarishlash haqida savollaringiz bo'lsa, bemalol so'rang! 🌿
                    </div>
                </div>
            </template>

            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start gap-2'">
                    <template x-if="msg.role === 'model'">
                        <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                            </svg>
                        </div>
                    </template>
                    <div
                        :class="msg.role === 'user'
                            ? 'bg-green-600 text-white rounded-2xl rounded-tr-none px-3 py-2 text-sm max-w-[80%]'
                            : 'bg-white text-gray-700 rounded-2xl rounded-tl-none px-3 py-2 text-sm shadow-sm max-w-[80%]'"
                        x-text="msg.content"
                    ></div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <template x-if="loading">
                <div class="flex items-start gap-2">
                    <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                            </svg>
                        </div>
                    <div class="bg-white rounded-2xl rounded-tl-none px-3 py-2 shadow-sm">
                        <div class="flex gap-1 items-center h-4">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input --}}
        <div class="p-3 bg-white border-t border-gray-100">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input
                    x-model="input"
                    type="text"
                    placeholder="Savolingizni yozing..."
                    :disabled="loading"
                    class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 disabled:opacity-50"
                    maxlength="1000"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    :disabled="loading || !input.trim()"
                    class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl px-3 py-2 transition flex items-center justify-center"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Toggle button --}}
    <div class="relative">
        {{-- To'lqin halqalari (faqat yopiq holatda) --}}
        <span x-show="!open" class="absolute inset-0 rounded-full bg-green-500 ai-ripple-1" aria-hidden="true"></span>
        <span x-show="!open" class="absolute inset-0 rounded-full bg-green-500 ai-ripple-2" aria-hidden="true"></span>
        <span x-show="!open" class="absolute inset-0 rounded-full bg-green-500 ai-ripple-3" aria-hidden="true"></span>

        <button
            @click="toggleChat()"
            class="relative z-10 w-14 h-14 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg flex items-center justify-center transition-colors duration-200"
        >
            <span x-show="!open" class="ai-icon-sparkle">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                </svg>
            </span>
            <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <template x-if="unread > 0 && !open">
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" x-text="unread"></span>
            </template>
        </button>
    </div>
</div>

<style>
@keyframes ai-ripple {
    0%   { transform: scale(1);   opacity: 0.5; }
    100% { transform: scale(2.6); opacity: 0;   }
}
@keyframes ai-sparkle {
    0%, 100% { transform: scale(1)    rotate(0deg);  }
    25%       { transform: scale(1.12) rotate(14deg); }
    50%       { transform: scale(1)    rotate(0deg);  }
    75%       { transform: scale(0.92) rotate(-10deg);}
}
.ai-ripple-1 { animation: ai-ripple 2.4s ease-out infinite;       }
.ai-ripple-2 { animation: ai-ripple 2.4s ease-out infinite 0.8s;  }
.ai-ripple-3 { animation: ai-ripple 2.4s ease-out infinite 1.6s;  }
.ai-icon-sparkle {
    display: inline-block;
    animation: ai-sparkle 3s ease-in-out infinite;
}
</style>

<script>
function aiChat() {
    return {
        open: false,
        messages: [],
        input: '',
        loading: false,
        unread: 0,

        init() {
            this.loadHistory();
        },

        async loadHistory() {
            try {
                const res = await fetch('{{ route("ai-chat.history") }}', {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                this.messages = data.messages || [];
            } catch (e) {}
        },

        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.unread = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading) return;

            this.messages.push({ role: 'user', content: text });
            this.input = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch('{{ route("ai-chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text }),
                });

                const data = await res.json();
                const reply = data.reply || 'Kechirasiz, xatolik yuz berdi.';
                this.messages.push({ role: 'model', content: reply });

                if (!this.open) this.unread++;
            } catch (e) {
                this.messages.push({ role: 'model', content: 'Internet yoki server xatosi. Iltimos, qaytadan urinib ko\'ring.' });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        scrollToBottom() {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTop = el.scrollHeight;
        },
    };
}
</script>
