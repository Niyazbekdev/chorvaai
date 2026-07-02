<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-16 sm:pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('profile.favorites_title') }}</h1>
                <div class="flex gap-3 mt-2">
                    <a href="{{ route('profile.my-products') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('profile.ads_tab') }}</a>
                    <a href="{{ route('profile.favorites') }}" class="text-sm font-semibold text-green-600 border-b-2 border-green-600 pb-0.5">{{ __('profile.favorites_tab') }}</a>
                    <a href="{{ route('conversations.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('profile.messages_tab') }}</a>
                </div>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="bg-white rounded-2xl shadow p-16 text-center text-gray-400">
                <p class="text-5xl mb-4">🤍</p>
                <p class="text-xl font-semibold text-gray-600">{{ __('profile.no_favorites') }}</p>
                <p class="text-sm mt-1 mb-6">{{ __('profile.no_favorites_hint') }}</p>
                <a href="{{ route('products.index') }}"
                   class="inline-block bg-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                    {{ __('profile.go_marketplace') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden flex flex-col"
                         x-data="{ favorited: true }"
                         x-show="favorited">
                        <div class="h-48 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden relative">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-6xl">🐄</div>
                            @endif

                            {{-- Remove favorite button --}}
                            <button @click.prevent="async function() {
                                    const r = await fetch('{{ route('products.favorite', $product) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                    });
                                    const d = await r.json();
                                    if (!d.favorited) favorited = false;
                                }()"
                                class="absolute top-2 right-2 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow hover:bg-red-50 transition text-base">
                                ❤️
                            </button>
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <div class="flex justify-between items-center mb-2">
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $product->pivot->created_at ? \Carbon\Carbon::parse($product->pivot->created_at)->diffForHumans() : '' }}</span>
                            </div>

                            <h3 class="text-base font-bold text-gray-900 leading-tight line-clamp-2">{{ $product->name }}</h3>
                            @if($product->breed)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $product->breed }}</p>
                            @endif
                            <p class="text-green-600 font-bold text-xl mt-1">{{ $product->formatted_price }}</p>

                            <div class="mt-2 text-gray-500 text-xs">
                                <p>📍 {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') ?: '—' }}</p>
                            </div>

                            <a href="{{ route('products.show', $product) }}"
                                class="mt-4 block text-center border border-green-600 text-green-600 py-2 rounded-xl text-sm font-semibold hover:bg-green-600 hover:text-white transition">
                                {{ __('profile.view_detail') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
