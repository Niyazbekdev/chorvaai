<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Telefon raqamingizni kiriting. Biz ushbu raqamga tasdiqlash kodini yuboramiz.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
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

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('SMS kod yuborish') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        const phoneInput = document.getElementById('phone');
        const forgotForm = document.getElementById('forgotForm');

        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });

        forgotForm.addEventListener('submit', function () {
            if (phoneInput.value && !phoneInput.value.startsWith('+998')) {
                phoneInput.value = '+998' + phoneInput.value;
            }
        });
    </script>
</x-guest-layout>
