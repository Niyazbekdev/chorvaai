<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">🔐</div>
        <h2 class="text-xl font-bold text-gray-800">Parolni tiklash</h2>
        <p class="text-sm text-gray-500 mt-1">Telefon raqamingizni kiriting — SMS kod yuboramiz</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf

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
                    inputmode="numeric"
                    autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mt-5">
            SMS kod yuborish
        </x-primary-button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
            ← Kirishga qaytish
        </a>
    </div>

    <script>
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
        document.getElementById('forgotForm').addEventListener('submit', function () {
            if (phoneInput.value && !phoneInput.value.startsWith('+998')) {
                phoneInput.value = '+998' + phoneInput.value;
            }
        });
    </script>
</x-guest-layout>
