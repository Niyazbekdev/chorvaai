<x-app-layout>
@push('styles')
<style>
.gallery-thumb { transition: all .2s; cursor: pointer; }
.gallery-thumb.active { border-color: #10b981; opacity: 1; }
.gallery-thumb:not(.active) { opacity: .6; }
.gallery-thumb:hover { opacity: 1; }
.contact-reveal { animation: fadeIn .3s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
.stat-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:10px 14px; }
.product-gallery-main { height: 420px; }
@media (max-width: 640px) {
    .product-gallery-main { height: 260px; }
}
</style>
@endpush

<div class="min-h-screen bg-gray-50 pt-6 pb-16"
     x-data="{
        activeImg: 0,
        favorited: {{ $isFavorited ? 'true' : 'false' }},
        favCount: {{ $product->favorites()->count() }},
        showPhone: false,
        phone: '',
        showMsg: false,
        loadingFav: false,
        loadingPhone: false,

        async toggleFav() {
            @auth
            if (this.loadingFav) return;
            this.loadingFav = true;
            try {
                const r = await fetch('{{ route('products.favorite', $product) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const d = await r.json();
                this.favorited = d.favorited;
                this.favCount  = d.count;
            } finally { this.loadingFav = false; }
            @else
            window.location = '{{ route('login') }}';
            @endauth
        },

        async revealPhone() {
            if (this.showPhone) return;
            this.loadingPhone = true;
            try {
                const r = await fetch('{{ route('products.contact-event', $product) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ type: 'phone_view' })
                });
                const d = await r.json();
                this.phone = d.phone;
                this.showPhone = true;
            } finally { this.loadingPhone = false; }
        },

        async trackCall() {
            await fetch('{{ route('products.contact-event', $product) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'call_click' })
            });
        },

        async trackMessage() {
            await fetch('{{ route('products.contact-event', $product) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'message_click' })
            });
        }
     }">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-5 flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="hover:text-green-600">{{ __('products.page_title') }}</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->id]) }}"
                   class="hover:text-green-600">{{ $product->category->name }}</a>
                <span>/</span>
            @endif
            <span class="text-gray-800 font-medium truncate">{{ $product->name }}</span>
        </nav>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Gallery + Details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Gallery --}}
                @php
                    $gallery = $product->gallery;
                @endphp
                <div class="bg-white rounded-2xl shadow overflow-hidden">
                    {{-- Main image --}}
                    <div class="relative bg-gradient-to-br from-green-100 to-green-200 product-gallery-main">
                        @if(count($gallery) > 0)
                            @foreach($gallery as $i => $img)
                                <img src="{{ Storage::url($img) }}" alt="{{ $product->name }}"
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                     :class="activeImg === {{ $i }} ? 'opacity-100' : 'opacity-0'">
                            @endforeach
                        @else
                            <div class="w-full h-full flex items-center justify-center text-9xl">🐄</div>
                        @endif

                        {{-- Status badge --}}
                        @if($product->isSold())
                            <div class="absolute top-4 left-4 bg-gray-800 bg-opacity-80 text-white text-sm font-bold px-4 py-1.5 rounded-full">
                                {{ __('products.sold_badge') }}
                            </div>
                        @endif

                        {{-- Views --}}
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-1">
                            👁 {{ number_format($product->views_count) }} {{ __('products.views_count') }}
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    @if(count($gallery) > 1)
                        <div class="flex gap-2 p-3 overflow-x-auto bg-gray-50">
                            @foreach($gallery as $i => $img)
                                <button @click="activeImg = {{ $i }}"
                                        class="gallery-thumb flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2"
                                        :class="activeImg === {{ $i }} ? 'border-green-500 opacity-100' : 'border-gray-200 opacity-60'">
                                    <img src="{{ Storage::url($img) }}" alt="{{ $product->name }} {{ $i + 1 }}"
                                         class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Product info --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($product->category)
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-semibold">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        @if($product->status)
                            <span class="text-xs px-3 py-1 rounded-full font-semibold
                                {{ $product->status->name === 'Faol' ? 'bg-green-100 text-green-700' :
                                   ($product->status->name === 'Sotildi' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $product->status->name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-green-600 font-bold text-3xl mt-2">{{ $product->formatted_price }}</p>

                    <p class="text-gray-600 text-sm mt-4 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>

                    {{-- Details grid --}}
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        @if($product->gender)
                            <div class="stat-card">
                                <p class="text-gray-400 text-xs mb-0.5">{{ __('products.gender_label') }}</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $product->gender === 'erkak' ? '♂ Erkak' : '♀ Urgochi' }}
                                </p>
                            </div>
                        @endif
                        <div class="stat-card">
                            <p class="text-gray-400 text-xs mb-0.5">{{ __('products.age_label') }}</p>
                            <p class="font-semibold text-gray-800">{{ $product->age }} {{ __('products.age_unit') }}</p>
                        </div>
                        <div class="stat-card">
                            <p class="text-gray-400 text-xs mb-0.5">{{ __('products.weight_label') }}</p>
                            <p class="font-semibold text-gray-800">{{ $product->weight }} kg</p>
                        </div>
                        @if($product->color)
                            <div class="stat-card">
                                <p class="text-gray-400 text-xs mb-0.5">{{ __('products.color_label') }}</p>
                                <p class="font-semibold text-gray-800">{{ $product->color->name }}</p>
                            </div>
                        @endif
                        <div class="stat-card col-span-2 sm:col-span-3">
                            <p class="text-gray-400 text-xs mb-0.5">{{ __('products.location_label') }}</p>
                            <p class="font-semibold text-gray-800">
                                {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') ?: '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Location map --}}
                    @php
                        $address = collect([$product->city?->name, $product->region?->name])->filter()->implode(', ');
                    @endphp
                    <div class="mt-5 rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                        @if($product->latitude && $product->longitude)
                            <div id="miniMap" style="height:280px"></div>
                        @else
                            <div class="flex items-center justify-center bg-gray-100" style="height:140px">
                                <div class="text-center text-gray-400">
                                    <div class="text-4xl mb-2">📍</div>
                                    <p class="text-sm font-medium">{{ $address ?: __('products.no_location') }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="p-3 bg-white flex gap-2">
                            @if($product->latitude && $product->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $product->latitude }},{{ $product->longitude }}"
                                   target="_blank" rel="noopener"
                                   class="flex-1 flex items-center justify-center gap-2 bg-blue-600 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-blue-700 active:bg-blue-800 transition select-none">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Yo'l ko'rsatish
                                </a>
                                <a href="https://yandex.com/maps/?rtext=~{{ $product->latitude }},{{ $product->longitude }}&rtt=auto"
                                   target="_blank" rel="noopener"
                                   class="flex items-center justify-center gap-1.5 border border-gray-300 text-gray-700 px-4 py-3.5 rounded-xl font-semibold text-sm hover:bg-gray-50 active:bg-gray-100 transition select-none whitespace-nowrap">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Yandex
                                </a>
                            @elseif($address)
                                <a href="https://www.google.com/maps/search/{{ urlencode($address) }}"
                                   target="_blank" rel="noopener"
                                   class="flex-1 flex items-center justify-center gap-2 bg-blue-600 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-blue-700 active:bg-blue-800 transition select-none">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Xaritada ko'rish
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Seller + Actions --}}
            <div class="space-y-4">

                {{-- Seller card --}}
                <a href="{{ route('seller.show', $product->user) }}"
                   class="block bg-white rounded-2xl shadow p-5 hover:shadow-lg transition group">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('products.seller') }}</h3>
                    <div class="flex items-center gap-3">
                        @if($product->user?->avatar)
                            <img src="{{ $product->user->avatar_url }}" alt="{{ $product->user->first_name }}"
                                 class="w-12 h-12 rounded-full object-cover flex-shrink-0 border border-gray-200 group-hover:border-emerald-400 transition">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                                        flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($product->user?->first_name ?? 'S', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-emerald-600 transition">
                                {{ $product->user?->first_name }} {{ $product->user?->last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ __('products.advertiser') }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">
                        {{ __('products.posted_on') }} {{ $product->created_at->format('d.m.Y') }}
                    </p>
                    <p class="text-xs text-emerald-600 font-semibold mt-2 opacity-0 group-hover:opacity-100 transition">
                        {{ __('products.view_seller') }} →
                    </p>
                </a>

                {{-- Favorite button --}}
                <button @click="toggleFav()" :disabled="loadingFav"
                        class="w-full flex items-center justify-center gap-2 py-3 rounded-2xl border-2 font-semibold text-sm transition"
                        :class="favorited
                            ? 'bg-red-50 border-red-400 text-red-600 hover:bg-red-100'
                            : 'bg-white border-gray-300 text-gray-600 hover:border-red-400 hover:text-red-500'">
                    <span x-text="favorited ? '❤️' : '🤍'" class="text-lg"></span>
                    <span x-text="favorited ? '{{ __('products.remove_favorite') }}' : '{{ __('products.add_favorite') }}'"></span>
                    <span x-show="favCount > 0" x-text="'('+favCount+')'" class="text-xs opacity-70"></span>
                </button>

                {{-- Contact section --}}
                @if(!$product->isSold())
                    @auth
                        @if(auth()->id() === $product->user_id)
                            {{-- Owner: edit/delete/mark sold --}}
                            <div class="bg-white rounded-2xl shadow p-5 space-y-3">
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">{{ __('products.manage') }}</p>
                                <a href="{{ route('products.edit', $product) }}"
                                    class="block text-center bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition text-sm">
                                    {{ __('products.edit_btn') }}
                                </a>
                                <button @click="$dispatch('open-sold-modal')"
                                    class="block w-full text-center bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                                    {{ __('products.mark_sold_btn') }}
                                </button>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                    onsubmit="return confirm('{{ __('products.delete_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-full py-2.5 border border-red-400 text-red-500 rounded-xl font-semibold hover:bg-red-500 hover:text-white transition text-sm">
                                        {{ __('products.delete_btn') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Buyer: phone + chat --}}
                            <div class="bg-white rounded-2xl shadow p-5 space-y-3">
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-1">{{ __('products.contact') }}</p>

                                {{-- Phone reveal --}}
                                <div>
                                    <template x-if="!showPhone">
                                        <button @click="revealPhone()" :disabled="loadingPhone"
                                                class="w-full flex items-center justify-center gap-2 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition text-sm">
                                            <span x-show="!loadingPhone">{{ __('products.show_phone') }}</span>
                                            <span x-show="loadingPhone">...</span>
                                        </button>
                                    </template>
                                    <template x-if="showPhone">
                                        <div class="contact-reveal text-center">
                                            <a :href="'tel:' + phone" @click="trackCall()"
                                               class="block bg-green-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-green-700 transition"
                                               x-text="phone"></a>
                                            <p class="text-xs text-gray-400 mt-1">{{ __('products.call_hint') }}</p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Chat --}}
                                @if($existingConversation)
                                    <a href="{{ route('conversations.show', $existingConversation) }}"
                                       @click="trackMessage()"
                                       class="flex items-center justify-center gap-2 w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                                        {{ __('products.continue_chat') }}
                                    </a>
                                @else
                                    <button @click="trackMessage(); showMsg = !showMsg"
                                            class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                                        {{ __('products.send_message') }}
                                    </button>
                                    <div x-show="showMsg" x-transition class="mt-2">
                                        <form method="POST" action="{{ route('conversations.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <textarea name="message" rows="3" required
                                                placeholder="{{ __('products.write_message') }}"
                                                class="w-full rounded-xl border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                            <button type="submit"
                                                class="mt-2 w-full bg-blue-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                                                {{ __('products.send') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        {{-- Guest --}}
                        <div class="bg-white rounded-2xl shadow p-5">
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('products.contact') }}</p>
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-center">
                                <p class="text-sm text-blue-700 font-medium mb-3">{{ __('products.login_to_contact') }}</p>
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('login') }}"
                                       class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                                        {{ __('nav.login') }}
                                    </a>
                                    <a href="{{ route('register') }}"
                                       class="px-5 py-2 border border-blue-600 text-blue-600 rounded-xl text-sm font-semibold hover:bg-blue-50 transition">
                                        {{ __('nav.register') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                @else
                    <div class="bg-gray-100 rounded-2xl p-5 text-center text-gray-500">
                        <p class="text-2xl mb-2">🏷️</p>
                        <p class="font-semibold">{{ __('products.product_sold') }}</p>
                    </div>
                @endif

                {{-- Quick stats --}}
                <div class="bg-white rounded-2xl shadow p-4 grid grid-cols-2 gap-3 text-center text-sm">
                    <div>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($product->views_count) }}</p>
                        <p class="text-xs text-gray-400">{{ __('products.views_stat') }}</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-800" x-text="favCount">{{ $product->favorites()->count() }}</p>
                        <p class="text-xs text-gray-400">{{ __('products.favorites_stat') }}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('products.index') }}" class="text-sm text-green-600 hover:underline font-medium">
                {{ __('products.back') }}
            </a>
        </div>
    </div>

    {{-- Mark as Sold modal --}}
    @auth
        @if(auth()->id() === $product->user_id)
            <div x-data="{ open: false }" @open-sold-modal.window="open = true">
                <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black bg-opacity-50" @click="open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md">
                        <h3 class="text-lg font-bold mb-4">{{ __('products.sold_modal_title') }}</h3>
                        <form method="POST" action="{{ route('products.mark-sold', $product) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.sold_price_label') }}</label>
                                <input type="number" name="sold_price" value="{{ $product->price }}" min="0"
                                    class="w-full rounded-xl border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.sale_source_label') }}</label>
                                <select name="source" class="w-full rounded-xl border-gray-300 text-sm">
                                    <option value="outside">{{ __('products.source_outside') }}</option>
                                    <option value="phone_call">{{ __('products.source_phone') }}</option>
                                    <option value="platform_chat">{{ __('products.source_chat') }}</option>
                                </select>
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="open = false"
                                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50">
                                    {{ __('products.cancel') }}
                                </button>
                                <button type="submit"
                                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                                    {{ __('products.confirm') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>

@if($product->latitude && $product->longitude)
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isMobile = window.innerWidth < 768;
    const map = L.map('miniMap', {
        scrollWheelZoom: false,
        dragging: !isMobile,
        tap: false,
        zoomControl: !isMobile,
    }).setView([{{ $product->latitude }}, {{ $product->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const icon = L.divIcon({
        className: '',
        html: '<div style="background:#2563eb;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>',
        iconSize: [16, 16],
        iconAnchor: [8, 8],
    });

    L.marker([{{ $product->latitude }}, {{ $product->longitude }}], { icon })
        .addTo(map)
        .bindPopup('<b>{{ e($product->name) }}</b><br><span style="font-size:.8rem;color:#6b7280">{{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') }}</span>')
        .openPopup();

    // Mobilda xaritaga turtish → Google Maps ochish
    if (isMobile) {
        document.getElementById('miniMap').addEventListener('click', function () {
            window.open('https://www.google.com/maps/dir/?api=1&destination={{ $product->latitude }},{{ $product->longitude }}', '_blank');
        });
        document.getElementById('miniMap').style.cursor = 'pointer';
    }
});
</script>
@endpush
@endif
</x-app-layout>
