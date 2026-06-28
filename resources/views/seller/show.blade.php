<x-app-layout>

<div class="min-h-screen bg-gray-50 pt-24 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="hover:text-green-600">{{ __('products.page_title') }}</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $seller->first_name }} {{ $seller->last_name }}</span>
        </nav>

        {{-- Seller hero card --}}
        <div class="bg-white rounded-2xl shadow p-6 sm:p-8 mb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">

                {{-- Avatar --}}
                @if($seller->avatar)
                    <img src="{{ $seller->avatarUrl() }}" alt="{{ $seller->first_name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-emerald-100 shadow flex-shrink-0">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                                flex items-center justify-center text-white font-bold text-4xl shadow flex-shrink-0">
                        {{ mb_strtoupper(mb_substr($seller->first_name, 0, 1)) }}
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $seller->first_name }} {{ $seller->last_name }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">{{ __('seller.member_since') }} {{ $seller->created_at->format('d.m.Y') }}</p>

                    <div class="flex flex-wrap justify-center sm:justify-start gap-4 mt-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-emerald-600">{{ $totalActive }}</p>
                            <p class="text-xs text-gray-400">{{ __('seller.active_listings') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-700">{{ $seller->products()->count() }}</p>
                            <p class="text-xs text-gray-400">{{ __('seller.total_listings') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listings --}}
        <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('seller.listings_title') }}</h2>

        @if($products->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <p class="text-5xl mb-4">🐄</p>
                <p class="text-lg font-semibold">{{ __('seller.no_listings') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden flex flex-col group">
                        <div class="h-48 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden relative">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-6xl">
                                    {{ match(true) {
                                        in_array($product->category?->name, ['Sigir','Buqa','Buzoq']) => '🐄',
                                        $product->category?->name === "Qo'y" => '🐑',
                                        $product->category?->name === 'Echki' => '🐐',
                                        $product->category?->name === 'Ot' => '🐴',
                                        $product->category?->name === 'Tuya' => '🐪',
                                        default => '🐾'
                                    } }}
                                </div>
                            @endif
                            @if($product->gender)
                                <span class="absolute top-2 left-2 bg-white bg-opacity-90 text-gray-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                    {{ $product->gender === 'erkak' ? '♂' : '♀' }}
                                </span>
                            @endif
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <div class="flex justify-between items-center mb-2">
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-semibold truncate max-w-[130px]">
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ $product->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="text-base font-bold text-gray-900 leading-tight line-clamp-2">{{ $product->name }}</h3>
                            @if($product->breed)
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $product->breed }}</p>
                            @endif
                            <p class="text-green-600 font-bold text-xl mt-1">{{ $product->formatted_price }}</p>

                            <div class="mt-2 text-gray-500 text-xs space-y-0.5">
                                <p>📍 {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') ?: '—' }}</p>
                                <p>⚖️ {{ $product->weight }} kg &nbsp;·&nbsp; {{ $product->age }} {{ __('products.age_unit') }}</p>
                            </div>

                            <a href="{{ route('products.show', $product) }}"
                               class="mt-4 block text-center border border-green-600 text-green-600 py-2 rounded-xl text-sm font-semibold hover:bg-green-600 hover:text-white transition">
                                {{ __('products.view_detail') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        @endif

    </div>
</div>

</x-app-layout>
