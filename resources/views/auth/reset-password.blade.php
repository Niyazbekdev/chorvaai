<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">🔑</div>
        <h2 class="text-xl font-bold text-gray-800">Yangi parol o'rnatish</h2>
        <p class="text-sm text-gray-500 mt-1">Yangi parolni ikki marta kiriting</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mt-2">
            Parolni saqlash
        </x-primary-button>
    </form>
</x-guest-layout>
