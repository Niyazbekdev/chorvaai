<section x-data="phoneChange()">

    {{-- Joriy raqam --}}
    <div class="flex items-center justify-between p-4 rounded-xl mb-4" style="background:#F8FCF7;border:1.5px solid #EDF0E5;">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide mb-0.5" style="color:#5C6352;">Joriy raqam</p>
            <p class="font-bold tracking-wide text-ink">{{ auth()->user()->phone }}</p>
        </div>
        <button type="button" @click="step = (step === 0 ? 1 : 0)"
                style="font-size:.825rem;font-weight:700;color:#1D3520;background:none;border:none;cursor:pointer;padding:0;"
                x-text="step === 0 ? 'O\'zgartirish' : 'Bekor qilish'">
        </button>
    </div>

    {{-- SUCCESS --}}
    @if(session('status') === 'phone-updated')
        <div class="px-4 py-3 rounded-xl text-sm font-semibold mb-4" style="background:#E2ECDF;color:#1D3520;">
            ✓ Telefon raqam muvaffaqiyatli yangilandi
        </div>
    @endif

    {{-- Step 1: Yangi raqam kiritish --}}
    <div x-show="step === 1" x-transition class="space-y-4">
        @if(session('phone_change_pending'))
            <div x-init="step = 2"></div>
        @endif

        @if(session('dev_otp_change'))
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#FEF9C3;border:1px solid #fde047;color:#854d0e;">
                <p class="font-bold">Dev rejim — SMS yuborilmadi</p>
                <p class="mt-1 font-mono font-bold text-xl tracking-widest">{{ session('dev_otp_change') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.phone.request') }}" id="phoneRequestForm">
            @csrf
            <div>
                <label class="field-label" for="new_phone">Yangi telefon raqam</label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-xl font-bold text-sm"
                          style="background:#EDF0E5;color:#191D14;border:1.5px solid #E2ECDF;border-right:none;">+998</span>
                    <input id="new_phone" name="new_phone" type="text"
                           value="{{ old('new_phone') }}" placeholder="90 123 45 67" maxlength="9"
                           inputmode="numeric" autocomplete="off"
                           style="flex:1;border:1.5px solid #E2ECDF;border-left:none;border-radius:0 10px 10px 0;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                </div>
                @error('new_phone')
                    <p class="text-xs mt-1 font-semibold" style="color:#A34F30;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    style="margin-top:14px;background:#1D3520;color:white;padding:12px 24px;border-radius:10px;font-weight:700;font-size:.875rem;border:none;cursor:pointer;"
                    onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                SMS kod yuborish
            </button>
        </form>
    </div>

    {{-- Step 2: OTP kiritish --}}
    <div x-show="step === 2" x-transition class="space-y-4">
        @if(session('phone_change_pending'))
            @if(session('dev_otp_change'))
                <div class="px-4 py-3 rounded-xl text-sm" style="background:#FEF9C3;border:1px solid #fde047;color:#854d0e;">
                    <p class="font-bold">Dev rejim — kodni kiriting</p>
                    <p class="font-mono font-bold text-xl tracking-widest mt-1">{{ session('dev_otp_change') }}</p>
                </div>
            @endif

            <p class="text-sm" style="color:#5C6352;">
                <span class="font-bold text-ink">{{ session('phone_change_pending') }}</span>
                raqamiga yuborilgan 6 xonali kodni kiriting
            </p>

            @if(session('status') === 'phone-otp-resent')
                <div class="px-4 py-3 rounded-xl text-sm" style="background:#E2ECDF;color:#1D3520;">
                    Kod qayta yuborildi
                </div>
            @endif

            <form method="POST" action="{{ route('profile.phone.verify') }}">
                @csrf
                <label class="field-label" for="phone_code">Tasdiqlash kodi</label>
                <input id="phone_code" name="code" type="text"
                       inputmode="numeric" pattern="\d{6}" maxlength="6"
                       placeholder="• • • • • •" autofocus autocomplete="one-time-code"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:14px;font-size:1.5rem;font-weight:700;text-align:center;letter-spacing:.5em;outline:none;box-sizing:border-box;font-family:monospace;transition:border-color .15s;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                @error('code')
                    <p class="text-xs mt-1 font-semibold" style="color:#A34F30;">{{ $message }}</p>
                @enderror

                <div class="flex items-center gap-4 mt-4">
                    <button type="submit"
                            style="background:#1D3520;color:white;padding:12px 24px;border-radius:10px;font-weight:700;font-size:.875rem;border:none;cursor:pointer;"
                            onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                        Tasdiqlash
                    </button>
                    <form method="POST" action="{{ route('profile.phone.resend') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="font-size:.825rem;font-weight:600;color:#3E683F;background:none;border:none;cursor:pointer;padding:0;">
                            Qayta yuborish
                        </button>
                    </form>
                </div>
            </form>

            <form method="POST" action="{{ route('profile.phone.cancel') }}" class="mt-2">
                @csrf
                <button type="submit" style="font-size:.75rem;color:#5C6352;background:none;border:none;cursor:pointer;padding:0;">
                    Bekor qilish
                </button>
            </form>
        @endif
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
