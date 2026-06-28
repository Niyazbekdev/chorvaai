<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />

            <div class="flex mt-1">
                <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-100 rounded-l-md text-gray-600">
                    +998
                </span>

                <x-text-input
                    id="phone"
                    class="block w-full rounded-l-none"
                    type="text"
                    name="phone"
                    :value="old('phone') ? str_replace('+998', '', old('phone')) : ''"
                    placeholder="901234567"
                    maxlength="9"
                    required
                    autofocus
                    autocomplete="tel" />
            </div>

            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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

                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        const phoneInput = document.getElementById('phone');
        const loginForm = document.getElementById('loginForm');

        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });

        loginForm.addEventListener('submit', function () {
            if (phoneInput.value && !phoneInput.value.startsWith('+998')) {
                phoneInput.value = '+998' + phoneInput.value;
            }
        });
    </script>
</x-guest-layout>
