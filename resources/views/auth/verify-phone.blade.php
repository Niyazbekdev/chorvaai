<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">📱</div>
        <h2 class="text-xl font-bold text-gray-800">Telefon raqamni tasdiqlang</h2>
        <p class="text-sm text-gray-500 mt-1">
            <span class="font-medium text-gray-700">{{ auth()->user()->phone }}</span>
            raqamiga 6 xonali kod yuborildi
        </p>
    </div>

    {{-- Dev: OTP kodni ko'rsatish (faqat test/local muhitda) --}}
    @if(session('dev_otp'))
        <div class="mb-4 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-xl text-sm text-center">
            <p class="font-semibold">🛠 Test rejimi — SMS yuborilmadi</p>
            <p class="mt-1">OTP kod: <span class="font-mono font-bold text-lg tracking-widest">{{ session('dev_otp') }}</span></p>
        </div>
    @endif

    {{-- Status --}}
    @if(session('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    {{-- OTP form --}}
    <form method="POST" action="{{ route('phone.verify.submit') }}">
        @csrf

        <div>
            <x-input-label for="code" value="Tasdiqlash kodi" />
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
            Tasdiqlash
        </x-primary-button>
    </form>

    {{-- Resend --}}
    <div class="mt-5 text-center">
        <form method="POST" action="{{ route('phone.verify.resend') }}">
            @csrf
            <button
                type="submit"
                id="resendBtn"
                class="text-sm text-blue-600 hover:underline disabled:text-gray-400 disabled:no-underline disabled:cursor-not-allowed"
                {{ $resendSeconds > 0 ? 'disabled' : '' }}>
                Kodni qayta yuborish
                <span id="countdown" class="{{ $resendSeconds > 0 ? '' : 'hidden' }}">
                    (<span id="timer">{{ $resendSeconds }}</span>s)
                </span>
            </button>
        </form>
    </div>

    <div class="mt-5 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">
                Boshqa akkaunt bilan kirish
            </button>
        </form>
    </div>
</x-guest-layout>

@push('scripts')
<script>
let seconds = {{ $resendSeconds }};
const timerEl  = document.getElementById('timer');
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

// Faqat raqam qabul qilish
document.getElementById('code').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
});
</script>
@endpush
