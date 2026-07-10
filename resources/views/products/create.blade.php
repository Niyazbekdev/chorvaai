<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-6 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('products.new_ad') }}</h1>
            <p class="text-gray-500 text-sm mt-1">* {{ __('products.all_required') }}</p>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
              class="space-y-6">
            @csrf

            {{-- Asosiy --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.basic_info') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.title_label') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm"
                        placeholder="Masalan: Sersut sigir, 3 yoshli">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.description_label') }} <span class="text-gray-400 font-normal text-xs">(ixtiyoriy)</span></label>
                    <textarea name="description" rows="5"
                        class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm"
                        placeholder="Hayvon haqida batafsil ma'lumot...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.price_label') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price') }}" min="0"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm"
                            placeholder="5000000">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.contact_phone_label') }}</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm"
                            placeholder="+998901234567">
                        @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Rasmlar --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.images_section') }}</h2>
                <p class="text-xs text-gray-400">{{ __('products.images_hint') }}</p>

                <div x-data="imageUpload()" class="space-y-3">
                    <label class="block w-full border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition"
                           :class="compressing ? 'pointer-events-none opacity-60' : ''"
                           @dragover.prevent @drop.prevent="handleDrop($event)">
                        <input type="file" name="images[]" multiple accept="image/*"
                               class="hidden" @change="handleFiles($event)" ref="fileInput">

                        {{-- Compressing state --}}
                        <div x-show="compressing" class="py-3">
                            <div class="flex items-center justify-center gap-2 text-green-600">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <span class="text-sm font-medium">Rasmlar optimallashtirilmoqda...</span>
                            </div>
                        </div>

                        {{-- Empty state --}}
                        <div x-show="!compressing && previews.length === 0">
                            <p class="text-3xl mb-2">📷</p>
                            <p class="text-sm text-gray-500">{{ __('products.drag_images') }} <span class="text-green-600 font-semibold">{{ __('products.select_images') }}</span></p>
                            <p class="text-xs text-gray-400 mt-1">Rasmlar avtomatik siqiladi — sifat saqlanadi</p>
                        </div>

                        {{-- Preview grid --}}
                        <div x-show="!compressing && previews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2" @click.prevent>
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative rounded-lg overflow-hidden" style="aspect-ratio:1">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <button type="button" @click.stop="removeImage(i)"
                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-600">
                                        ✕
                                    </button>
                                    <span x-show="i === 0"
                                        class="absolute bottom-1 left-1 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">
                                        {{ __('products.main_image') }}
                                    </span>
                                </div>
                            </template>
                            <label class="border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-green-400 transition"
                                   style="aspect-ratio:1" @click.stop="$refs.fileInput.click()">
                                <span class="text-2xl text-gray-400">+</span>
                            </label>
                        </div>
                    </label>

                    {{-- Size savings info --}}
                    <p x-show="savedMb > 0.05" x-cloak
                       class="text-xs text-green-600 font-medium">
                        ✓ <span x-text="savedMb.toFixed(1)"></span> MB tejaldi (avtomatik siqildi)
                    </p>
                </div>
                @error('images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tasnif --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.classification') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.category_label') }} <span class="text-red-500">*</span></label>
                        <select name="category_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose') }}</option>
                            @foreach($categories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}" @selected(old('category_id') == $child->id)>
                                            {{ $child->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.gender_label') }}</label>
                        <select name="gender"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose') }}</option>
                            <option value="erkak" @selected(old('gender') === 'erkak')>{{ __('products.male') }}</option>
                            <option value="urgochi" @selected(old('gender') === 'urgochi')>{{ __('products.female') }}</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.color_label2') }}</label>
                        <select name="color_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose') }}</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" @selected(old('color_id') == $color->id)>
                                    {{ $color->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Fizik xususiyatlar --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.physical') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.age_year') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="age" value="{{ old('age') }}" min="0" max="100"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.weight_kg') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight') }}" min="0"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Joylashuv --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.location_section') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.region_label') }} <span class="text-red-500">*</span></label>
                        <select name="region_id" id="region_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose') }}</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.city_label') }} <span class="text-red-500">*</span></label>
                        <select name="city_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose_region_first') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-region="{{ $city->region_id }}"
                                    @selected(old('city_id') == $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('products.map_pick_hint') }} <span class="text-gray-400 font-normal text-xs">(ixtiyoriy)</span>
                    </label>
                    <div id="pickMap"
                         class="rounded-xl overflow-hidden border-2 border-gray-200"
                         style="height:300px"></div>
                    <p class="text-xs mt-1 text-gray-400" id="mapCoords">
                        @if(old('latitude') && old('longitude'))
                            {{ __('products.selected') }} {{ old('latitude') }}, {{ old('longitude') }}
                        @else
                            {{ __('products.map_point_hint') }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('products.index') }}"
                    class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 text-sm">
                    {{ __('products.cancel') }}
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 text-sm">
                    {{ __('products.post_ad') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// City filter by region
const regionSelect = document.getElementById('region_id');
const citySelect   = document.querySelector('[name="city_id"]');
const allCityOpts  = Array.from(citySelect.options);
const CHOOSE_TEXT         = '{{ __('products.choose') }}';
const CHOOSE_REGION_TEXT  = '{{ __('products.choose_region_first') }}';
const SELECTED_TEXT       = '{{ __('products.selected') }}';
const MAP_POINT_TEXT      = '{{ __('products.map_point_hint') }}';

function filterCities() {
    const regionId = regionSelect.value;
    citySelect.innerHTML = '';
    const ph = new Option(regionId ? CHOOSE_TEXT : CHOOSE_REGION_TEXT, '');
    citySelect.appendChild(ph);
    allCityOpts.filter(o => o.dataset.region === regionId)
               .forEach(o => citySelect.appendChild(o.cloneNode(true)));
}
regionSelect.addEventListener('change', filterCities);
filterCities();

// Leaflet pick-a-point map
const initLat = parseFloat(document.getElementById('latitude').value) || 41.2995;
const initLng = parseFloat(document.getElementById('longitude').value) || 69.2401;
const map = L.map('pickMap').setView([initLat, initLng], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker = null;
if (document.getElementById('latitude').value) {
    marker = L.marker([initLat, initLng]).addTo(map);
}

map.on('click', function (e) {
    const { lat, lng } = e.latlng;
    document.getElementById('latitude').value  = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
    document.getElementById('mapCoords').textContent = `${SELECTED_TEXT} ${lat.toFixed(5)}, ${lng.toFixed(5)}`;

    if (marker) marker.setLatLng(e.latlng);
    else marker = L.marker(e.latlng).addTo(map);
});

// Compress image via canvas: max 1920px, 85% JPEG — like Telegram/WhatsApp
function compressImage(file) {
    return new Promise(resolve => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const MAX = 1920;
            let w = img.naturalWidth, h = img.naturalHeight;
            if (w > MAX || h > MAX) {
                const r = Math.min(MAX / w, MAX / h);
                w = Math.round(w * r);
                h = Math.round(h * r);
            }
            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            const name = file.name.replace(/\.[^.]+$/, '.jpg');
            canvas.toBlob(blob => {
                resolve(new File([blob], name, { type: 'image/jpeg' }));
            }, 'image/jpeg', 0.85);
        };
        img.onerror = () => { URL.revokeObjectURL(url); resolve(file); };
        img.src = url;
    });
}

function imageUpload() {
    return {
        previews: [],
        files: [],
        compressing: false,
        savedMb: 0,

        async handleFiles(e) { await this.addFiles(Array.from(e.target.files)); },
        async handleDrop(e) { await this.addFiles(Array.from(e.dataTransfer.files)); },

        async addFiles(newFiles) {
            this.compressing = true;
            for (const f of newFiles) {
                if (this.files.length >= 8) break;
                if (!f.type.startsWith('image/')) continue;
                const compressed = await compressImage(f);
                this.savedMb += (f.size - compressed.size) / 1048576;
                this.files.push(compressed);
                await new Promise(res => {
                    const reader = new FileReader();
                    reader.onload = ev => { this.previews.push(ev.target.result); res(); };
                    reader.readAsDataURL(compressed);
                });
            }
            this.compressing = false;
            this.syncInput();
        },

        removeImage(i) {
            this.previews.splice(i, 1);
            this.files.splice(i, 1);
            this.syncInput();
        },

        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            this.$refs.fileInput.files = dt.files;
        }
    };
}
</script>
@endpush
</x-app-layout>
