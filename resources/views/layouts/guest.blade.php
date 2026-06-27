<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            {{-- Language switcher --}}
            @php $locale = app()->getLocale(); @endphp
            <div class="absolute top-4 right-4 flex items-center bg-white rounded-full shadow px-1 py-1 gap-1">
                <a href="{{ route('lang.switch', 'uz') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition
                          {{ $locale === 'uz' ? 'bg-[#011f13] text-white' : 'text-gray-500 hover:text-gray-800' }}">
                    🇺🇿 UZ
                </a>
                <a href="{{ route('lang.switch', 'ru') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition
                          {{ $locale === 'ru' ? 'bg-[#011f13] text-white' : 'text-gray-500 hover:text-gray-800' }}">
                    🇷🇺 RU
                </a>
            </div>

            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
