<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email yoki Telefon -->
        <div>
            <x-input-label for="login" :value="__('auth.login_identifier')" />
            <x-text-input
                id="login"
                class="block mt-1 w-full"
                type="text"
                name="login"
                :value="old('login')"
                required
                autofocus
                autocomplete="username"
                placeholder="{{ __('auth.login_placeholder') }}" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('password.request') }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('auth.forgot_password') }}
            </a>
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('register') }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('auth.no_account') }}
            </a>
        </div>
    </form>

    <div class="mt-6 flex items-center gap-3">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400">{{ __('auth.or') }}</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <div class="mt-4">
        <x-google-button>{{ __('auth.google_login') }}</x-google-button>
    </div>
</x-guest-layout>
