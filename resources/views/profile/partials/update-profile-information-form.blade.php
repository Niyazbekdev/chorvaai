<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('profile.info_title') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('profile.info_desc') }}</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- Avatar --}}
        <div class="flex items-center gap-5">
            <div class="relative group">
                @if($user->avatar)
                    <img src="{{ $user->avatarUrl() }}" alt="avatar"
                         class="w-20 h-20 rounded-full object-cover border-2 border-emerald-200 shadow">
                @else
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-400 to-emerald-600
                                flex items-center justify-center text-white font-bold text-3xl shadow">
                        {{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}
                    </div>
                @endif
                <label for="avatar"
                       class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center
                              opacity-0 group-hover:opacity-100 cursor-pointer transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </label>
                <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden"
                       onchange="previewAvatar(this)">
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">{{ __('profile.avatar_label') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('profile.avatar_hint') }}</p>
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>
        </div>

        {{-- First Name --}}
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" name="first_name" type="text"
                class="mt-1 block w-full"
                :value="old('first_name', $user->first_name)"
                required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        {{-- Last Name --}}
        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" name="last_name" type="text"
                class="mt-1 block w-full"
                :value="old('last_name', $user->last_name)"
                required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        {{-- Phone --}}
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <div class="flex mt-1">
                <span class="inline-flex items-center px-3 border border-r-0 border-gray-200 bg-gray-100 rounded-l-xl text-gray-500 text-sm font-medium">
                    +998
                </span>
                <x-text-input id="phone" name="phone" type="text"
                    class="block w-full rounded-l-none"
                    :value="old('phone', str_replace('+998', '', $user->phone))"
                    placeholder="901234567" maxlength="9"
                    inputmode="numeric" autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-1">
            <x-primary-button>{{ __('profile.save') }}</x-primary-button>

            @if(session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-emerald-600 font-medium">
                    ✓ {{ __('profile.saved') }}
                </p>
            @endif
        </div>
    </form>
</section>

<script>
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const container = input.closest('.group');
        let img = container.querySelector('img');
        if (!img) {
            const div = container.querySelector('div.w-20');
            img = document.createElement('img');
            img.className = 'w-20 h-20 rounded-full object-cover border-2 border-emerald-200 shadow';
            if (div) div.replaceWith(img);
            else container.prepend(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

// Phone prefix handler
document.getElementById('phone').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 9);
});
document.querySelector('form[action="{{ route("profile.update") }}"]')
    .addEventListener('submit', function () {
        const p = document.getElementById('phone');
        if (p.value && !p.value.startsWith('+998')) {
            p.value = '+998' + p.value;
        }
    });
</script>
