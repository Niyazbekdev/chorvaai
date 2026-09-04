<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-6 pb-20"
     x-data="aiAgent()"
     x-init="init()">

    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-xl font-bold shrink-0">
                🐄
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Bozor tahlili agenti</h1>
                <p class="text-sm text-gray-500">Chorva bozori haqida savollar bering — real ma'lumotlar asosida javob olasiz</p>
            </div>
        </div>

        {{-- Chat window --}}
        <div class="bg-white rounded-2xl shadow flex flex-col" style="height: calc(100vh - 230px); min-height: 420px;">

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-4 scroll-smooth" x-ref="chatBox">

                {{-- Welcome message --}}
                <div class="flex gap-3 items-start">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-base shrink-0">🤖</div>
                    <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-800 max-w-xl leading-relaxed">
                        Salom! Men ChorvaAI bozor tahlili agentiman. 🐄<br>
                        Chorva mollar narxi, trendlar va viloyatlar bo'yicha savollar bering.
                        <br><br>
                        <span class="text-gray-500 text-xs">Masalan:</span>
                    </div>
                </div>

                {{-- Suggestion chips --}}
                <div class="flex flex-wrap gap-2 pl-11" x-show="messages.length === 0">
                    <template x-for="chip in suggestions" :key="chip">
                        <button @click="sendMessage(chip)"
                                class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-1.5 rounded-full hover:bg-emerald-100 transition font-medium">
                            <span x-text="chip"></span>
                        </button>
                    </template>
                </div>

                {{-- Conversation messages --}}
                <template x-for="(msg, idx) in messages" :key="idx">
                    <div :class="msg.role === 'user' ? 'flex flex-row-reverse gap-3 items-start' : 'flex gap-3 items-start'">

                        {{-- Avatar --}}
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0"
                             :class="msg.role === 'user' ? 'bg-green-600 text-white font-bold' : 'bg-gray-100'">
                            <span x-text="msg.role === 'user' ? '{{ substr(auth()->user()->name ?? 'U', 0, 1) }}' : '🤖'"></span>
                        </div>

                        {{-- User bubble --}}
                        <div x-show="msg.role === 'user'"
                             class="max-w-xl text-sm leading-relaxed whitespace-pre-wrap px-4 py-3 rounded-2xl bg-green-600 text-white rounded-tr-sm"
                             x-text="msg.content">
                        </div>

                        {{-- Assistant bubble --}}
                        <div x-show="msg.role === 'assistant'"
                             class="max-w-xl text-sm leading-relaxed px-4 py-3 rounded-2xl bg-gray-100 text-gray-800 rounded-tl-sm ai-prose"
                             x-html="renderMarkdown(msg.content)">
                        </div>
                    </div>
                </template>

                {{-- Typing indicator --}}
                <div x-show="loading" class="flex gap-3 items-start">
                    <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-sm shrink-0">🤖</div>
                    <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex gap-1 items-center h-4">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100"></div>

            {{-- Input --}}
            <div class="p-4">
                <form @submit.prevent="sendMessage()" class="flex gap-2 items-end">
                    <textarea
                        x-model="input"
                        x-ref="inputBox"
                        @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                        :disabled="loading"
                        rows="1"
                        placeholder="Savol yozing... (Enter — yuborish, Shift+Enter — yangi qator)"
                        class="flex-1 resize-none rounded-xl border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5 leading-relaxed disabled:opacity-50"
                        style="max-height: 120px; overflow-y: auto;"
                        @input="autoResize($event.target)"
                    ></textarea>

                    <button type="submit"
                            :disabled="loading || !input.trim()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-4 py-2.5 transition disabled:opacity-40 shrink-0">
                        <svg x-show="!loading" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg x-show="loading" class="animate-spin" width="20" height="20" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </button>
                </form>

                <p class="text-xs text-gray-400 mt-2 text-center">
                    AI ma'lumotlari marketplace bazasidan olinadi · Oldingi suhbat saqlanmaydi
                </p>
            </div>

        </div>

    </div>
</div>

@push('styles')
<style>
.ai-prose ul { list-style: disc; padding-left: 1.2em; margin: 0.4em 0; }
.ai-prose ol { list-style: decimal; padding-left: 1.2em; margin: 0.4em 0; }
.ai-prose li { margin: 0.15em 0; }
.ai-prose p { margin: 0.4em 0; }
.ai-prose p:first-child { margin-top: 0; }
.ai-prose p:last-child { margin-bottom: 0; }
.ai-prose strong { font-weight: 700; }
.ai-prose em { font-style: italic; }
.ai-prose code { background: rgba(0,0,0,.08); padding: 0 4px; border-radius: 4px; font-size: .85em; }
.ai-prose hr { border: none; border-top: 1px solid rgba(0,0,0,.1); margin: 0.5em 0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@13/marked.min.js"></script>
<script>
function aiAgent() {
    return {
        messages: [],
        input: '',
        loading: false,
        suggestions: [
            'Hozir qoramollarning o\'rtacha narxi qancha?',
            'Qaysi viloyatda qo\'ylar arzonroq?',
            'So\'nggi 3 oyda narxlar o\'zgarganmi?',
            'Barcha kategoriyalar narxini solishtir',
            'Otlarning so\'nggi oy narxi qancha?',
        ],

        init() {
            if (window.marked) {
                marked.setOptions({ breaks: true, gfm: true });
            }
            this.$nextTick(() => this.$refs.inputBox?.focus());
        },

        renderMarkdown(text) {
            if (window.marked) {
                return marked.parse(text);
            }
            return text.replace(/\n/g, '<br>');
        },

        async sendMessage(text = null) {
            const content = (text ?? this.input).trim();
            if (!content || this.loading) return;

            this.input = '';
            this.$nextTick(() => {
                if (this.$refs.inputBox) {
                    this.$refs.inputBox.style.height = 'auto';
                }
            });

            this.messages.push({ role: 'user', content });
            this.loading = true;
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ messages: this.messages }),
                });

                if (res.status === 429) {
                    this.messages.push({
                        role: 'assistant',
                        content: 'So\'rovlar chegarasiga yetdingiz. Iltimos, bir daqiqa kutib, qayta urinib ko\'ring.',
                    });
                    return;
                }

                const data = await res.json();
                this.messages.push({ role: 'assistant', content: data.reply });
            } catch (e) {
                this.messages.push({
                    role: 'assistant',
                    content: 'Xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.',
                });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const box = this.$refs.chatBox;
                if (box) box.scrollTop = box.scrollHeight;
            });
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        },
    };
}
</script>
@endpush

</x-app-layout>
