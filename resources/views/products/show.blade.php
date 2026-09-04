<x-app-layout>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.gallery-thumb { cursor:pointer; border-radius:10px; overflow:hidden; border:2px solid transparent; transition:border-color .15s,opacity .15s; opacity:.55; flex-shrink:0; }
.gallery-thumb.active { border-color:#1D3520; opacity:1; }
.gallery-thumb:hover { opacity:1; }

.contact-reveal { animation: fadeIn .3s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

.attr-row { display:flex; flex-direction:column; gap:2px; }
.attr-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#5C6352; }
.attr-value { font-size:.9rem; font-weight:600; color:#191D14; }

.related-card { background:white; border-radius:14px; overflow:hidden; transition:box-shadow .2s; text-decoration:none; display:block; }
.related-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); }

.product-gallery-main { height:440px; }
@media (max-width:640px) { .product-gallery-main { height:260px; } }

/* Sticky sidebar on desktop */
@media (min-width:1024px) {
    .sidebar-sticky { position:sticky; top:72px; }
}
</style>
@endpush

<div class="min-h-screen pb-16" style="background:#F8FCF7;"
     x-data="{
        activeImg: 0,
        favorited: {{ $isFavorited ? 'true' : 'false' }},
        favCount: {{ $product->favorites_count }},
        showPhone: false,
        phone: '',
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
                this.favorited = d.favorited; this.favCount = d.count;
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
                this.phone = d.phone; this.showPhone = true;
            } finally { this.loadingPhone = false; }
        },

        async trackCall() {
            await fetch('{{ route('products.contact-event', $product) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'call_click' })
            });
        },
     }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm mb-6" style="color:#5C6352;">
            <a href="{{ route('products.index') }}" style="color:#5C6352;text-decoration:none;" class="hover:underline">Bozor</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($product->category?->parent)
                <a href="{{ route('products.index', ['category' => $product->category->parent->id]) }}" style="color:#5C6352;text-decoration:none;" class="hover:underline">{{ $product->category->parent->name }}</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->id]) }}" style="color:#5C6352;text-decoration:none;" class="hover:underline">{{ $product->category->name }}</a>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="font-semibold text-ink truncate">{{ $product->name }}</span>
        </nav>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl text-sm" style="background:#E2ECDF;color:#1D3520;border:1px solid #c8dcc5;">{{ session('success') }}</div>
        @endif

        {{-- ── MAIN GRID: Gallery + Sidebar ── --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ═══ LEFT: Gallery + Description + Map ═══ --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Gallery --}}
                @php $gallery = $product->gallery; @endphp
                <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #EDF0E5;">

                    {{-- Main image --}}
                    <div class="relative product-gallery-main" style="background:#EDF0E5;">
                        @if(count($gallery) > 0)
                            @foreach($gallery as $i => $img)
                                <img src="{{ Storage::url($img) }}" alt="{{ $product->name }}"
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                     :class="activeImg === {{ $i }} ? 'opacity-100' : 'opacity-0'">
                            @endforeach
                        @else
                            <div class="w-full h-full flex items-center justify-center text-8xl">
                                {{ match(true) {
                                    in_array($product->category?->name, ['Sigir','Buqa','Buzoq']) || $product->category?->parent?->name === 'Qoramol' => '🐄',
                                    in_array($product->category?->name, ["Qo'y"]) => '🐑',
                                    $product->category?->name === 'Echki' => '🐐',
                                    $product->category?->name === 'Ot' => '🐴',
                                    $product->category?->name === 'Tuya' => '🐪',
                                    default => '🐾'
                                } }}
                            </div>
                        @endif

                        {{-- Counter --}}
                        @if(count($gallery) > 1)
                            <div class="absolute bottom-3 right-3 px-3 py-1 rounded-full text-xs font-semibold"
                                 style="background:rgba(0,0,0,.5);color:white;"
                                 x-text="(activeImg+1)+' / {{ count($gallery) }}'"></div>
                        @endif

                        {{-- Sold badge --}}
                        @if($product->isSold())
                            <div class="absolute top-4 left-4 px-4 py-1.5 rounded-full text-sm font-bold"
                                 style="background:rgba(0,0,0,.7);color:white;">Sotildi</div>
                        @endif
                    </div>

                    {{-- Thumbnails --}}
                    @if(count($gallery) > 1)
                        <div class="flex gap-2 p-3" style="background:#F8FCF7;overflow-x:auto;">
                            @foreach($gallery as $i => $img)
                                <button @click="activeImg = {{ $i }}"
                                        class="gallery-thumb"
                                        :class="activeImg === {{ $i }} ? 'active' : ''"
                                        style="width:80px;height:60px;">
                                    <img src="{{ Storage::url($img) }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Description & Attributes ── --}}
                <div class="bg-white rounded-2xl p-6" style="border:1px solid #EDF0E5;">
                    <h2 class="font-serif text-xl font-bold text-ink mb-4">Tavsif</h2>

                    @if($product->description)
                        <p class="text-sm leading-relaxed whitespace-pre-line mb-6" style="color:#3a3d35;">{{ $product->description }}</p>
                    @endif

                    {{-- Attribute grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-5 pt-5" style="border-top:1px solid #EDF0E5;">
                        @if($product->category?->parent?->name || $product->category?->name)
                            <div class="attr-row">
                                <span class="attr-label">Zoti</span>
                                <span class="attr-value">{{ $product->category?->name }}</span>
                            </div>
                        @endif
                        @if($product->age)
                            <div class="attr-row">
                                <span class="attr-label">Yoshi</span>
                                <span class="attr-value">{{ $product->age }} yosh</span>
                            </div>
                        @endif
                        @if($product->weight)
                            <div class="attr-row">
                                <span class="attr-label">Vazni</span>
                                <span class="attr-value">{{ $product->weight }} kg</span>
                            </div>
                        @endif
                        @if($product->gender)
                            <div class="attr-row">
                                <span class="attr-label">Jinsi</span>
                                <span class="attr-value">{{ $product->gender === 'erkak' ? 'Erkak' : "Urg'ochi" }}</span>
                            </div>
                        @endif
                        @if($product->color)
                            <div class="attr-row">
                                <span class="attr-label">Rangi</span>
                                <span class="attr-value">{{ $product->color->name }}</span>
                            </div>
                        @endif
                        @if($product->status)
                            <div class="attr-row">
                                <span class="attr-label">Sog'liq holati</span>
                                <span class="attr-value">Sog'lom, emlangan</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Map ── --}}
                <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #EDF0E5;">
                    <div class="px-6 pt-5 pb-3 flex items-center justify-between">
                        <h2 class="font-serif text-xl font-bold text-ink">Joylashuv</h2>
                        @php $address = collect([$product->city?->name, $product->region?->name])->filter()->implode(', '); @endphp
                        @if($address)
                            <span class="text-sm flex items-center gap-1.5" style="color:#5C6352;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $address }}
                            </span>
                        @endif
                    </div>

                    @if($product->latitude && $product->longitude)
                        <div id="miniMap" style="height:280px;"></div>
                        <div class="flex gap-3 p-4">
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $product->latitude }},{{ $product->longitude }}"
                               target="_blank" rel="noopener"
                               class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-semibold text-sm"
                               style="background:#1D3520;color:white;text-decoration:none;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Yo'l ko'rsatish
                            </a>
                            <a href="https://yandex.com/maps/?rtext=~{{ $product->latitude }},{{ $product->longitude }}&rtt=auto"
                               target="_blank" rel="noopener"
                               class="flex items-center justify-center gap-1.5 px-5 py-3 rounded-xl font-semibold text-sm"
                               style="background:#EDF0E5;color:#1D3520;text-decoration:none;">
                                Yandex
                            </a>
                        </div>
                    @elseif($address)
                        <div class="flex items-center justify-center py-10" style="background:#F8FCF7;">
                            <div class="text-center" style="color:#5C6352;">
                                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mx-auto mb-2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium">{{ $address }}</p>
                            </div>
                        </div>
                        <div class="px-4 pb-4">
                            <a href="https://www.google.com/maps/search/{{ urlencode($address) }}" target="_blank" rel="noopener"
                               class="flex items-center justify-center gap-2 py-3 rounded-xl font-semibold text-sm"
                               style="background:#1D3520;color:white;text-decoration:none;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Xaritada ko'rish
                            </a>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ═══ RIGHT: Sticky Sidebar ═══ --}}
            <div class="w-full lg:w-[340px] flex-shrink-0">
                <div class="sidebar-sticky space-y-4">

                    {{-- Price & CTA card --}}
                    <div class="bg-white rounded-2xl p-5" style="border:1px solid #EDF0E5;">

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($product->category)
                                <span style="background:#EDF0E5;color:#3E683F;font-size:.75rem;font-weight:700;padding:4px 10px;border-radius:6px;">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                            @if($product->gender)
                                <span style="background:#EDF0E5;color:#5C6352;font-size:.75rem;font-weight:700;padding:4px 10px;border-radius:6px;">
                                    {{ $product->gender === 'erkak' ? 'Erkak' : "Urg'ochi" }}
                                </span>
                            @endif
                            @if($product->status)
                                <span style="font-size:.75rem;font-weight:700;padding:4px 10px;border-radius:6px;
                                    {{ $product->status->name === 'Faol' ? 'background:#E2ECDF;color:#1D3520;' :
                                       ($product->status->name === 'Sotildi' ? 'background:#f0f0f0;color:#6b7280;' : 'background:#F6ECD7;color:#B5822A;') }}">
                                    {{ strtoupper($product->status->name) }}
                                </span>
                            @endif
                        </div>

                        {{-- Title & Price --}}
                        <h1 class="font-serif text-xl font-bold text-ink leading-snug mb-1">{{ $product->name }}</h1>
                        <p class="font-serif font-bold text-3xl mb-1" style="color:#1D3520;">{{ $product->formatted_price }}</p>
                        <p class="text-xs flex items-center gap-3 mb-5" style="color:#5C6352;">
                            <span class="flex items-center gap-1">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ number_format($product->views_count) }} ko'rish
                            </span>
                            <span>{{ $product->created_at->diffForHumans() }}</span>
                        </p>

                        {{-- Contact buttons --}}
                        @if(!$product->isSold())
                            @auth
                                @if(auth()->id() === $product->user_id)
                                    {{-- Owner controls --}}
                                    <div class="space-y-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                           style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;background:#1D3520;color:white;padding:13px;border-radius:10px;font-weight:700;font-size:.9rem;text-decoration:none;">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            E'lonni tahrirlash
                                        </a>
                                        <button @click="$dispatch('open-sold-modal')"
                                                style="width:100%;background:#F6ECD7;color:#B5822A;padding:12px;border-radius:10px;font-weight:700;font-size:.9rem;border:none;cursor:pointer;">
                                            Sotildiga belgilash
                                        </button>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    style="width:100%;background:transparent;color:#A34F30;padding:11px;border-radius:10px;font-weight:700;font-size:.9rem;border:1.5px solid #A34F30;cursor:pointer;">
                                                O'chirish
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    {{-- Buyer: phone reveal + save/share --}}
                                    <div class="space-y-2">
                                        <template x-if="!showPhone">
                                            <button @click="revealPhone()" :disabled="loadingPhone"
                                                    style="width:100%;background:#1D3520;color:white;padding:13px;border-radius:10px;font-weight:700;font-size:.9rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span x-show="!loadingPhone">Raqamni ko'rsatish</span>
                                                <span x-show="loadingPhone">...</span>
                                            </button>
                                        </template>
                                        <template x-if="showPhone">
                                            <div class="contact-reveal">
                                                <a :href="'tel:' + phone" @click="trackCall()"
                                                   style="width:100%;background:#1D3520;color:white;padding:13px;border-radius:10px;font-weight:700;font-size:1rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;"
                                                   x-text="phone"></a>
                                                <p class="text-center text-xs mt-1.5" style="color:#5C6352;">Qo'ng'iroq qilish uchun bosing</p>
                                            </div>
                                        </template>

                                        <div class="flex gap-2">
                                            <button @click="toggleFav()" :disabled="loadingFav"
                                                    style="flex:1;padding:11px;border-radius:10px;font-weight:600;font-size:.88rem;border:1.5px solid #E2ECDF;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .15s;"
                                                    :style="favorited ? 'background:#F5E3DB;border-color:#A34F30;color:#A34F30;' : 'background:white;border-color:#E2ECDF;color:#5C6352;'">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="favorited ? 'stroke:#A34F30;fill:#A34F30;' : ''"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                                <span x-text="favorited ? 'Saqlangan' : 'Saqlash'"></span>
                                            </button>
                                            <button onclick="navigator.share ? navigator.share({title:{{ json_encode($product->name) }},url:window.location.href}) : navigator.clipboard.writeText(window.location.href)"
                                                    style="flex:1;padding:11px;border-radius:10px;font-weight:600;font-size:.88rem;border:1.5px solid #E2ECDF;background:white;color:#5C6352;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                                Ulashish
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- Guest --}}
                                <div class="rounded-xl p-4 text-center mb-2" style="background:#EDF0E5;">
                                    <p class="text-sm font-semibold text-ink mb-3">Raqamni ko'rish uchun kiring</p>
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('login') }}"
                                           style="padding:9px 20px;background:#1D3520;color:white;border-radius:8px;font-weight:700;font-size:.85rem;text-decoration:none;">Kirish</a>
                                        <a href="{{ route('register') }}"
                                           style="padding:9px 20px;background:white;color:#1D3520;border-radius:8px;font-weight:700;font-size:.85rem;text-decoration:none;border:1.5px solid #E2ECDF;">Ro'yxat</a>
                                    </div>
                                </div>
                            @endauth
                        @else
                            <div class="rounded-xl p-5 text-center mb-2" style="background:#f0f0f0;">
                                <p class="text-2xl mb-2">🏷️</p>
                                <p class="font-semibold text-ink">Bu mahsulot sotilgan</p>
                            </div>
                        @endif
                    </div>

                    {{-- Seller card --}}
                    <div class="bg-white rounded-2xl p-5" style="border:1px solid #EDF0E5;">
                        <p class="text-xs font-semibold uppercase tracking-widest mb-4" style="color:#5C6352;">Sotuvchi</p>

                        @if($product->user)
                        <a href="{{ route('seller.show', $product->user) }}" style="text-decoration:none;display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                            {{-- Avatar --}}
                            @if($product->user->avatar)
                                <img src="{{ $product->user->avatar_url }}" alt=""
                                     class="w-11 h-11 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-base"
                                     style="background:#E2ECDF;color:#1D3520;">
                                    {{ mb_strtoupper(mb_substr($product->user->first_name ?? 'S', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-sm text-ink">{{ $product->user->first_name }} {{ $product->user->last_name }}</p>
                                <p class="text-xs flex items-center gap-1" style="color:#3E683F;">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Tasdiqlangan fermer · {{ $product->user->created_at?->year }} yildan
                                </p>
                            </div>
                        </a>

                        {{-- Seller stats --}}
                        @if($sellerStats)
                            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                                <div class="rounded-xl py-2.5" style="background:#F8FCF7;">
                                    <p class="font-bold text-base text-ink">{{ $sellerStats['active'] }}</p>
                                    <p class="text-xs" style="color:#5C6352;">Faol e'lon</p>
                                </div>
                                <div class="rounded-xl py-2.5" style="background:#F8FCF7;">
                                    <p class="font-bold text-base text-ink">{{ $sellerStats['total'] }}</p>
                                    <p class="text-xs" style="color:#5C6352;">Jami e'lon</p>
                                </div>
                                <div class="rounded-xl py-2.5" style="background:#F8FCF7;">
                                    <p class="font-bold text-base text-ink">{{ $sellerStats['sold'] }}</p>
                                    <p class="text-xs" style="color:#5C6352;">Sotilgan</p>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('seller.show', $product->user) }}"
                           style="display:block;width:100%;padding:10px;text-align:center;border-radius:10px;font-weight:600;font-size:.875rem;text-decoration:none;border:1.5px solid #E2ECDF;color:#191D14;background:white;transition:background .15s;"
                           onmouseover="this.style.background='#F8FCF7'" onmouseout="this.style.background='white'">
                            Barcha e'lonlari
                        </a>
                        @else
                        <div class="flex items-center gap-3 py-2" style="color:#5C6352;">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                                 style="background:#EDF0E5;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <p class="text-sm">Sotuvchi ma'lumoti topilmadi</p>
                        </div>
                        @endif
                    </div>

                    {{-- Safety box --}}
                    <div class="rounded-2xl p-4 flex gap-3" style="background:#F6ECD7;border:1px solid #e8d5a8;">
                        <svg width="20" height="20" fill="none" stroke="#B5822A" viewBox="0 0 24 24" class="flex-shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-sm mb-1" style="color:#B5822A;">Xavfsiz savdo</p>
                            <p class="text-xs leading-relaxed" style="color:#9a6d22;">Oldindan to'lov qilmang. Molni o'zingiz ko'ring va veterinar hujjatlarini tekshiring. Shubhali e'lonni bizga xabar qiling.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── O'XSHASH E'LONLAR ── --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-12">
                <h2 class="font-serif text-2xl font-bold text-ink mb-6">O'xshash e'lonlar</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $rel)
                        <a href="{{ route('products.show', $rel) }}" class="related-card">
                            <div style="height:160px;background:#EDF0E5;overflow:hidden;position:relative;">
                                @if($rel->primary_image_url)
                                    <img src="{{ $rel->primary_image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl">
                                        {{ match(true) {
                                            in_array($rel->category?->name, ['Sigir','Buqa','Buzoq']) || $rel->category?->parent?->name === 'Qoramol' => '🐄',
                                            in_array($rel->category?->name, ["Qo'y"]) => '🐑',
                                            $rel->category?->name === 'Echki' => '🐐',
                                            $rel->category?->name === 'Ot' => '🐴',
                                            $rel->category?->name === 'Tuya' => '🐪',
                                            default => '🐾'
                                        } }}
                                    </div>
                                @endif
                                @if($rel->created_at->diffInDays() < 3)
                                    <span style="position:absolute;top:10px;left:10px;background:#B5822A;color:white;font-size:.62rem;font-weight:700;padding:3px 8px;border-radius:5px;text-transform:uppercase;">Yangi</span>
                                @endif
                            </div>
                            <div class="p-4">
                                <div class="flex gap-1.5 mb-2 flex-wrap">
                                    @if($rel->category)
                                        <span style="background:#EDF0E5;color:#3E683F;font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:5px;">
                                            {{ $rel->category?->parent?->name ?? $rel->category?->name }}
                                        </span>
                                        @if($rel->category->parent)
                                            <span style="background:#EDF0E5;color:#5C6352;font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:5px;">
                                                {{ $rel->category->name }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <h3 class="font-bold text-sm text-ink line-clamp-1 mb-1">{{ $rel->name }}</h3>
                                <p class="font-serif font-bold text-base mb-1.5" style="color:#1D3520;">{{ $rel->formatted_price }}</p>
                                <div class="flex gap-2 text-xs" style="color:#5C6352;">
                                    @if($rel->age)<span>{{ $rel->age }} yosh</span>@endif
                                    @if($rel->weight)<span>{{ $rel->weight }} kg</span>@endif
                                </div>
                                @if($rel->city || $rel->region)
                                    <p class="text-xs mt-1 flex items-center gap-1" style="color:#5C6352;">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ collect([$rel->city?->name, $rel->region?->name])->filter()->implode(', ') }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Mark as Sold modal --}}
    @auth
        @if(auth()->id() === $product->user_id)
            <div x-data="{ open: false }" @open-sold-modal.window="open = true">
                <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0" style="background:rgba(0,0,0,.5);" @click="open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
                        <h3 class="font-serif text-lg font-bold text-ink mb-5">Sotildiga belgilash</h3>
                        <form method="POST" action="{{ route('products.mark-sold', $product) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Sotilgan narxi</label>
                                <input type="number" name="sold_price" value="{{ $product->price }}" min="0"
                                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;box-sizing:border-box;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Savdo manbasi</label>
                                <select name="source"
                                        style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;box-sizing:border-box;">
                                    <option value="outside">Platforma tashqarisida</option>
                                    <option value="phone_call">Telefon orqali</option>
                                    <option value="platform_chat">Sayt orqali</option>
                                </select>
                            </div>
                            <div class="flex gap-3 pt-1">
                                <button type="button" @click="open = false"
                                        style="flex:1;padding:12px;border:1.5px solid #E2ECDF;border-radius:10px;font-weight:600;background:white;cursor:pointer;">
                                    Bekor qilish
                                </button>
                                <button type="submit"
                                        style="flex:1;padding:12px;background:#1D3520;color:white;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                                    Tasdiqlash
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('miniMap', { scrollWheelZoom: false, dragging: window.innerWidth > 768, tap: false })
                 .setView([{{ $product->latitude }}, {{ $product->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    const icon = L.divIcon({
        className: '',
        html: '<div style="background:#1D3520;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,.4)"></div>',
        iconSize: [16, 16], iconAnchor: [8, 8],
    });

    L.marker([{{ $product->latitude }}, {{ $product->longitude }}], { icon })
     .addTo(map)
     .bindPopup('<b>' + {{ json_encode(e($product->name)) }} + '</b><br><span style="font-size:.8rem;color:#6b7280">{{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') }}</span>')
     .openPopup();

    if (window.innerWidth < 768) {
        document.getElementById('miniMap').addEventListener('click', () =>
            window.open('https://www.google.com/maps/dir/?api=1&destination={{ $product->latitude }},{{ $product->longitude }}', '_blank'));
        document.getElementById('miniMap').style.cursor = 'pointer';
    }
});
</script>
@endpush
@endif
</x-app-layout>
