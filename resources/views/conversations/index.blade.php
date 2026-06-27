<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('conversations.title') }}</h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($conversations->isEmpty())
                <div class="bg-white rounded-2xl shadow p-16 text-center text-gray-400">
                    <p class="text-5xl mb-4">💬</p>
                    <p class="text-xl font-semibold text-gray-600">{{ __('conversations.no_messages') }}</p>
                    <p class="text-sm mt-1">{{ __('conversations.no_messages_hint') }}</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-block mt-5 bg-green-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                        {{ __('conversations.go_marketplace') }}
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($conversations as $conv)
                        @php
                            $other      = $conv->other(auth()->user());
                            $lastMsg    = $conv->messages->first();
                            $unread     = $conv->messages
                                ->where('sender_id', '!=', $userId)
                                ->whereNull('read_at')
                                ->count();
                            $isSeller   = $conv->seller_id === $userId;
                        @endphp
                        <a href="{{ route('conversations.show', $conv) }}"
                           class="flex items-start gap-4 bg-white rounded-2xl shadow hover:shadow-md transition p-4
                                  {{ $unread > 0 ? 'border-l-4 border-blue-500' : '' }}">

                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                                        flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($other->first_name, 0, 1)) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $other->first_name }} {{ $other->last_name }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-2">
                                            {{ $isSeller ? __('conversations.buyer') : __('conversations.seller') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($unread > 0)
                                            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                                {{ $unread }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-400">
                                            {{ $conv->last_message_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Product name --}}
                                <p class="text-xs text-green-600 font-medium mt-0.5 truncate">
                                    📦 {{ $conv->product?->name }}
                                </p>

                                {{-- Last message preview --}}
                                @if($lastMsg)
                                    <p class="text-sm text-gray-500 mt-1 truncate">
                                        @if($lastMsg->sender_id === $userId)
                                            <span class="text-gray-400">{{ __('conversations.you') }}</span>
                                        @endif
                                        {{ $lastMsg->message }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
