<x-app-layout>
    <div class="min-h-screen bg-gray-50 pt-24 pb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="hover:text-green-600">Marketplace</a>
                <span>/</span>
                <span class="text-gray-800 font-medium truncate">{{ $product->name }}</span>
            </nav>

            {{-- Flash --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">

                    {{-- Image --}}
                    <div class="h-80 md:h-full bg-gradient-to-br from-green-100 to-green-200">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-8xl">🐄</div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="p-8 flex flex-col">
                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-semibold">
                                {{ $product->category?->name }}
                            </span>
                            <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">
                                {{ $product->status?->name }}
                            </span>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

                        <p class="text-green-600 font-bold text-3xl mt-2">{{ $product->formatted_price }}</p>

                        <p class="text-gray-600 text-sm mt-4 leading-relaxed">{{ $product->description }}</p>

                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-gray-400 text-xs">Tur</p>
                                <p class="font-semibold text-gray-800">{{ $product->type?->name }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-gray-400 text-xs">Rang</p>
                                <p class="font-semibold text-gray-800">{{ $product->color?->name }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-gray-400 text-xs">Yoshi</p>
                                <p class="font-semibold text-gray-800">{{ $product->age }} yosh</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-gray-400 text-xs">Vazni</p>
                                <p class="font-semibold text-gray-800">{{ $product->weight }} kg</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 col-span-2">
                                <p class="text-gray-400 text-xs">Joylashuv</p>
                                <p class="font-semibold text-gray-800">
                                    📍 {{ $product->city?->name }}, {{ $product->region?->name }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 pt-5 border-t border-gray-100 text-sm text-gray-500">
                            <p>Sotuvchi: <span class="font-medium text-gray-800">
                                {{ $product->user?->first_name }} {{ $product->user?->last_name }}
                            </span></p>
                        </div>

                        @auth
                            @if(auth()->id() === $product->user_id)
                                <div class="flex gap-3 mt-6">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="flex-1 text-center bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition text-sm">
                                        Tahrirlash
                                    </a>

                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('E\'lonni o\'chirishni tasdiqlaysizmi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="px-5 py-3 border border-red-500 text-red-500 rounded-xl font-semibold hover:bg-red-500 hover:text-white transition text-sm">
                                            O'chirish
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('products.index') }}"
                    class="text-sm text-green-600 hover:underline font-medium">
                    ← Marketplace ga qaytish
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
