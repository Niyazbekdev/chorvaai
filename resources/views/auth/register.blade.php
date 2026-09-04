<x-guest-layout>
    <div>
        <h1 class="font-serif text-3xl font-bold text-ink mb-1">Akkaunt yaratish</h1>
        <p class="text-ink-2 text-sm mb-7">ChorvaAI platformasiga qo'shiling</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Ism + Familiya --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Ism</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                           required autofocus autocomplete="given-name" placeholder="Jasur"
                           style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                    <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Familiya</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                           required autocomplete="family-name" placeholder="Toshmatov"
                           style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                    <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                </div>
            </div>

            {{-- Telefon --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Telefon raqam</label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-xl font-semibold text-sm"
                          style="background:#EDF0E5;color:#191D14;border:1.5px solid #E2ECDF;border-right:none;">+998</span>
                    <input id="phone" type="text" name="phone"
                           value="{{ old('phone') ? str_replace('+998', '', old('phone')) : '' }}"
                           placeholder="90 123 45 67" maxlength="9" required
                           style="flex:1;border:1.5px solid #E2ECDF;border-left:none;border-radius:0 10px 10px 0;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                </div>
                <p class="text-xs mt-1" style="color:#5C6352;">SMS orqali tasdiqlash kodi yuboriladi</p>
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Email manzil</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="jasur@example.com"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Parol</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="••••••••••"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">Parolni tasdiqlang</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="••••••••••"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            {{-- Submit --}}
            <button type="submit"
                    style="width:100%;background:#1D3520;color:white;padding:13px;border-radius:10px;font-weight:700;font-size:.95rem;border:none;cursor:pointer;transition:background .2s;"
                    onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                Ro'yxatdan o'tish
            </button>

            <p class="text-center text-xs" style="color:#5C6352;">
                Davom etish orqali
                <a href="#" style="color:#1D3520;font-weight:600;text-decoration:none;">foydalanish shartlari</a>ga rozilik bildirasiz.
            </p>
        </form>

        <p class="text-center text-sm mt-5" style="color:#5C6352;">
            Akkauntingiz bormi?
            <a href="{{ route('login') }}" style="color:#1D3520;font-weight:700;text-decoration:none;">Kirish</a>
        </p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
        phoneInput.closest('form').addEventListener('submit', function () {
            if (phoneInput.value && !phoneInput.value.startsWith('+998')) {
                phoneInput.value = '+998' + phoneInput.value;
            }
        });
    });
    </script>
</x-guest-layout>
