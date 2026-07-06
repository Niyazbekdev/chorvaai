<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-6 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('profile.my_ads') }}</h1>
                <div class="flex gap-3 mt-2">
                    <a href="{{ route('profile.my-products') }}" class="text-sm font-semibold text-green-600 border-b-2 border-green-600 pb-0.5">{{ __('profile.ads_tab') }}</a>
                    <a href="{{ route('profile.favorites') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('profile.favorites_tab') }}</a>
                    <a href="{{ route('conversations.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('profile.messages_tab') }}</a>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    {{ __('profile.profile_settings') }}
                </a>
                <a href="{{ route('products.create') }}"
                   class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    {{ __('profile.new_ad') }}
                </a>
            </div>
        </div>

        {{-- Statistika --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $statItems = [
                    ['📋', $stats['total'],       __('profile.total_ads'),   'text-gray-900'],
                    ['✅', $stats['active'],      __('profile.active'),      'text-green-600'],
                    ['🏷️', $stats['sold'],         __('profile.sold'),        'text-blue-600'],
                    ['⏳', $stats['pending'],     __('profile.pending'),     'text-yellow-600'],
                    ['👁', $stats['total_views'], __('profile.total_views'), 'text-purple-600'],
                    ['💰', number_format($stats['total_value'], 0, '.', ' '), __('profile.active_value'), 'text-emerald-700'],
                ];
            @endphp
            @foreach($statItems as [$icon, $val, $label, $color])
                <div class="bg-white rounded-2xl shadow p-4 text-center">
                    <p class="text-2xl mb-1">{{ $icon }}</p>
                    <p class="text-xl font-bold {{ $color }}">{{ $val }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- E'lonlar --}}
        @if($products->isEmpty())
            <div class="bg-white rounded-2xl shadow p-16 text-center text-gray-400">
                <p class="text-5xl mb-4">📭</p>
                <p class="text-xl font-semibold text-gray-600">{{ __('profile.no_ads') }}</p>
                <p class="text-sm mt-1 mb-6">{{ __('profile.no_ads_hint') }}</p>
                <a href="{{ route('products.create') }}"
                   class="inline-block bg-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                    {{ __('profile.post_first') }}
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($products as $product)
                    @php
                        $statusName  = $product->status?->name ?? '';
                        $statusClass = match($statusName) {
                            'Faol'               => 'bg-green-100 text-green-700',
                            'Sotildi'            => 'bg-gray-200 text-gray-500',
                            "Ko'rib chiqilmoqda" => 'bg-yellow-100 text-yellow-700',
                            default              => 'bg-blue-100 text-blue-600',
                        };
                        $isSold = $statusName === 'Sotildi';
                    @endphp

                    <div class="bg-white rounded-2xl shadow hover:shadow-md transition overflow-hidden">
                        <div class="flex flex-col sm:flex-row">
                            {{-- Image --}}
                            <div class="w-full sm:w-40 h-40 sm:h-auto bg-gradient-to-br from-green-100 to-green-200 flex-shrink-0 overflow-hidden">
                                @if($product->primary_image_url)
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl">🐄</div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 p-4 sm:p-5 flex flex-col">
                                <div class="flex items-start justify-between gap-2 flex-wrap">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                                {{ $product->category?->name ?? '—' }}
                                            </span>
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusClass }}">
                                                {{ $statusName ?: '—' }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-gray-900 text-base">{{ $product->name }}</h4>
                                        <p class="text-green-600 font-bold">{{ $product->formatted_price }}</p>
                                    </div>
                                    <p class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ $product->created_at->format('d.m.Y') }}
                                    </p>
                                </div>

                                {{-- Per-product stats --}}
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        👁 <strong>{{ number_format($product->views_count) }}</strong> {{ __('profile.views') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        ❤️ <strong>{{ $product->favorites_count }}</strong> {{ __('profile.favorites') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        📞 <strong>{{ $product->phone_views_count }}</strong> {{ __('profile.phone_views') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        💬 <strong>{{ $product->conversations_count }}</strong> {{ __('profile.chats') }}
                                    </span>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="text-xs border border-gray-300 text-gray-600 px-3 py-1.5 rounded-xl hover:bg-gray-50 transition">
                                        {{ __('profile.view') }}
                                    </a>
                                    @if(!$isSold)
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="text-xs border border-blue-500 text-blue-500 px-3 py-1.5 rounded-xl hover:bg-blue-500 hover:text-white transition">
                                            {{ __('profile.edit') }}
                                        </a>
                                        <button onclick="openSoldModal({{ $product->id }}, '{{ e($product->name) }}')"
                                            class="text-xs bg-blue-50 border border-blue-400 text-blue-600 px-3 py-1.5 rounded-xl hover:bg-blue-500 hover:text-white transition">
                                            {{ __('profile.sold_btn') }}
                                        </button>
                                    @endif
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('{{ __('profile.delete_confirm') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-xs border border-red-400 text-red-500 px-3 py-1.5 rounded-xl hover:bg-red-500 hover:text-white transition">
                                            {{ __('profile.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Mark as Sold modal --}}
<div id="soldModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeSoldModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-1">{{ __('profile.mark_sold_title') }}</h3>
        <p class="text-sm text-gray-500 mb-4" id="soldProductName"></p>
        <form method="POST" id="soldForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('profile.sold_price') }}</label>
                <input type="number" name="sold_price" min="0"
                    id="soldPrice"
                    class="w-full rounded-xl border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('profile.sale_source') }}</label>
                <select name="source" class="w-full rounded-xl border-gray-300 text-sm">
                    <option value="outside">{{ __('profile.source_outside') }}</option>
                    <option value="phone_call">{{ __('profile.source_phone') }}</option>
                    <option value="platform_chat">{{ __('profile.source_chat') }}</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeSoldModal()"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50">
                    {{ __('profile.cancel') }}
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                    {{ __('profile.confirm') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openSoldModal(productId, productName) {
    const modal = document.getElementById('soldModal');
    document.getElementById('soldProductName').textContent = productName;
    document.getElementById('soldForm').action = `/products/${productId}/mark-sold`;
    modal.classList.remove('hidden');
}
function closeSoldModal() {
    document.getElementById('soldModal').classList.add('hidden');
}
</script>
</x-app-layout>
