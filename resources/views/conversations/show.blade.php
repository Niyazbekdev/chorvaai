<x-app-layout>
    <div class="bg-gray-50 min-h-screen">

        {{-- Chat header bar --}}
        @php $other = $conversation->other($user); @endphp
        <div class="bg-white border-b border-gray-200 sticky top-[64px] sm:top-[76px] z-40">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('conversations.index') }}" class="text-gray-400 hover:text-gray-600 text-lg">←</a>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                                flex items-center justify-center text-white font-bold text-base flex-shrink-0">
                        {{ mb_strtoupper(mb_substr($other->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-base leading-tight">
                            {{ $other->first_name }} {{ $other->last_name }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $other->phone }}</p>
                    </div>
                </div>
                <a href="{{ route('products.show', $conversation->product) }}"
                   class="text-xs text-green-600 font-medium hover:underline hidden sm:block truncate max-w-xs">
                    📦 {{ $conversation->product?->name }}
                </a>
            </div>
        </div>

        <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 flex flex-col" style="min-height: calc(100vh - 120px)">

            {{-- Product info banner --}}
            <a href="{{ route('products.show', $conversation->product) }}"
               class="flex items-center gap-3 bg-white rounded-2xl shadow p-3 mb-4 hover:shadow-md transition">
                <div class="w-14 h-14 rounded-xl overflow-hidden bg-green-100 flex-shrink-0">
                    @if($conversation->product?->image)
                        <img src="{{ Storage::url($conversation->product->image) }}"
                             class="w-full h-full object-cover" alt="">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl">🐄</div>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $conversation->product?->name }}</p>
                    <p class="text-green-600 font-bold text-sm">{{ $conversation->product?->formatted_price }}</p>
                </div>
                <span class="ml-auto text-xs text-gray-400 flex-shrink-0">{{ __('conversations.view') }}</span>
            </a>

            {{-- Messages --}}
            <div class="flex-1 space-y-3 mb-4" id="messageList">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === $user->id; @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs sm:max-w-sm lg:max-w-md">
                            @if(!$isMe)
                                <p class="text-xs text-gray-400 mb-1 ml-1">{{ $msg->sender->first_name }}</p>
                            @endif
                            <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed
                                {{ $isMe
                                    ? 'bg-green-600 text-white rounded-br-sm'
                                    : 'bg-white shadow text-gray-800 rounded-bl-sm' }}">
                                {{ $msg->message }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1 {{ $isMe ? 'text-right mr-1' : 'ml-1' }}">
                                {{ $msg->created_at->format('H:i') }}
                                @if($isMe)· {{ $msg->read_at ? '✓✓' : '✓' }}@endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8 text-sm">{{ __('conversations.start_chat') }}</div>
                @endforelse
                <div id="bottom"></div>
            </div>

            {{-- Message input --}}
            <form id="msgForm"
                  action="{{ route('messages.store', $conversation) }}"
                  method="post"
                  class="bg-white rounded-2xl shadow p-3 flex gap-3 items-end sticky bottom-4">
                @csrf
                <textarea name="message" id="msgInput" rows="1" required
                    placeholder="{{ __('conversations.write_message') }}"
                    oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"
                    class="flex-1 rounded-xl border-gray-200 text-sm focus:ring-green-500 focus:border-green-500
                           resize-none overflow-hidden leading-relaxed"
                    style="min-height: 40px;"></textarea>
                <button type="submit" id="sendBtn"
                    class="bg-green-600 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition
                           text-sm flex-shrink-0 self-end disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('conversations.send') }}
                </button>
            </form>

        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
const MY_ID   = {{ $user->id }};
const CONV_ID = {{ $conversation->id }};
const msgList = document.getElementById('messageList');
const bottom  = document.getElementById('bottom');
const msgForm = document.getElementById('msgForm');
const msgInput = document.getElementById('msgInput');
const sendBtn  = document.getElementById('sendBtn');

function scrollDown() {
    bottom.scrollIntoView({ behavior: 'smooth' });
}
scrollDown();

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function buildBubble(data) {
    const isMe = data.sender_id === MY_ID;
    return `
    <div class="flex ${isMe ? 'justify-end' : 'justify-start'}">
        <div class="max-w-xs sm:max-w-sm lg:max-w-md">
            ${!isMe ? `<p class="text-xs text-gray-400 mb-1 ml-1">${escHtml(data.sender_name)}</p>` : ''}
            <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed
                ${isMe ? 'bg-green-600 text-white rounded-br-sm' : 'bg-white shadow text-gray-800 rounded-bl-sm'}">
                ${escHtml(data.message)}
            </div>
            <p class="text-xs text-gray-400 mt-1 ${isMe ? 'text-right mr-1' : 'ml-1'}">
                ${escHtml(data.created_at)}${isMe ? ' · ✓' : ''}
            </p>
        </div>
    </div>`;
}

function appendBubble(data) {
    const div = document.createElement('div');
    div.innerHTML = buildBubble(data);
    bottom.before(div.firstElementChild);
    scrollDown();
}

// AJAX form submit — sahifa yangilanmaydi
msgForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const text = msgInput.value.trim();
    if (!text) return;

    sendBtn.disabled = true;
    msgInput.disabled = true;

    try {
        const res = await fetch(msgForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text }),
        });

        if (!res.ok) throw new Error('send_failed');

        const data = await res.json();
        msgInput.value = '';
        msgInput.style.height = 'auto';
        appendBubble(data);
    } catch (_) {
        alert('Xabar yuborishda xatolik. Qaytadan urinib ko\'ring.');
    } finally {
        sendBtn.disabled = false;
        msgInput.disabled = false;
        msgInput.focus();
    }
});

// Enter tugmasi (Shift+Enter yangi qator)
msgInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        msgForm.dispatchEvent(new Event('submit', { cancelable: true }));
    }
});

// Pusher — boshqa tomonning xabarlarini real-time qabul qilish
if (window.Echo) {
    window.Echo.private(`conversation.${CONV_ID}`)
        .listen('.MessageSent', (e) => {
            if (e.sender_id === MY_ID) return; // o'z xabarimiz AJAX orqali allaqachon ko'rsatildi
            appendBubble(e);
        });
}
</script>
@endpush
