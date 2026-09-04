<x-app-layout>
@push('styles')
<style>
.profile-nav-link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 10px;
    font-size: .9rem; font-weight: 600; color: #5C6352;
    text-decoration: none; transition: background .15s, color .15s;
}
.profile-nav-link:hover { background: #EDF0E5; color: #1D3520; }
.profile-nav-link.active { background: #E2ECDF; color: #1D3520; }
.profile-nav-link svg { flex-shrink: 0; }

.field-input {
    width: 100%; border: 1.5px solid #E2ECDF; border-radius: 10px;
    padding: 11px 14px; font-size: .9rem; outline: none;
    background: white; color: #191D14; transition: border-color .15s;
    box-sizing: border-box; font-family: inherit;
}
.field-input:focus { border-color: #3E683F; }
.field-label {
    display: block; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: #5C6352; margin-bottom: 6px;
}
.section-card { background: white; border-radius: 20px; padding: 28px 28px; border: 1px solid #EDF0E5; }
</style>
@endpush

<div class="min-h-screen pb-16" style="background:#F8FCF7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="flex gap-6">

            {{-- ═══ LEFT SIDEBAR ═══ --}}
            <div class="hidden lg:block w-[220px] flex-shrink-0">
                <div class="bg-white rounded-2xl p-5 sticky top-[72px]" style="border:1px solid #EDF0E5;">

                    {{-- User info --}}
                    <div class="flex flex-col items-center text-center mb-6 pb-5" style="border-bottom:1px solid #EDF0E5;">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" alt=""
                                 class="w-14 h-14 rounded-full object-cover mb-3">
                        @else
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg mb-3"
                                 style="background:#E2ECDF;color:#1D3520;">
                                {{ mb_strtoupper(mb_substr(auth()->user()->first_name,0,1).mb_substr(auth()->user()->last_name,0,1)) }}
                            </div>
                        @endif
                        <p class="font-bold text-sm text-ink leading-tight">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                        <span class="mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full" style="background:#E2ECDF;color:#3E683F;">
                            Tasdiqlangan
                        </span>
                    </div>

                    {{-- Nav --}}
                    <nav class="space-y-1">
                        <a href="{{ route('profile.my-products') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Mening e'lonlarim
                        </a>
                        <a href="{{ route('profile.favorites') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Sevimlilar
                        </a>
                        <a href="{{ route('profile.edit') }}" class="profile-nav-link active">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil sozlamalari
                        </a>
                    </nav>
                </div>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Header --}}
                <div>
                    <h1 class="font-serif text-2xl font-bold text-ink">Profil sozlamalari</h1>
                    <p class="text-sm mt-0.5" style="color:#5C6352;">Shaxsiy ma'lumotlaringizni tahrirlang</p>
                </div>

                {{-- Flash --}}
                @if(session('status') === 'profile-updated')
                    <div class="px-4 py-3 rounded-xl text-sm font-semibold" style="background:#E2ECDF;color:#1D3520;">
                        ✓ Ma'lumotlar saqlandi
                    </div>
                @endif
                @if(session('status') === 'phone-changed')
                    <div class="px-4 py-3 rounded-xl text-sm font-semibold" style="background:#E2ECDF;color:#1D3520;">
                        ✓ Telefon raqam yangilandi
                    </div>
                @endif

                {{-- Profile info card --}}
                <div class="section-card">
                    <h2 class="font-bold text-base text-ink mb-1">Shaxsiy ma'lumotlar</h2>
                    <p class="text-sm mb-6" style="color:#5C6352;">Ism, familiya va profilingiz rasmini yangilang</p>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        {{-- Avatar --}}
                        <div class="flex items-center gap-5 pb-5" style="border-bottom:1px solid #EDF0E5;">
                            <div class="relative group cursor-pointer" onclick="document.getElementById('avatar_input').click()">
                                @if(auth()->user()->avatar)
                                    <img id="avatarPreviewImg" src="{{ auth()->user()->avatar_url }}" alt="avatar"
                                         class="w-20 h-20 rounded-full object-cover" style="border:3px solid #E2ECDF;">
                                @else
                                    <div id="avatarPreviewDiv" class="w-20 h-20 rounded-full flex items-center justify-center font-bold text-2xl"
                                         style="background:#E2ECDF;color:#1D3520;">
                                        {{ mb_strtoupper(mb_substr(auth()->user()->first_name,0,1).mb_substr(auth()->user()->last_name,0,1)) }}
                                    </div>
                                @endif
                                <div class="absolute inset-0 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                                     style="background:rgba(0,0,0,.45);">
                                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <input id="avatar_input" name="avatar" type="file" accept="image/*" class="hidden"
                                       onchange="previewAvatar(this)">
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-ink">Profil rasmi</p>
                                <p class="text-xs mt-0.5" style="color:#5C6352;">JPG, PNG, GIF · Max 2MB</p>
                                <button type="button" onclick="document.getElementById('avatar_input').click()"
                                        class="mt-2 text-xs font-semibold" style="color:#1D3520;background:none;border:none;cursor:pointer;padding:0;">
                                    Rasmni o'zgartirish
                                </button>
                                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Name row --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label" for="first_name">Ism</label>
                                <input id="first_name" type="text" name="first_name" class="field-input"
                                       value="{{ old('first_name', auth()->user()->first_name) }}"
                                       required autofocus autocomplete="given-name" placeholder="Jasur">
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                            </div>
                            <div>
                                <label class="field-label" for="last_name">Familiya</label>
                                <input id="last_name" type="text" name="last_name" class="field-input"
                                       value="{{ old('last_name', auth()->user()->last_name) }}"
                                       required autocomplete="family-name" placeholder="Toshmatov">
                                <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Phone (readonly) --}}
                        <div>
                            <label class="field-label">Telefon raqam</label>
                            <div class="flex items-center gap-2">
                                <input type="text" class="field-input" value="{{ auth()->user()->phone }}" readonly
                                       style="background:#F8FCF7;color:#5C6352;cursor:not-allowed;flex:1;">
                                <a href="#phone-section"
                                   style="white-space:nowrap;font-size:.8rem;font-weight:700;color:#1D3520;text-decoration:none;padding:12px 16px;border:1.5px solid #E2ECDF;border-radius:10px;background:white;"
                                   onmouseover="this.style.background='#EDF0E5'" onmouseout="this.style.background='white'">
                                    O'zgartirish
                                </a>
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit"
                                    style="background:#1D3520;color:white;padding:12px 28px;border-radius:10px;font-weight:700;font-size:.9rem;border:none;cursor:pointer;"
                                    onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                                Saqlash
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change phone card --}}
                <div class="section-card" id="phone-section">
                    <h2 class="font-bold text-base text-ink mb-1">Telefon raqamni o'zgartirish</h2>
                    <p class="text-sm mb-6" style="color:#5C6352;">Yangi raqam SMS orqali tasdiqlanadi</p>
                    @include('profile.partials.change-phone-form')
                </div>

                {{-- Change password card --}}
                <div class="section-card">
                    <h2 class="font-bold text-base text-ink mb-1">Parolni o'zgartirish</h2>
                    <p class="text-sm mb-6" style="color:#5C6352;">Xavfsizlik uchun kuchli parol ishlating</p>

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="field-label" for="current_password">Joriy parol</label>
                            <input id="current_password" type="password" name="current_password" class="field-input"
                                   autocomplete="current-password" placeholder="••••••••">
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label" for="new_password">Yangi parol</label>
                                <input id="new_password" type="password" name="password" class="field-input"
                                       autocomplete="new-password" placeholder="••••••••">
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                            </div>
                            <div>
                                <label class="field-label" for="new_password_confirmation">Tasdiqlang</label>
                                <input id="new_password_confirmation" type="password" name="password_confirmation" class="field-input"
                                       autocomplete="new-password" placeholder="••••••••">
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                            </div>
                        </div>
                        <div class="flex items-center gap-4 pt-1">
                            <button type="submit"
                                    style="background:#1D3520;color:white;padding:12px 28px;border-radius:10px;font-weight:700;font-size:.9rem;border:none;cursor:pointer;"
                                    onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                                Parolni yangilash
                            </button>
                            @if(session('status') === 'password-updated')
                                <span class="text-sm font-semibold" style="color:#3E683F;">✓ Parol yangilandi</span>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Delete account card --}}
                <div class="section-card" style="border-color:#F5E3DB;">
                    <h2 class="font-bold text-base mb-1" style="color:#A34F30;">Akkauntni o'chirish</h2>
                    <p class="text-sm mb-5" style="color:#5C6352;">Bu amal qaytarib bo'lmaydi. Barcha ma'lumotlar o'chirib yuboriladi.</p>

                    <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                            style="padding:11px 24px;border:1.5px solid #A34F30;border-radius:10px;font-weight:700;font-size:.875rem;color:#A34F30;background:white;cursor:pointer;"
                            onmouseover="this.style.background='#F5E3DB'" onmouseout="this.style.background='white'">
                        Akkauntni o'chirish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0" style="background:rgba(0,0,0,.5);" onclick="document.getElementById('deleteModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="font-serif text-xl font-bold text-ink mb-2">Akkauntni o'chirish</h3>
        <p class="text-sm mb-5" style="color:#5C6352;">Tasdiqlash uchun parolingizni kiriting. Bu amal qaytarib bo'lmaydi.</p>
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label class="field-label">Parol</label>
                <input type="password" name="password" class="field-input" required placeholder="••••••••">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        style="flex:1;padding:12px;border:1.5px solid #E2ECDF;border-radius:10px;font-weight:600;background:white;cursor:pointer;">
                    Bekor qilish
                </button>
                <button type="submit"
                        style="flex:1;padding:12px;background:#A34F30;color:white;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                    O'chirish
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const container = input.closest('.group');
        let img = container.querySelector('img');
        const div = container.querySelector('div[id="avatarPreviewDiv"]');
        if (img) {
            img.src = e.target.result;
        } else if (div) {
            const newImg = document.createElement('img');
            newImg.id = 'avatarPreviewImg';
            newImg.className = 'w-20 h-20 rounded-full object-cover';
            newImg.style.border = '3px solid #E2ECDF';
            newImg.src = e.target.result;
            div.replaceWith(newImg);
        }
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
</x-app-layout>
