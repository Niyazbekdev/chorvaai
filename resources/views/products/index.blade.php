<x-app-layout>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
/* ── Filter bottom sheet (mobile) ── */
@media (max-width: 1023px) {
    #filterBox {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 9998;
        border-radius: 20px 20px 0 0;
        max-height: 82dvh;
        overflow-y: auto;
        margin: 0;
        box-shadow: 0 -4px 30px rgba(0,0,0,.15);
        transition: transform .28s ease, opacity .28s ease;
    }
    #filterBox.hidden {
        display: block !important;
        transform: translateY(110%);
        opacity: 0;
        pointer-events: none;
    }
    #filter-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 9997;
        background: rgba(0,0,0,.4);
    }
    #filter-backdrop.open { display: block; }
}

/* ── Map container ── */
#map-view {
    height: calc(100vh - 200px);
    min-height: 500px;
    border-radius: 16px;
    overflow: hidden;
}

/* ── Animal pin marker ── */
.animal-marker-wrap { background: transparent !important; border: none !important; }
.animal-pin {
    position: relative; width: 44px; height: 44px;
    border-radius: 50% 50% 50% 0; transform: rotate(-45deg);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.28); transition: transform .15s; cursor: pointer;
}
.animal-pin:hover { transform: rotate(-45deg) scale(1.15); }
.animal-pin span { transform: rotate(45deg); font-size: 22px; line-height: 1; display: block; user-select: none; }
.pin-qoramol { background:#fff; border:3px solid #1D3520; }
.pin-qoy     { background:#fff; border:3px solid #B5822A; }
.pin-ot      { background:#fff; border:3px solid #3E683F; }
.pin-default { background:#fff; border:3px solid #5C6352; }

/* ── Popup ── */
.leaflet-popup-content-wrapper {
    border-radius: 14px !important; padding: 0 !important; overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.15) !important; min-width: 200px;
}
.leaflet-popup-content { margin: 0 !important; }
.map-popup-img { width:100%; height:90px; object-fit:cover; display:block; }
.map-popup-img-placeholder { width:100%; height:90px; background:#EDF0E5; display:flex; align-items:center; justify-content:center; font-size:36px; }
.map-popup-body { padding:10px 12px 12px; }
.map-popup-cat { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#3E683F; margin-bottom:2px; }
.map-popup-title { font-weight:700; font-size:.9rem; color:#191D14; line-height:1.3; }
.map-popup-price { color:#1D3520; font-weight:800; font-size:1rem; margin:2px 0 4px; }
.map-popup-loc { color:#5C6352; font-size:.75rem; }
.map-popup-btn { display:block; margin-top:8px; background:#1D3520; color:white; text-align:center; padding:7px; border-radius:8px; font-size:.8rem; font-weight:700; text-decoration:none; }
.map-popup-btn:hover { background:#2C4E2E; }

/* Filter sidebar category tree */
.filter-parent { font-weight:700; font-size:.88rem; color:#191D14; margin-bottom:4px; cursor:pointer; }
.filter-child {
    display:block; padding:5px 0 5px 12px; font-size:.84rem; color:#5C6352;
    cursor:pointer; border-radius:6px; transition:background .12s, color .12s;
    text-decoration:none;
}
.filter-child:hover, .filter-child.active { color:#1D3520; background:#E2ECDF; }
.filter-child.active { font-weight:700; }

/* Active category chip */
.filter-chip-plain.active { background:#1D3520; color:white; }

/* Active filter chips */
.filter-chip {
    display:inline-flex; align-items:center; gap:6px;
    background:#1D3520; color:white;
    font-size:.78rem; font-weight:600; padding:5px 12px; border-radius:999px;
}
.filter-chip-plain {
    display:inline-flex; align-items:center;
    background:#EDF0E5; color:#191D14;
    font-size:.78rem; font-weight:600; padding:5px 14px; border-radius:999px;
    cursor:pointer; transition:background .12s;
    text-decoration:none;
}
.filter-chip-plain:hover { background:#E2ECDF; }

/* Gender toggle */
.gender-btn {
    flex:1; padding:7px 0; border-radius:8px; font-size:.84rem; font-weight:600;
    border:1.5px solid #E2ECDF; background:white; color:#5C6352; cursor:pointer; text-align:center;
    transition:all .15s;
}
.gender-btn.active { background:#1D3520; color:white; border-color:#1D3520; }

/* Product card */
.product-card { background:white; border-radius:16px; overflow:hidden; transition:box-shadow .2s; text-decoration:none; display:block; }
.product-card:hover { box-shadow:0 8px 24px rgba(0,0,0,.1); }
</style>
@endpush

<div class="min-h-screen" style="background:#F8FCF7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Banner --}}
        <x-banner-slider />

        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 mb-6 mt-4">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-ink">{{ __('products.page_title') }}</h1>
                <p class="text-ink-2 mt-0.5 text-sm">{{ __('products.header_subtitle', ['count' => $products->total()]) }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                {{-- View toggle --}}
                <div style="background:#EDF0E5;border-radius:10px;display:flex;padding:3px;gap:2px;">
                    <button id="btn-cards" onclick="setView('cards')"
                            style="padding:6px 12px;border-radius:8px;background:#1D3520;color:white;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;transition:all .15s;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        {{ __('products.cards_view') }}
                    </button>
                    <button id="btn-map" onclick="setView('map')"
                            style="padding:6px 12px;border-radius:8px;background:transparent;color:#5C6352;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;transition:all .15s;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        {{ __('products.map_view') }}
                    </button>
                </div>
                {{-- Sort (desktop) --}}
                <select onchange="location.href=this.value" class="hidden sm:block text-sm rounded-xl border border-[#E2ECDF] bg-white text-ink px-3 py-2 focus:ring-0 focus:border-[#3E683F]">
                    <option value="{{ route('products.index', request()->except('sort')) }}" @selected(!request('sort'))>{{ __('products.sort_newest') }}</option>
                    <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_asc'])) }}" @selected(request('sort') === 'price_asc')>{{ __('products.sort_price_asc') }}</option>
                    <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'price_desc'])) }}" @selected(request('sort') === 'price_desc')>{{ __('products.sort_price_desc') }}</option>
                    <option value="{{ route('products.index', array_merge(request()->all(), ['sort' => 'popular'])) }}" @selected(request('sort') === 'popular')>{{ __('products.sort_popular') }}</option>
                </select>
                {{-- Filter btn (mobile/tablet) --}}
                <button onclick="toggleFilter()"
                        class="lg:hidden flex items-center gap-2 text-sm font-semibold px-3 py-2 rounded-xl"
                        style="background:#EDF0E5;color:#191D14;border:none;cursor:pointer;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    {{ __('products.filter_btn') }}
                </button>
            </div>
        </div>

        {{-- Active filter chips --}}
        @if(request()->anyFilled(['category','region','city','price_from','price_to','q','gender']))
            <div class="flex flex-wrap gap-2 mb-5">
                @if(request('q'))
                    <span class="filter-chip">
                        "{{ request('q') }}"
                        <a href="{{ route('products.index', request()->except('q')) }}" style="color:white;line-height:1;">×</a>
                    </span>
                @endif
                @if(request('region'))
                    @php $rn = $regions->firstWhere('id', request('region'))?->name; @endphp
                    @if($rn)
                        <span class="filter-chip">
                            {{ $rn }}
                            <a href="{{ route('products.index', request()->except('region','city')) }}" style="color:white;line-height:1;">×</a>
                        </span>
                    @endif
                @endif
                @foreach([['Qoramol','🐄'],["Qo'y va echki",'🐑'],['Ot va tuya','🐴'],['Parranda','🐓']] as [$name, $em])
                    <a href="{{ route('products.index', ['category' => $categories->firstWhere('name', $name)?->id]) }}" class="filter-chip-plain">{{ $em }} {{ $name }}</a>
                @endforeach
                <a href="{{ route('products.index') }}" class="filter-chip-plain" style="color:#A34F30;">{{ __('products.clear') }}</a>
            </div>
        @else
            {{-- Category quick chips --}}
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach([['Qoramol','🐄'],["Qo'y va echki",'🐑'],['Ot va tuya','🐴'],['Parranda','🐓']] as [$name, $em])
                    @php $cid = $categories->firstWhere('name', $name)?->id; @endphp
                    <a href="{{ route('products.index', ['category' => $cid]) }}" class="filter-chip-plain {{ request('category') == $cid ? 'active' : '' }}">
                        {{ $em }} {{ $name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#E2ECDF;color:#1D3520;">{{ session('success') }}</div>
        @endif

        {{-- Layout: sidebar + content --}}
        <div class="flex gap-6">

            {{-- ── SIDEBAR FILTER (desktop) ── --}}
            <div id="filterBox" class="hidden lg:block w-full lg:w-[220px] flex-shrink-0 bg-white rounded-2xl p-5"
                 style="border:1px solid #EDF0E5;height:fit-content;align-self:start;">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-base text-ink">{{ __('products.filter_title') }}</h2>
                    @if(request()->anyFilled(['category','region','city','price_from','price_to','q']))
                        <a href="{{ route('products.index') }}" style="color:#A34F30;font-size:.8rem;font-weight:600;text-decoration:none;">{{ __('products.clear') }}</a>
                    @endif
                    <button onclick="toggleFilter()" class="lg:hidden" style="background:none;border:none;cursor:pointer;color:#5C6352;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('products.index') }}" id="filterForm">

                    {{-- Search --}}
                    <div class="relative mb-5">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#5C6352;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('products.search_placeholder') }}"
                               style="width:100%;padding:9px 12px 9px 34px;border:1.5px solid #E2ECDF;border-radius:10px;font-size:.84rem;outline:none;background:#F8FCF7;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                    </div>

                    {{-- Categories --}}
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:#5C6352;">{{ __('products.category_label') }}</p>
                        @foreach($categories as $parent)
                            <div class="mb-2">
                                <p class="filter-parent">{{ $parent->name }}</p>
                                @foreach($parent->children as $child)
                                    <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $child->id])) }}"
                                       class="filter-child {{ request('category') == $child->id ? 'active' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Viloyat --}}
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#5C6352;">{{ __('products.region_label') }}</p>
                        <select name="region" id="regionSelect" onchange="document.getElementById('filterForm').submit()"
                                style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:9px 12px;font-size:.84rem;background:white;color:#191D14;outline:none;box-sizing:border-box;">
                            <option value="">{{ __('products.all_regions') }}</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected(request('region') == $region->id)>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tuman --}}
                    <div class="mb-4">
                        <select name="city" id="citySelect"
                                style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:9px 12px;font-size:.84rem;background:white;color:#191D14;outline:none;box-sizing:border-box;">
                            <option value="">{{ __('products.all_cities') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-region="{{ $city->region_id }}" @selected(request('city') == $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Narx --}}
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#5C6352;">{{ __('products.price_range') }}</p>
                        <div class="flex gap-2">
                            <input type="number" name="price_from" value="{{ request('price_from') }}" placeholder="{{ __('products.price_from') }}"
                                   style="flex:1;border:1.5px solid #E2ECDF;border-radius:10px;padding:9px 10px;font-size:.82rem;outline:none;min-width:0;"
                                   onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                            <input type="number" name="price_to" value="{{ request('price_to') }}" placeholder="{{ __('products.price_to') }}"
                                   style="flex:1;border:1.5px solid #E2ECDF;border-radius:10px;padding:9px 10px;font-size:.82rem;outline:none;min-width:0;"
                                   onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                        </div>
                    </div>

                    {{-- Jinsi --}}
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#5C6352;">{{ __('products.gender_label') }}</p>
                        <div class="flex gap-1.5">
                            <button type="submit" name="gender" value=""
                                    class="gender-btn {{ !request('gender') ? 'active' : '' }}">{{ __('products.gender_all') }}</button>
                            <button type="submit" name="gender" value="erkak"
                                    class="gender-btn {{ request('gender') === 'erkak' ? 'active' : '' }}">{{ __('products.gender_male') }}</button>
                            <button type="submit" name="gender" value="urgochi"
                                    class="gender-btn {{ request('gender') === 'urgochi' ? 'active' : '' }}">{{ __('products.gender_female') }}</button>
                        </div>
                    </div>

                    <button type="submit"
                            style="width:100%;background:#1D3520;color:white;padding:11px;border-radius:10px;font-weight:700;font-size:.9rem;border:none;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                        {{ __('products.search') }}
                    </button>
                </form>
            </div>

            {{-- ── MAIN CONTENT ── --}}
            <div class="flex-1 min-w-0">

                {{-- ══ CARDS VIEW ══ --}}
                <div id="cards-view">
                    @if($products->isEmpty())
                        <div class="text-center py-24" style="color:#5C6352;">
                            <p class="text-5xl mb-4">🐄</p>
                            <p class="text-lg font-bold text-ink">{{ __('products.no_results') }}</p>
                            <p class="text-sm mt-1">{{ __('products.no_results_hint') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            @foreach($products as $product)
                                <a href="{{ route('products.show', $product) }}" class="product-card group">
                                    {{-- Image --}}
                                    <div class="relative" style="height:160px;background:#EDF0E5;overflow:hidden;">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-4xl">
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
                                        {{-- Status badges --}}
                                        <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                                            @if($product->created_at->diffInDays() < 3)
                                                <span style="background:#B5822A;color:white;font-size:.62rem;font-weight:700;padding:3px 7px;border-radius:5px;text-transform:uppercase;">{{ __('products.new_tag') }}</span>
                                            @endif
                                        </div>
                                        {{-- Fav --}}
                                        @auth
                                        <button class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white flex items-center justify-center"
                                                style="box-shadow:0 2px 6px rgba(0,0,0,.12);"
                                                x-data="{ faved: false, loading: false }"
                                                x-init="faved = false"
                                                @click.prevent="
                                                    if (loading) return;
                                                    loading = true;
                                                    fetch('{{ route('products.favorite', $product) }}', {
                                                        method: 'POST',
                                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                    }).then(r => r.json()).then(d => { faved = d.favorited; }).finally(() => loading = false);
                                                ">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                 :style="faved ? 'stroke:#A34F30;fill:#A34F30;' : 'stroke:#5C6352;'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                        @else
                                        <a href="{{ route('login') }}"
                                           class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white flex items-center justify-center"
                                           style="box-shadow:0 2px 6px rgba(0,0,0,.12);"
                                           @click.stop>
                                            <svg width="13" height="13" fill="none" stroke="#5C6352" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        </a>
                                        @endauth
                                    </div>

                                    {{-- Info --}}
                                    <div class="p-3.5">
                                        <div class="flex gap-1.5 mb-2 flex-wrap">
                                            @if($product->category)
                                                <span style="background:#EDF0E5;color:#3E683F;font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:5px;">
                                                    {{ $product->category?->parent?->name ?? $product->category?->name }}
                                                </span>
                                                @if($product->category->parent)
                                                    <span style="background:#EDF0E5;color:#5C6352;font-size:.68rem;font-weight:700;padding:3px 8px;border-radius:5px;">
                                                        {{ $product->category->name }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <h3 class="font-bold text-ink text-sm leading-snug line-clamp-1 mb-1">{{ $product->name }}</h3>
                                        <p class="font-serif font-bold text-base mb-2" style="color:#1D3520;">{{ $product->formatted_price }}</p>
                                        <div class="flex gap-3 text-xs mb-1.5" style="color:#5C6352;">
                                            @if($product->age)<span>{{ $product->age }} {{ __('products.age_unit') }}</span>@endif
                                            @if($product->weight)<span>{{ $product->weight }} kg</span>@endif
                                        </div>
                                        @if($product->city || $product->region)
                                            <p class="text-xs flex items-center gap-1" style="color:#5C6352;">
                                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') }}
                                                @if($product->views_count)
                                                    <span class="ml-auto flex items-center gap-0.5">
                                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        {{ $product->views_count }}
                                                    </span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8 flex justify-center">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

                {{-- ══ MAP VIEW ══ --}}
                <div id="map-view" class="hidden relative">
                    <div class="absolute top-3 right-3 z-[1000] bg-white rounded-xl shadow p-3" style="min-width:150px;border:1px solid #EDF0E5;">
                        <p class="font-bold text-xs uppercase tracking-wide text-ink mb-1.5">{{ __('products.map_legend') }}</p>
                        <div class="space-y-1 text-xs" style="color:#5C6352;">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full inline-block" style="background:#1D3520;"></span>{{ __('products.map_legend_cattle') }}</div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full inline-block" style="background:#B5822A;"></span>{{ __('products.map_legend_sheep') }}</div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full inline-block" style="background:#3E683F;"></span>{{ __('products.map_legend_horse') }}</div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full inline-block" style="background:#5C6352;"></span>{{ __('products.map_legend_other') }}</div>
                        </div>
                        <hr class="my-2" style="border-color:#EDF0E5;">
                        <p class="text-xs" style="color:#5C6352;" id="mapCount">{{ __('products.map_count', ['count' => $mapProducts->count()]) }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Filter backdrop (mobile) --}}
<div id="filter-backdrop" onclick="toggleFilter()"></div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Region/city cascade ─────────────────────────────────────
(function () {
    const regionSel = document.getElementById('regionSelect');
    const citySel   = document.getElementById('citySelect');
    if (!regionSel || !citySel) return;
    const savedOpts = Array.from(citySel.querySelectorAll('option')).map(opt => ({
        value: opt.value, text: opt.textContent, regionId: opt.dataset.region || '',
    }));
    function filterCities(regionId) {
        const currentVal = citySel.value;
        citySel.innerHTML = '';
        savedOpts.forEach(data => {
            if (!data.value || !regionId || data.regionId == regionId) {
                const opt = document.createElement('option');
                opt.value = data.value; opt.textContent = data.text;
                if (data.regionId) opt.dataset.region = data.regionId;
                if (data.value && data.value == currentVal && (!regionId || data.regionId == regionId)) opt.selected = true;
                citySel.appendChild(opt);
            }
        });
    }
    regionSel.addEventListener('change', () => filterCities(regionSel.value));
    filterCities(regionSel.value);
})();

// ── Filter toggle ────────────────────────────────────────────
function toggleFilter() {
    const box      = document.getElementById('filterBox');
    const backdrop = document.getElementById('filter-backdrop');
    const isMobile = window.innerWidth < 1024;
    if (isMobile) {
        const isHidden = box.classList.contains('hidden');
        box.classList.toggle('hidden');
        backdrop.classList.toggle('open', isHidden);
        document.body.style.overflow = isHidden ? 'hidden' : '';
    } else {
        box.classList.toggle('hidden');
    }
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const box = document.getElementById('filterBox');
        if (!box.classList.contains('hidden')) toggleFilter();
    }
});

// ── Map ──────────────────────────────────────────────────────
const MAP_PRODUCTS      = @json($mapProducts);
const VIEW_DETAIL_TEXT  = '{{ __('products.view_detail') }}';

const ANIMAL_MAP = {
    'Sigir': { emoji: '🐄', cls: 'pin-qoramol' }, 'Buqa': { emoji: '🐄', cls: 'pin-qoramol' },
    'Buzoq': { emoji: '🐄', cls: 'pin-qoramol' }, "Qo'y": { emoji: '🐑', cls: 'pin-qoy' },
    'Echki': { emoji: '🐐', cls: 'pin-qoy'     }, 'Ot':   { emoji: '🐴', cls: 'pin-ot'  },
    'Tuya':  { emoji: '🐪', cls: 'pin-ot'      },
    'Qoramol': { emoji: '🐄', cls: 'pin-qoramol' }, "Qo'y va echki": { emoji: '🐑', cls: 'pin-qoy' },
    'Ot va tuya': { emoji: '🐴', cls: 'pin-ot' },
};
function getAnimal(cat, par) { return ANIMAL_MAP[cat] || ANIMAL_MAP[par] || { emoji: '🐾', cls: 'pin-default' }; }
function createPin(emoji, cls) {
    return L.divIcon({ html: `<div class="animal-pin ${cls}"><span>${emoji}</span></div>`, className: 'animal-marker-wrap', iconSize: [44,44], iconAnchor: [22,44], popupAnchor: [0,-48] });
}
function buildPopup(p) {
    const imgHtml = p.img ? `<img class="map-popup-img" src="${p.img}" alt="${p.title}">` : `<div class="map-popup-img-placeholder">${getAnimal(p.category, p.parent).emoji}</div>`;
    return `<div style="width:210px">${imgHtml}<div class="map-popup-body"><div class="map-popup-cat">${p.category||p.parent||''}</div><div class="map-popup-title">${p.title}</div><div class="map-popup-price">${p.price}</div>${p.loc?`<div class="map-popup-loc">📍 ${p.loc}</div>`:''}<a class="map-popup-btn" href="${p.url}">${VIEW_DETAIL_TEXT} →</a></div></div>`;
}

let mapInstance = null;
function initMap() {
    if (mapInstance) return;
    mapInstance = L.map('map-view').setView([41.2995, 69.2401], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mapInstance);
    const bounds = [];
    MAP_PRODUCTS.forEach(p => {
        if (!p.lat || !p.lng) return;
        const { emoji, cls } = getAnimal(p.category, p.parent);
        L.marker([p.lat, p.lng], { icon: createPin(emoji, cls) }).addTo(mapInstance)
         .bindPopup(buildPopup(p), { maxWidth: 230 });
        bounds.push([p.lat, p.lng]);
    });
    if (bounds.length > 0) mapInstance.fitBounds(bounds, { padding: [40,40], maxZoom: 10 });
    document.getElementById('mapCount').textContent = '{{ __('products.map_count', ['count' => '']) }}' + MAP_PRODUCTS.length;
}

// ── View toggle ──────────────────────────────────────────────
let currentView = 'cards';
function setView(v) {
    const cardsEl  = document.getElementById('cards-view');
    const mapEl    = document.getElementById('map-view');
    const btnCards = document.getElementById('btn-cards');
    const btnMap   = document.getElementById('btn-map');
    currentView = v;
    if (v === 'map') {
        cardsEl.classList.add('hidden'); mapEl.classList.remove('hidden');
        btnMap.style.cssText   = 'padding:6px 12px;border-radius:8px;background:#1D3520;color:white;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;';
        btnCards.style.cssText = 'padding:6px 12px;border-radius:8px;background:transparent;color:#5C6352;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;';
        requestAnimationFrame(() => { if (!mapInstance) initMap(); else mapInstance.invalidateSize(); });
    } else {
        cardsEl.classList.remove('hidden'); mapEl.classList.add('hidden');
        btnCards.style.cssText = 'padding:6px 12px;border-radius:8px;background:#1D3520;color:white;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;';
        btnMap.style.cssText   = 'padding:6px 12px;border-radius:8px;background:transparent;color:#5C6352;border:none;cursor:pointer;font-size:.82rem;font-weight:600;display:flex;align-items:center;gap:5px;';
    }
}
@if(request('view') === 'map')
document.addEventListener('DOMContentLoaded', () => setView('map'));
@endif
</script>
@endpush
</x-app-layout>
