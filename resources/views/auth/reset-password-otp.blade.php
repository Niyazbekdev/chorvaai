<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">📱</div>
        <h2 class="text-xl font-bold text-gray-800">{{ __('auth.enter_code_title') }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            <span class="font-medium text-gray-700">{{ $phone }}</span>
            {{ __('auth.enter_code_desc') }}
        </p>
    </div>

    @if(session('dev_otp'))
        <div class="mb-4 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm text-center">
            <p class="font-semibold">{{ __('auth.dev_otp_title') }}</p>
            <p class="mt-1">{{ __('auth.dev_otp_code') }} <span class="font-mono font-bold text-lg tracking-widest">{{ session('dev_otp') }}</span></p>
        </div>
    @endif

    @if(session('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('auth.verify_code_label')" />
            <x-text-input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                pattern="\d{6}"
                maxlength="6"
                class="block mt-1 w-full text-center text-2xl tracking-[0.5em] font-bold"
                placeholder="• • • • • •"
                autofocus
                autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mt-5">
            {{ __('auth.verify_btn') }}
        </x-primary-button>
    </form>

    <div class="mt-5 text-center">
        <form method="POST" action="{{ route('password.otp.resend') }}">
            @csrf
            <button
                type="submit"
                id="resendBtn"
                class="text-sm text-blue-600 hover:underline disabled:text-gray-400 disabled:no-underline disabled:cursor-not-allowed"
                {{ $resendSeconds > 0 ? 'disabled' : '' }}>
                {{ __('auth.resend') }}
                <span id="countdown" class="{{ $resendSeconds > 0 ? '' : 'hidden' }}">
                    (<span id="timer">{{ $resendSeconds }}</span>s)
                </span>
            </button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('password.request') }}" class="text-xs text-gray-400 hover:text-gray-600 underline">
            {{ __('auth.back_to_phone') }}
        </a>
    </div>

    <script>
    let seconds = {{ $resendSeconds }};
    const timerEl   = document.getElementById('timer');
    const countdown = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');

    if (seconds > 0) {
        const interval = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                countdown.classList.add('hidden');
                resendBtn.disabled = false;
            }
        }, 1000);
    }

    document.getElementById('code').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
    </script>
</x-guest-layout>
