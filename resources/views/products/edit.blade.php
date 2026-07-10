<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-6 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('products.edit_ad') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $product->name }}</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Asosiy --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.basic_info') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.title_label') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                        class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.description_label') }} <span class="text-gray-400 font-normal text-xs">(ixtiyoriy)</span></label>
                    <textarea name="description" rows="5"
                        class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.price_label') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.contact_phone_label') }}</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $product->contact_phone) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Rasmlar --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('products.images_section') }}</h2>

                {{-- Current images --}}
                @php $gallery = $product->gallery; @endphp
                @if(count($gallery) > 0)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($gallery as $img)
                            <img src="{{ Storage::url($img) }}" alt="{{ __('products.images_section') }}"
                                 class="h-20 w-24 object-cover rounded-xl border border-gray-200">
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400">{{ __('products.current_images_hint') }}</p>
                @endif

                <input type="file" name="images[]" id="editImages" multiple accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400">{{ __('products.images_hint_edit') }} — rasmlar avtomatik siqiladi</p>
                <p id="editImgStatus" class="text-xs text-green-600 font-medium hidden"></p>
                @error('images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                                        <option value="{{ $child->id }}"
                                            @selected(old('category_id', $product->category_id) == $child->id)>
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
                            <option value="erkak" @selected(old('gender', $product->gender) === 'erkak')>{{ __('products.male') }}</option>
                            <option value="urgochi" @selected(old('gender', $product->gender) === 'urgochi')>{{ __('products.female') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.color_label2') }}</label>
                        <select name="color_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            <option value="">{{ __('products.choose') }}</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}"
                                    @selected(old('color_id', $product->color_id) == $color->id)>
                                    {{ $color->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.status_label') }}</label>
                        <select name="status_id"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}"
                                    @selected(old('status_id', $product->status_id) == $status->id)>
                                    {{ $status->name }}
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
                        <input type="number" name="age" value="{{ old('age', $product->age) }}" min="0" max="100"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('products.weight_kg') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" min="0"
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
                                <option value="{{ $region->id }}"
                                    @selected(old('region_id', $product->region_id) == $region->id)>
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
                            <option value="">{{ __('products.choose') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-region="{{ $city->region_id }}"
                                    @selected(old('city_id', $product->city_id) == $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $product->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $product->longitude) }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('products.map_pick_hint_edit') }} <span class="text-gray-400 font-normal text-xs">(ixtiyoriy)</span>
                    </label>
                    <div id="pickMap"
                         class="rounded-xl overflow-hidden border-2 border-gray-200"
                         style="height:300px"></div>
                    <p class="text-xs mt-1 text-gray-400" id="mapCoords">
                        @if(old('latitude', $product->latitude) && old('longitude', $product->longitude))
                            {{ __('products.selected') }} {{ old('latitude', $product->latitude) }}, {{ old('longitude', $product->longitude) }}
                        @else
                            {{ __('products.map_point_hint') }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('products.show', $product) }}"
                    class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 text-sm">
                    {{ __('products.cancel') }}
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 text-sm">
                    {{ __('products.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Image compression — max 1920px, 85% JPEG
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
                w = Math.round(w * r); h = Math.round(h * r);
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

(function () {
    const form    = document.querySelector('form');
    const input   = document.getElementById('editImages');
    const status  = document.getElementById('editImgStatus');
    const saveBtn = form.querySelector('[type="submit"]');

    form.addEventListener('submit', async function (e) {
        if (!input.files.length) return;
        e.preventDefault();

        const origText    = saveBtn.textContent;
        saveBtn.disabled  = true;
        saveBtn.textContent = 'Rasmlar siqilmoqda...';

        let savedBytes = 0;
        const dt = new DataTransfer();
        for (const f of Array.from(input.files)) {
            const c = await compressImage(f);
            savedBytes += f.size - c.size;
            dt.items.add(c);
        }
        input.files = dt.files;

        if (savedBytes > 51200) {
            status.textContent = `✓ ${(savedBytes / 1048576).toFixed(1)} MB tejaldi`;
            status.classList.remove('hidden');
        }

        saveBtn.textContent = origText;
        saveBtn.disabled    = false;
        form.submit();
    });
})();

const regionSelect = document.getElementById('region_id');
const citySelect   = document.querySelector('[name="city_id"]');
const allOptions   = Array.from(citySelect.options);
const selectedCity = {{ $product->city_id ?? 'null' }};
const CHOOSE_TEXT   = '{{ __('products.choose') }}';
const SELECTED_TEXT = '{{ __('products.selected') }}';

function filterCities(keepSelected = false) {
    const regionId = regionSelect.value;
    citySelect.innerHTML = '';
    const ph = new Option(CHOOSE_TEXT, '');
    citySelect.appendChild(ph);
    allOptions.filter(o => o.dataset.region === regionId).forEach(o => {
        const clone = o.cloneNode(true);
        if (keepSelected && parseInt(o.value) === selectedCity) clone.selected = true;
        citySelect.appendChild(clone);
    });
}
regionSelect.addEventListener('change', () => filterCities(false));
filterCities(true);

const initLat = parseFloat('{{ $product->latitude ?? 41.2995 }}');
const initLng = parseFloat('{{ $product->longitude ?? 69.2401 }}');
const map = L.map('pickMap').setView([initLat, initLng], $product->latitude ? 13 : 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker = @if($product->latitude) L.marker([{{ $product->latitude }}, {{ $product->longitude }}]).addTo(map) @else null @endif;

map.on('click', function (e) {
    const { lat, lng } = e.latlng;
    document.getElementById('latitude').value  = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
    document.getElementById('mapCoords').textContent = `${SELECTED_TEXT} ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    if (marker) marker.setLatLng(e.latlng);
    else marker = L.marker(e.latlng).addTo(map);
});
</script>
@endpush
</x-app-layout>
