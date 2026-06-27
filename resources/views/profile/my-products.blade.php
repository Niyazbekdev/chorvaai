<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mening e'lonlarim</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Profil sozlamalari
                </a>
                <a href="{{ route('products.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    + Yangi e'lon
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Statistika kartalar --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
                    <span class="text-3xl">📋</span>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-500">Jami e'lonlar</p>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
                    <span class="text-3xl">✅</span>
                    <p class="text-2xl font-bold text-green-600 mt-2">{{ $stats['active'] }}</p>
                    <p class="text-sm text-gray-500">Faol e'lonlar</p>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
                    <span class="text-3xl">🏷️</span>
                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['sold'] }}</p>
                    <p class="text-sm text-gray-500">Sotilgan</p>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
                    <span class="text-3xl">💰</span>
                    <p class="text-2xl font-bold text-emerald-700 mt-2">
                        {{ number_format($stats['total_value'], 0, '.', ' ') }}
                    </p>
                    <p class="text-sm text-gray-500">Faol e'lonlar qiymati (so'm)</p>
                </div>
            </div>

            {{-- Kutilmoqda bo'lsa ko'rsatish --}}
            @if($stats['pending'] > 0)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl text-sm">
                    ⏳ {{ $stats['pending'] }} ta e'loningiz ko'rib chiqilmoqda.
                </div>
            @endif

            {{-- Flash message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- E'lonlar ro'yxati --}}
            @if($products->isEmpty())
                <div class="bg-white rounded-2xl shadow p-16 text-center text-gray-400">
                    <p class="text-5xl mb-4">📭</p>
                    <p class="text-xl font-semibold text-gray-600">Hali e'lon qo'shmagansiz</p>
                    <p class="text-sm mt-1 mb-6">Birinchi e'loningizni joylashtiring va xaridorlarni toping</p>
                    <a href="{{ route('products.create') }}"
                       class="inline-block bg-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                        E'lon joylash
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $product)
                        @php
                            $statusName  = $product->status?->name ?? '';
                            $statusClass = match($statusName) {
                                'Faol'               => 'bg-green-100 text-green-700',
                                'Sotildi'            => 'bg-gray-100 text-gray-500',
                                "Ko'rib chiqilmoqda" => 'bg-yellow-100 text-yellow-700',
                                default              => 'bg-blue-100 text-blue-600',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                            {{-- Image --}}
                            <div class="h-40 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl">🐄</div>
                                @endif
                            </div>

                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ $product->category?->name ?? '—' }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">
                                        {{ $statusName ?: '—' }}
                                    </span>
                                </div>

                                <h4 class="font-bold text-gray-900 text-sm leading-snug mb-1">{{ $product->name }}</h4>
                                <p class="text-green-600 font-bold text-base">{{ $product->formatted_price }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $product->created_at->diffForHumans() }}
                                </p>

                                <div class="flex gap-2 mt-auto pt-3">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="flex-1 text-center text-xs border border-gray-300 text-gray-600 py-2 rounded-xl hover:bg-gray-50 transition">
                                        Ko'rish
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="flex-1 text-center text-xs border border-blue-500 text-blue-500 py-2 rounded-xl hover:bg-blue-500 hover:text-white transition">
                                        Tahrirlash
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('E\'lonni o\'chirishni tasdiqlaysizmi?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-xs border border-red-400 text-red-500 px-3 py-2 rounded-xl hover:bg-red-500 hover:text-white transition">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
