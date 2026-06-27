<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil sozlamalari
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile info --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Password --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete account --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            {{-- Mening e'lonlarim --}}
            <div id="my-products" class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Mening e'lonlarim</h3>
                    <a href="{{ route('products.create') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                        + Yangi e'lon
                    </a>
                </div>

                @if($products->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <p class="text-4xl mb-3">📋</p>
                        <p class="text-lg font-semibold">Hali e'lon qo'shmagansiz</p>
                        <a href="{{ route('products.create') }}"
                           class="inline-block mt-4 text-green-600 font-semibold hover:underline">
                            Birinchi e'loningizni joylashtiring →
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($products as $product)
                            <div class="border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md transition flex flex-col">
                                {{-- Image --}}
                                <div class="h-36 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden">
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
                                        @php
                                            $statusColor = match($product->status?->name) {
                                                'Faol'   => 'bg-green-100 text-green-700',
                                                'Sotildi'=> 'bg-gray-100 text-gray-500',
                                                default  => 'bg-yellow-100 text-yellow-700',
                                            };
                                        @endphp
                                        <span class="text-xs px-2 py-1 rounded-full {{ $statusColor }}">
                                            {{ $product->status?->name ?? '—' }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-gray-900 text-sm leading-snug mb-1">{{ $product->name }}</h4>
                                    <p class="text-green-600 font-bold text-base">{{ $product->formatted_price }}</p>

                                    <div class="flex gap-2 mt-3 mt-auto pt-3">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="flex-1 text-center text-xs border border-gray-300 text-gray-600 py-1.5 rounded-lg hover:bg-gray-50 transition">
                                            Ko'rish
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="flex-1 text-center text-xs border border-blue-500 text-blue-500 py-1.5 rounded-lg hover:bg-blue-500 hover:text-white transition">
                                            Tahrirlash
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm('E\'lonni o\'chirishni tasdiqlaysizmi?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-xs border border-red-400 text-red-500 px-3 py-1.5 rounded-lg hover:bg-red-500 hover:text-white transition">
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
    </div>
</x-app-layout>
