<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('conversations.index') }}" class="text-gray-400 hover:text-gray-600">
                    ←
                </a>
                @php $other = $conversation->other($user); @endphp
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                            flex items-center justify-center text-white font-bold text-base">
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
    </x-slot>

    <div class="bg-gray-50 min-h-screen flex flex-col" style="padding-top: 0;">
        <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col flex-1 py-6">

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
                <span class="ml-auto text-xs text-gray-400">Ko'rish →</span>
            </a>

            {{-- Messages --}}
            <div class="flex-1 space-y-3 mb-4 overflow-y-auto" id="messageList">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === $user->id; @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs sm:max-w-sm lg:max-w-md">
                            @if(!$isMe)
                                <p class="text-xs text-gray-400 mb-1 ml-1">{{ $msg->sender->first_name }}</p>
                            @endif
                            <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed
                                {{ $isMe
                                    ? 'bg-blue-600 text-white rounded-br-sm'
                                    : 'bg-white shadow text-gray-800 rounded-bl-sm' }}">
                                {{ $msg->message }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1 {{ $isMe ? 'text-right mr-1' : 'ml-1' }}">
                                {{ $msg->created_at->format('H:i') }}
                                @if($isMe)
                                    · {{ $msg->read_at ? '✓✓' : '✓' }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8 text-sm">
                        Suhbat boshlang
                    </div>
                @endforelse
                <div id="bottom"></div>
            </div>

            {{-- Message input --}}
            <form method="POST" action="{{ route('messages.store', $conversation) }}"
                  class="bg-white rounded-2xl shadow p-3 flex gap-3 items-end sticky bottom-4">
                @csrf
                <textarea name="message" rows="1" required
                    placeholder="Xabar yozing..."
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit()}"
                    oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"
                    class="flex-1 rounded-xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500
                           resize-none overflow-hidden leading-relaxed"
                    style="min-height: 40px;"></textarea>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition
                           text-sm flex-shrink-0 self-end">
                    Yuborish
                </button>
            </form>

        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    // Auto scroll to bottom on load
    const list = document.getElementById('messageList');
    if (list) list.scrollTop = list.scrollHeight;
</script>
@endpush
