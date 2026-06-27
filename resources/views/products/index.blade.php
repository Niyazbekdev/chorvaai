<x-app-layout>
    <div class="min-h-screen bg-gray-50 pt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Marketplace</h1>
                    <p class="text-gray-500 mt-1">Sotuvdagi chorva mollari</p>
                </div>

                <button onclick="document.getElementById('filterBox').classList.toggle('hidden')"
                    class="border border-green-600 text-green-600 px-5 py-3 rounded-xl font-semibold hover:bg-green-50 text-sm self-start sm:self-auto">
                    ☰ Filter
                </button>
            </div>

            {{-- Flash message --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter panel --}}
            <div id="filterBox" class="hidden bg-white rounded-2xl shadow p-6 mb-8">
                <h2 class="text-lg font-bold mb-4">Qidiruv va filter</h2>

                <form method="GET" action="{{ route('products.index') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <select name="category"
                            class="rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Barcha kategoriyalar</option>
                            @foreach($categories as $parent)
                                <option value="{{ $parent->id }}" @selected(request('category') == $parent->id)
                                    class="font-semibold">
                                    {{ $parent->name }}
                                </option>
                                @foreach($parent->children as $child)
                                    <option value="{{ $child->id }}" @selected(request('category') == $child->id)>
                                        &nbsp;&nbsp;— {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>

                        <select name="region"
                            class="rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                            <option value="">Barcha viloyatlar</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected(request('region') == $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>

                        <div class="flex gap-2">
                            <input type="number" name="price_from" placeholder="Narxdan"
                                value="{{ request('price_from') }}"
                                class="w-1/2 rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                            <input type="number" name="price_to" placeholder="Narxgacha"
                                value="{{ request('price_to') }}"
                                class="w-1/2 rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="flex-1 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 text-sm py-2">
                                Qidirish
                            </button>
                            <a href="{{ route('products.index') }}"
                                class="flex-1 text-center border border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 text-sm py-2 flex items-center justify-center">
                                Tozalash
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Product cards --}}
            @if($products->isEmpty())
                <div class="text-center py-24 text-gray-400">
                    <p class="text-5xl mb-4">🐄</p>
                    <p class="text-xl font-semibold">E'lonlar topilmadi</p>
                    <p class="text-sm mt-1">Filter shartlarini o'zgartiring yoki yangi e'lon qo'shing</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden flex flex-col">
                            <div class="h-44 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-6xl">🐄</div>
                                @endif
                            </div>

                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ $product->category?->name ?? '—' }}
                                    </span>
                                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                        {{ $product->status?->name ?? '—' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $product->name }}</h3>

                                <p class="text-green-600 font-bold text-xl mt-1">
                                    {{ $product->formatted_price }}
                                </p>

                                <div class="mt-3 text-gray-500 text-xs space-y-1">
                                    <p>📍 {{ $product->city?->name }}, {{ $product->region?->name }}</p>
                                    <p>⚖️ {{ $product->weight }} kg &nbsp;·&nbsp; {{ $product->age }} yosh</p>
                                    <p>🎨 {{ $product->color?->name }} &nbsp;·&nbsp; {{ $product->type?->name }}</p>
                                </div>

                                <a href="{{ route('products.show', $product) }}"
                                    class="mt-4 block text-center border border-green-600 text-green-600 py-2 rounded-xl text-sm font-semibold hover:bg-green-600 hover:text-white transition">
                                    Batafsil ko'rish
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
