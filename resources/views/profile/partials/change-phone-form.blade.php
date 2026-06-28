<section x-data="phoneChange()">
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('profile.phone_title') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('profile.phone_desc') }}</p>
    </header>

    <div class="mt-6">
        {{-- Joriy raqam --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('profile.current_phone') }}</p>
                <p class="font-semibold text-gray-800 tracking-wide">{{ auth()->user()->phone }}</p>
            </div>
            <button type="button" @click="step = (step === 0 ? 1 : 0)"
                    class="text-sm text-emerald-600 font-semibold hover:text-emerald-700 transition">
                <span x-text="step === 0 ? '{{ __('profile.change_btn') }}' : '{{ __('profile.cancel') }}'"></span>
            </button>
        </div>

        {{-- SUCCESS --}}
        @if(session('status') === 'phone-updated')
            <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                ✓ {{ __('profile.phone_updated') }}
            </div>
        @endif

        {{-- Step 1: Yangi raqam kiritish --}}
        <div x-show="step === 1" x-transition class="mt-4">
            @if(session('phone_change_pending'))
                {{-- Step 2: OTP kiritish --}}
                <div x-init="step = 2"></div>
            @endif

            @if(session('dev_otp_change'))
                <div class="mb-3 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm">
                    <p class="font-semibold">{{ __('auth.dev_otp_title') }}</p>
                    <p class="mt-1">{{ __('auth.dev_otp_code') }} <span class="font-mono font-bold text-lg tracking-widest">{{ session('dev_otp_change') }}</span></p>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.phone.request') }}" id="phoneRequestForm">
                @csrf
                <x-input-label for="new_phone" :value="__('profile.new_phone')" />
                <div class="flex mt-1">
                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-200 bg-gray-100 rounded-l-xl text-gray-500 text-sm font-medium">
                        +998
                    </span>
                    <x-text-input id="new_phone" name="new_phone" type="text"
                        class="block w-full rounded-l-none"
                        :value="old('new_phone')"
                        placeholder="901234567" maxlength="9"
                        inputmode="numeric" autocomplete="off" />
                </div>
                <x-input-error :messages="$errors->get('new_phone')" class="mt-2" />

                <x-primary-button class="mt-4">
                    {{ __('auth.send_sms') }}
                </x-primary-button>
            </form>
        </div>

        {{-- Step 2: OTP kiritish --}}
        <div x-show="step === 2" x-transition class="mt-4">
            @if(session('phone_change_pending'))
                @if(session('dev_otp_change'))
                    <div class="mb-3 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm">
                        <p class="font-semibold">{{ __('auth.dev_otp_title') }}</p>
                        <p class="mt-1">{{ __('auth.dev_otp_code') }} <span class="font-mono font-bold text-lg tracking-widest">{{ session('dev_otp_change') }}</span></p>
                    </div>
                @endif

                <p class="text-sm text-gray-500 mb-4">
                    <span class="font-semibold text-gray-700">{{ session('phone_change_pending') }}</span>
                    {{ __('auth.enter_code_desc') }}
                </p>

                @if(session('status') === 'phone-otp-resent')
                    <div class="mb-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                        {{ __('auth.resend') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.phone.verify') }}">
                    @csrf
                    <x-input-label for="phone_code" :value="__('auth.verify_code_label')" />
                    <x-text-input id="phone_code" name="code" type="text"
                        inputmode="numeric" pattern="\d{6}" maxlength="6"
                        class="block mt-1 w-full text-center text-2xl tracking-[0.5em] font-bold"
                        placeholder="• • • • • •" autofocus autocomplete="one-time-code" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />

                    <div class="flex items-center gap-4 mt-4">
                        <x-primary-button>{{ __('auth.verify_btn') }}</x-primary-button>

                        <form method="POST" action="{{ route('profile.phone.resend') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-blue-600 hover:underline">
                                {{ __('auth.resend') }}
                            </button>
                        </form>
                    </div>
                </form>

                <div class="mt-3">
                    <form method="POST" action="{{ route('profile.phone.cancel') }}">
                        @csrf
                        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">
                            {{ __('profile.cancel') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
function phoneChange() {
    return {
        step: {{ session('phone_change_pending') ? 2 : 0 }},
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const inp = document.getElementById('new_phone');
    if (inp) {
        inp.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    }
    const form = document.getElementById('phoneRequestForm');
    if (form) {
        form.addEventListener('submit', function () {
            const p = document.getElementById('new_phone');
            if (p && p.value && !p.value.startsWith('+998')) {
                p.value = '+998' + p.value;
            }
        });
    }
    const codeInp = document.getElementById('phone_code');
    if (codeInp) {
        codeInp.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
});
</script>
