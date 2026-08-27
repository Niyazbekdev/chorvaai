<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">🔐</div>
        <h2 class="text-xl font-bold text-gray-800">{{ __('auth.forgot_title') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('auth.forgot_desc') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="phone" :value="__('auth.login_identifier')" />
            <div class="flex mt-1">
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-600">
                    +998
                </span>
                <x-text-input
                    id="phone"
                    class="block w-full rounded-l-none"
                    type="text"
                    name="phone"
                    :value="old('phone')"
                    placeholder="{{ __('auth.login_placeholder') }}"
                    required
                    autofocus
                    autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mt-5">
            {{ __('auth.send_code') }}
        </x-primary-button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
            {{ __('auth.back_to_login') }}
        </a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });
    </script>
</x-guest-layout>
