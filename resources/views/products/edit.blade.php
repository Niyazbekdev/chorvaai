<x-app-layout>
    <div class="min-h-screen bg-gray-50 pt-24 pb-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">E'lonni tahrirlash</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $product->name }}</p>
            </div>

            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
                class="bg-white rounded-2xl shadow p-8 space-y-6">
                @csrf
                @method('PUT')

                {{-- Asosiy ma'lumotlar --}}
                <fieldset class="space-y-4">
                    <legend class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Asosiy</legend>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Narx (so'm) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0"
                            class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rasm</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($product->image) }}" alt="Joriy rasm"
                                    class="h-24 w-36 object-cover rounded-xl border border-gray-200">
                                <p class="text-xs text-gray-400 mt-1">Joriy rasm. Yangi fayl yuklasangiz o'rnini almashtiradi.</p>
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </fieldset>

                <hr class="border-gray-100">

                <fieldset class="space-y-4">
                    <legend class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Tasnif</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategoriya <span class="text-red-500">*</span></label>
                            <select name="category_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tur <span class="text-red-500">*</span></label>
                            <select name="type_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}"
                                        @selected(old('type_id', $product->type_id) == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rang <span class="text-red-500">*</span></label>
                            <select name="color_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}"
                                        @selected(old('color_id', $product->color_id) == $color->id)>
                                        {{ $color->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('color_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Holat <span class="text-red-500">*</span></label>
                            <select name="status_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}"
                                        @selected(old('status_id', $product->status_id) == $status->id)>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                <hr class="border-gray-100">

                <fieldset class="space-y-4">
                    <legend class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Fizik xususiyatlar</legend>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Yoshi (yil) <span class="text-red-500">*</span></label>
                            <input type="number" name="age" value="{{ old('age', $product->age) }}" min="0" max="100"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vazni (kg) <span class="text-red-500">*</span></label>
                            <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" min="0"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                            @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                <hr class="border-gray-100">

                <fieldset class="space-y-4">
                    <legend class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Joylashuv</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Viloyat <span class="text-red-500">*</span></label>
                            <select name="region_id" id="region_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shahar/tuman <span class="text-red-500">*</span></label>
                            <select name="city_id"
                                class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">Tanlang...</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}"
                                        data-region="{{ $city->region_id }}"
                                        @selected(old('city_id', $product->city_id) == $city->id)>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('products.show', $product) }}"
                        class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 text-sm">
                        Bekor qilish
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 text-sm">
                        Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const regionSelect  = document.getElementById('region_id');
        const citySelect    = document.querySelector('[name="city_id"]');
        const allOptions    = Array.from(citySelect.options);
        const selectedCity  = {{ $product->city_id ?? 'null' }};

        function filterCities(keepSelected = false) {
            const regionId = regionSelect.value;
            citySelect.innerHTML = '';

            const placeholder = new Option('Tanlang...', '');
            citySelect.appendChild(placeholder);

            allOptions
                .filter(opt => opt.dataset.region === regionId)
                .forEach(opt => {
                    const clone = opt.cloneNode(true);
                    if (keepSelected && parseInt(opt.value) === selectedCity) {
                        clone.selected = true;
                    }
                    citySelect.appendChild(clone);
                });
        }

        regionSelect.addEventListener('change', () => filterCities(false));
        filterCities(true);
    </script>
</x-app-layout>
