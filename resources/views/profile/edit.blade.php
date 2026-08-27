<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('profile.profile_title') }}</h2>
            <a href="{{ route('profile.my-products') }}"
               class="text-sm text-green-600 font-semibold hover:underline">
                {{ __('profile.my_listings') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Shaxsiy ma'lumotlar --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Telefon raqamni o'zgartirish --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.change-phone-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
