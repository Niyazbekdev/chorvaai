<x-guest-layout>
    <div>
        <h1 class="font-serif text-3xl font-bold text-ink mb-1">{{ __('auth.Welcome back') }}</h1>
        <p class="text-ink-2 text-sm mb-7">{{ __('auth.sign_in_desc') }}</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Phone --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">
                    {{ __('auth.phone_number') }}
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-xl font-semibold text-sm"
                          style="background:#EDF0E5;color:#191D14;border:1.5px solid #E2ECDF;border-right:none;">
                        +998
                    </span>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                           required autofocus autocomplete="username"
                           placeholder="90 123 45 67"
                           style="flex:1;border:1.5px solid #E2ECDF;border-left:none;border-radius:0 10px 10px 0;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;"
                           onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wide" style="color:#5C6352;">{{ __('auth.password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="color:#3E683F;font-size:.82rem;font-weight:600;text-decoration:none;">
                            {{ __('auth.forgot_password') }}
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••••"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;color:#191D14;transition:border-color .15s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            {{-- Submit --}}
            <button type="submit"
                    style="width:100%;background:#1D3520;color:white;padding:13px;border-radius:10px;font-weight:700;font-size:.95rem;border:none;cursor:pointer;transition:background .2s;margin-top:4px;"
                    onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                {{ __('auth.log_in') }}
            </button>

        </form>

        <p class="text-center text-sm mt-6" style="color:#5C6352;">
            {{ __('auth.no_account') }}
            <a href="{{ route('register') }}" style="color:#1D3520;font-weight:700;text-decoration:none;">
                {{ __('auth.register_now') }}
            </a>
        </p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });
    </script>
</x-guest-layout>
