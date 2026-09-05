<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-8 pb-16">
    <div class="max-w-xl mx-auto px-4 sm:px-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('banners.create_title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{!! __('banners.create_desc') !!}</p>
        </div>

        <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data"
              class="space-y-5" x-data="{ preview: null }">
            @csrf

            {{-- Rasm --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-3">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('banners.img_section') }}</h2>
                <p class="text-xs text-gray-400">{{ __('banners.img_hint') }}</p>

                <label class="block cursor-pointer">
                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden hover:border-green-400 transition"
                         style="min-height:160px">
                        <template x-if="preview">
                            <img :src="preview" class="w-full object-cover" style="max-height:220px">
                        </template>
                        <template x-if="!preview">
                            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                                <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="mt-2 text-sm">{{ __('banners.img_click') }}</span>
                            </div>
                        </template>
                    </div>
                    <input type="file" name="image" accept="image/*" class="hidden"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </label>
                @error('image') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- Matnlar --}}
            <div class="bg-white rounded-2xl shadow p-6 space-y-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ __('banners.info_section') }}</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('banners.title_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" maxlength="100"
                           class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('banners.contact_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact" value="{{ old('contact') }}" maxlength="50"
                           placeholder="+998 90 000 00 00"
                           class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                    @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('banners.url_label') }} <span class="text-gray-400 font-normal text-xs">{{ __('banners.url_optional') }}</span>
                    </label>
                    <input type="url" name="url" value="{{ old('url') }}"
                           placeholder="https://example.uz"
                           class="w-full rounded-xl border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm">
                    @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Narx va yuborish --}}
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">{{ __('banners.period_30') }}</p>
                    <p class="text-2xl font-bold text-green-700">30 000 so'm</p>
                </div>
                <button type="submit"
                        class="bg-green-600 text-white font-bold px-7 py-3 rounded-xl hover:bg-green-700 transition text-sm">
                    {{ __('banners.continue_btn') }}
                </button>
            </div>

        </form>
    </div>
</div>
</x-app-layout>
