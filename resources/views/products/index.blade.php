<x-app-layout>
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>

/* ── Filter bottom sheet (mobile) ── */
@media (max-width: 767px) {
    #filterBox {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 9998;
        border-radius: 20px 20px 0 0;
        max-height: 82dvh;
        overflow-y: auto;
        margin: 0;
        box-shadow: 0 -4px 30px rgba(0,0,0,.18);
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
        background: rgba(0,0,0,.45);
    }
    #filter-backdrop.open { display: block; }
}

/* ── Map container ── */
#map-view {
    height: calc(100vh - 230px);
    min-height: 520px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.12);
}

/* ── Animal pin marker ── */
.animal-marker-wrap { background: transparent !important; border: none !important; }

.animal-pin {
    position: relative;
    width: 44px; height: 44px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.28);
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
}
.animal-pin:hover {
    transform: rotate(-45deg) scale(1.15);
    box-shadow: 0 6px 20px rgba(0,0,0,.38);
}
.animal-pin span {
    transform: rotate(45deg);
    font-size: 22px;
    line-height: 1;
    display: block;
    user-select: none;
}

/* category colour classes */
.pin-qoramol  { background: #ffffff; border: 3px solid #10b981; } /* emerald */
.pin-qoy      { background: #ffffff; border: 3px solid #3b82f6; } /* blue */
.pin-ot       { background: #ffffff; border: 3px solid #f59e0b; } /* amber */
.pin-default  { background: #ffffff; border: 3px solid #8b5cf6; } /* violet */

/* ── Popup ── */
.leaflet-popup-content-wrapper {
    border-radius: 14px !important;
    padding: 0 !important;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.22) !important;
    min-width: 200px;
}
.leaflet-popup-content { margin: 0 !important; }
.map-popup-img {
    width: 100%; height: 90px; object-fit: cover;
    display: block;
}
.map-popup-img-placeholder {
    width: 100%; height: 90px;
    background: linear-gradient(135deg,#d1fae5,#a7f3d0);
    display: flex; align-items: center; justify-content: center;
    font-size: 36px;
}
.map-popup-body { padding: 10px 12px 12px; }
.map-popup-cat {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: #10b981; margin-bottom: 2px;
}
.map-popup-title { font-weight: 700; font-size: .9rem; color: #111827; line-height: 1.3; }
.map-popup-price { color: #059669; font-weight: 800; font-size: 1rem; margin: 2px 0 4px; }
.map-popup-loc { color: #6b7280; font-size: .75rem; }
.map-popup-btn {
    display: block; margin-top: 8px;
    background: #10b981; color: white;
    text-align: center; padding: 7px;
    border-radius: 8px; font-size: .8rem; font-weight: 700;
    text-decoration: none; transition: background .15s;
}
.map-popup-btn:hover { background: #059669; }

/* ── Map legend ── */
.map-legend {
    background: white; border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    padding: 10px 14px;
    font-size: .8rem;
    line-height: 2;
}
.legend-dot {
    display: inline-block; width: 12px; height: 12px;
    border-radius: 50%; margin-right: 6px; vertical-align: middle;
}
</style>
@endpush

<div class="min-h-screen bg-gray-50 pt-16 sm:pt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header --}}
        <div class="mb-5">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('products.page_title') }}</h1>
                    <p class="text-gray-500 mt-0.5 text-sm sm:text-base">{{ __('products.subtitle') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button onclick="toggleFilter()"
                        class="border border-green-600 text-green-600 px-3 py-2 rounded-xl font-semibold hover:bg-green-50 text-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        <span class="hidden sm:inline">{{ __('products.filter_btn') }}</span>
                    </button>
                    <button id="btn-toggle-view" onclick="toggleView()"
                        class="border border-green-600 text-green-600 px-3 py-2 rounded-xl font-semibold hover:bg-green-50 text-sm flex items-center gap-1.5">
                        <span id="toggle-icon">🗺</span>
                        <span class="hidden sm:inline" id="toggle-label">{{ __('products.map_view') }}</span>
                    </button>
                </div>
            </div>
            {{-- Search - full width on mobile --}}
            <form method="GET" action="{{ route('products.index') }}" class="flex items-center w-full">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('region'))   <input type="hidden" name="region"   value="{{ request('region') }}"> @endif
                @if(request('city'))     <input type="hidden" name="city"     value="{{ request('city') }}"> @endif
                @if(request('price_from')) <input type="hidden" name="price_from" value="{{ request('price_from') }}"> @endif
                @if(request('price_to'))   <input type="hidden" name="price_to"   value="{{ request('price_to') }}"> @endif
                <div class="relative flex items-center w-full">
                    <svg class="absolute left-3 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('products.search_placeholder') }}"
                           class="pl-9 {{ request('q') ? 'pr-7' : 'pr-3' }} py-2.5 w-full rounded-xl border border-gray-200 bg-white shadow-sm text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    @if(request('q'))
                        <a href="{{ route('products.index', request()->except('q')) }}"
                           class="absolute right-2 text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Search result hint --}}
        @if(request('q'))
            <p class="text-sm text-gray-500 mb-4">
                «<span class="font-semibold text-gray-800">{{ request('q') }}</span>»
                {{ __('products.search_results', ['count' => $products->total()]) }}
            </p>
        @endif

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter backdrop (mobile only) --}}
        <div id="filter-backdrop" onclick="toggleFilter()"></div>

        {{-- Filter panel --}}
        <div id="filterBox"
             class="{{ request()->anyFilled(['category','region','city','price_from','price_to']) ? '' : 'hidden' }}
                    bg-white rounded-2xl shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-800">{{ __('products.filter_title') }}</h2>
                <button onclick="toggleFilter()" class="sm:hidden text-gray-400 hover:text-gray-600 p-1 -mr-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="GET" action="{{ route('products.index') }}">
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <select name="category"
                        class="rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">{{ __('products.all_categories') }}</option>
                        @foreach($categories as $parent)
                            <option value="{{ $parent->id }}"
                                @selected(request('category') == $parent->id)
                                class="font-semibold">{{ $parent->name }}</option>
                            @foreach($parent->children as $child)
                                <option value="{{ $child->id }}"
                                    @selected(request('category') == $child->id)>
                                    &nbsp;&nbsp;— {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>

                    <select name="region" id="regionSelect"
                        class="rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">{{ __('products.all_regions') }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(request('region') == $region->id)>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="city" id="citySelect"
                        class="rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">{{ __('products.all_cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                data-region="{{ $city->region_id }}"
                                @selected(request('city') == $city->id)>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>

                    <script>
                    (function () {
                        const regionSel = document.getElementById('regionSelect');
                        const citySel   = document.getElementById('citySelect');
                        const allOpts   = Array.from(citySel.querySelectorAll('option'));

                        function filterCities(regionId) {
                            allOpts.forEach(opt => {
                                if (!opt.value) return; // "Barcha tumanlar"
                                opt.hidden = regionId && opt.dataset.region != regionId;
                            });
                            // tanlangan shahar boshqa viloyatga tegishli bo'lsa reset
                            const sel = citySel.querySelector('option:checked');
                            if (sel && sel.value && sel.dataset.region != regionId && regionId) {
                                citySel.value = '';
                            }
                        }

                        regionSel.addEventListener('change', () => filterCities(regionSel.value));
                        filterCities(regionSel.value); // sahifa yuklanganda ham ishlaydi
                    })();
                    </script>

                    <div class="flex gap-2">
                        <input type="number" name="price_from" placeholder="{{ __('products.price_from') }}"
                            value="{{ request('price_from') }}"
                            class="w-1/2 rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        <input type="number" name="price_to" placeholder="{{ __('products.price_to') }}"
                            value="{{ request('price_to') }}"
                            class="w-1/2 rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex gap-2">
                        <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 text-sm">
                            {{ __('products.search') }}
                        </button>
                        <a href="{{ route('products.index') }}"
                            class="px-6 py-2 border border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 text-sm flex items-center">
                            {{ __('products.clear') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════ CARDS VIEW ══════════════ --}}
        <div id="cards-view">
            @if($products->isEmpty())
                <div class="text-center py-24 text-gray-400">
                    <p class="text-5xl mb-4">🐄</p>
                    <p class="text-xl font-semibold">{{ __('products.no_results') }}</p>
                    <p class="text-sm mt-1">{{ __('products.no_results_hint') }}</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden flex flex-col group">
                            <div class="h-36 sm:h-48 bg-gradient-to-br from-green-100 to-green-200 overflow-hidden relative">
                                @if($product->primary_image_url)
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-6xl">
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
                                @if($product->gender)
                                    <span class="absolute top-2 left-2 bg-white bg-opacity-90 text-gray-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                        {{ $product->gender === 'erkak' ? '♂' : '♀' }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-3 sm:p-4 flex flex-col flex-1">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold truncate max-w-[90px] sm:max-w-[130px]">
                                        {{ $product->category?->name ?? '—' }}
                                    </span>
                                    <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">
                                        {{ $product->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-green-600 font-bold text-base sm:text-xl mt-1">{{ $product->formatted_price }}</p>

                                <div class="mt-1 text-gray-500 text-xs space-y-0.5">
                                    <p class="truncate">📍 {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') ?: '—' }}</p>
                                    <p class="hidden sm:block">⚖️ {{ $product->weight }} kg · {{ $product->age }} {{ __('products.age_unit') }}</p>
                                </div>

                                <a href="{{ route('products.show', $product) }}"
                                    class="mt-3 block text-center border border-green-600 text-green-600 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold hover:bg-green-600 hover:text-white transition active:bg-green-700 active:text-white">
                                    {{ __('products.view_detail') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">{{ $products->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- ══════════════ MAP VIEW ══════════════ --}}
        <div id="map-view" class="hidden relative">
            {{-- Legend --}}
            <div class="absolute top-3 right-3 z-[1000] map-legend" style="min-width:160px">
                <p class="font-bold text-gray-700 mb-1 text-xs uppercase tracking-wide">{{ __('products.map_legend') }}</p>
                <div><span class="legend-dot" style="background:#10b981"></span>🐄 Qoramol</div>
                <div><span class="legend-dot" style="background:#3b82f6"></span>🐑 Qo'y &amp; Echki</div>
                <div><span class="legend-dot" style="background:#f59e0b"></span>🐴 Ot &amp; Tuya</div>
                <div><span class="legend-dot" style="background:#8b5cf6"></span>🐾 Boshqa</div>
                <hr class="my-1.5 border-gray-100">
                <p class="text-gray-400 text-xs" id="mapCount">
                    {{ $mapProducts->count() }} {{ __('products.showing') }}
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Filter toggle ─────────────────────────────────────────
function toggleFilter() {
    const box      = document.getElementById('filterBox');
    const backdrop = document.getElementById('filter-backdrop');
    const isMobile = window.innerWidth < 768;

    if (isMobile) {
        const isHidden = box.classList.contains('hidden');
        box.classList.toggle('hidden');
        backdrop.classList.toggle('open', isHidden);
        // prevent body scroll when filter open on mobile
        document.body.style.overflow = isHidden ? 'hidden' : '';
    } else {
        box.classList.toggle('hidden');
    }
}

// ── Close filter on Escape ────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const box = document.getElementById('filterBox');
        if (!box.classList.contains('hidden')) toggleFilter();
    }
});

const MAP_PRODUCTS = @json($mapProducts);
const VIEW_DETAIL_TEXT = '{{ __('products.view_detail') }}';
const ON_MAP_TEXT = '{{ __('products.on_map') }}';

// ── Emoji & pin colour by category name ──────────────────
const ANIMAL_MAP = {
    // child categories
    'Sigir':  { emoji: '🐄', cls: 'pin-qoramol' },
    'Buqa':   { emoji: '🐄', cls: 'pin-qoramol' },
    'Buzoq':  { emoji: '🐄', cls: 'pin-qoramol' },
    "Qo'y":   { emoji: '🐑', cls: 'pin-qoy'     },
    'Echki':  { emoji: '🐐', cls: 'pin-qoy'     },
    'Ot':     { emoji: '🐴', cls: 'pin-ot'      },
    'Tuya':   { emoji: '🐪', cls: 'pin-ot'      },
    // parent categories (fallback)
    'Qoramol':        { emoji: '🐄', cls: 'pin-qoramol' },
    "Qo'y va echki":  { emoji: '🐑', cls: 'pin-qoy'     },
    'Ot va tuya':     { emoji: '🐴', cls: 'pin-ot'      },
};

function getAnimal(category, parent) {
    return ANIMAL_MAP[category] || ANIMAL_MAP[parent] || { emoji: '🐾', cls: 'pin-default' };
}

function createPin(emoji, cls) {
    return L.divIcon({
        html: `<div class="animal-pin ${cls}"><span>${emoji}</span></div>`,
        className: 'animal-marker-wrap',
        iconSize:    [44, 44],
        iconAnchor:  [22, 44],
        popupAnchor: [0, -48],
    });
}

// ── Popup HTML ────────────────────────────────────────────
function buildPopup(p) {
    const imgHtml = p.img
        ? `<img class="map-popup-img" src="${p.img}" alt="${p.title}">`
        : `<div class="map-popup-img-placeholder">${getAnimal(p.category, p.parent).emoji}</div>`;

    return `
        <div style="width:210px">
            ${imgHtml}
            <div class="map-popup-body">
                <div class="map-popup-cat">${p.category || p.parent || ''}</div>
                <div class="map-popup-title">${p.title}</div>
                <div class="map-popup-price">${p.price}</div>
                ${p.loc ? `<div class="map-popup-loc">📍 ${p.loc}</div>` : ''}
                <a class="map-popup-btn" href="${p.url}">${VIEW_DETAIL_TEXT} →</a>
            </div>
        </div>`;
}

// ── Map init ──────────────────────────────────────────────
let mapInstance = null;

function initMap() {
    if (mapInstance) return;

    mapInstance = L.map('map-view', { zoomControl: true })
        .setView([41.2995, 69.2401], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(mapInstance);

    // Add markers
    const bounds = [];
    MAP_PRODUCTS.forEach(p => {
        if (!p.lat || !p.lng) return;
        const { emoji, cls } = getAnimal(p.category, p.parent);
        const marker = L.marker([p.lat, p.lng], { icon: createPin(emoji, cls) })
            .addTo(mapInstance)
            .bindPopup(buildPopup(p), {
                maxWidth: 230,
                className: 'animal-popup',
                closeButton: true,
            });
        bounds.push([p.lat, p.lng]);
    });

    // Fit map to markers if any
    if (bounds.length > 0) {
        mapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 10 });
    }

    // Update count label
    document.getElementById('mapCount').textContent =
        MAP_PRODUCTS.length + ' ' + ON_MAP_TEXT;
}

// ── View toggle ───────────────────────────────────────────
let currentView = 'cards';

function setView(v) {
    const cardsEl = document.getElementById('cards-view');
    const mapEl   = document.getElementById('map-view');
    const icon    = document.getElementById('toggle-icon');
    const label   = document.getElementById('toggle-label');

    currentView = v;

    if (v === 'map') {
        cardsEl.classList.add('hidden');
        mapEl.classList.remove('hidden');
        icon.textContent  = '▦';
        label.textContent = '{{ __('products.cards_view') }}';
        requestAnimationFrame(() => {
            if (!mapInstance) initMap();
            else mapInstance.invalidateSize();
        });
    } else {
        cardsEl.classList.remove('hidden');
        mapEl.classList.add('hidden');
        icon.textContent  = '🗺';
        label.textContent = '{{ __('products.map_view') }}';
    }
}

function toggleView() {
    setView(currentView === 'cards' ? 'map' : 'cards');
}

@if(request('view') === 'map')
document.addEventListener('DOMContentLoaded', () => setView('map'));
@endif
</script>
@endpush
</x-app-layout>
