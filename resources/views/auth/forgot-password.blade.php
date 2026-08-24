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
            <x-input-label for="identifier" :value="__('auth.login_identifier')" />
            <x-text-input
                id="identifier"
                class="block mt-1 w-full"
                type="text"
                name="identifier"
                :value="old('identifier')"
                placeholder="{{ __('auth.login_placeholder') }}"
                required
                autofocus
                autocomplete="username" />
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
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
</x-guest-layout>
