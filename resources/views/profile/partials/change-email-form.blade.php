<section x-data="emailChange()">
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('profile.email_title') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('profile.email_desc') }}</p>
    </header>

    <div class="mt-6">
        {{-- Joriy email --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('profile.current_email') }}</p>
                <p class="font-semibold text-gray-800">{{ auth()->user()->email ?: '—' }}</p>
            </div>
            <button type="button" @click="step = (step === 0 ? 1 : 0)"
                    class="text-sm text-emerald-600 font-semibold hover:text-emerald-700 transition">
                <span x-text="step === 0 ? '{{ __('profile.change_btn') }}' : '{{ __('profile.cancel') }}'"></span>
            </button>
        </div>

        {{-- SUCCESS --}}
        @if(session('status') === 'email-updated')
            <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                ✓ {{ __('profile.email_updated') }}
            </div>
        @endif

        {{-- Step 1: Yangi email kiritish --}}
        <div x-show="step === 1" x-transition class="mt-4">
            @if(session('email_change_pending'))
                <div x-init="step = 2"></div>
            @endif

            <form method="POST" action="{{ route('profile.email.request') }}">
                @csrf
                <x-input-label for="new_email" :value="__('profile.new_email')" />
                <x-text-input id="new_email" name="new_email" type="email"
                    class="block mt-1 w-full"
                    :value="old('new_email')"
                    placeholder="yangi@email.com"
                    autocomplete="off" />
                <x-input-error :messages="$errors->get('new_email')" class="mt-2" />

                <x-primary-button class="mt-4">
                    {{ __('auth.send_code') }}
                </x-primary-button>
            </form>
        </div>

        {{-- Step 2: OTP kiritish --}}
        <div x-show="step === 2" x-transition class="mt-4">
            @if(session('email_change_pending'))
                @if(session('dev_otp_email_change'))
                    <div class="mb-3 bg-blue-50 border-2 border-blue-400 text-blue-900 px-4 py-3 rounded-xl text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 mb-1">Email tasdiqlash kodi</p>
                        <p class="font-mono font-bold text-3xl tracking-[0.4em]">{{ session('dev_otp_email_change') }}</p>
                    </div>
                @endif

                <p class="text-sm text-gray-500 mb-4">
                    <span class="font-semibold text-gray-700">{{ session('email_change_pending') }}</span>
                    {{ __('auth.enter_code_desc') }}
                </p>

                @if(session('status') === 'email-otp-resent')
                    <div class="mb-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                        {{ __('auth.resend') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.email.verify') }}">
                    @csrf
                    <x-input-label for="email_code" :value="__('auth.verify_code_label')" />
                    <x-text-input id="email_code" name="code" type="text"
                        inputmode="numeric" pattern="\d{6}" maxlength="6"
                        class="block mt-1 w-full text-center text-2xl tracking-[0.5em] font-bold"
                        placeholder="• • • • • •" autofocus autocomplete="one-time-code" />
                    <x-input-error :messages="$errors->get('email_code')" class="mt-2" />

                    <div class="flex items-center gap-4 mt-4">
                        <x-primary-button>{{ __('auth.verify_btn') }}</x-primary-button>

                        <form method="POST" action="{{ route('profile.email.resend') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-blue-600 hover:underline">
                                {{ __('auth.resend') }}
                            </button>
                        </form>
                    </div>
                </form>

                <div class="mt-3">
                    <form method="POST" action="{{ route('profile.email.cancel') }}">
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
function emailChange() {
    return {
        step: {{ session('email_change_pending') ? 2 : 0 }},
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const codeInp = document.getElementById('email_code');
    if (codeInp) {
        codeInp.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
});
</script>
