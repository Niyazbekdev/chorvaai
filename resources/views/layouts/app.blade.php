<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ChorvaAI') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Zilla+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-paper text-ink">
        <div class="min-h-screen">
            @include('layouts.site-navbar')
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <main class="site-main-offset">{{ $slot }}</main>
        </div>

        {{-- ===== FOOTER ===== --}}
        <footer style="background:#1D3520;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

                    {{-- Brand --}}
                    <div class="md:col-span-1">
                        <div class="flex items-center gap-2 mb-4">
                            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                                <circle cx="16" cy="16" r="16" fill="#3E683F"/>
                                <path d="M8 20c0-4.4 3.6-8 8-8s8 3.6 8 8" stroke="#E2ECDF" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="16" cy="12" r="3" fill="#E2ECDF"/>
                            </svg>
                            <span class="font-serif text-xl font-bold text-white">ChorvaAI</span>
                        </div>
                        <p class="text-white/50 text-sm leading-relaxed">
                            O'zbekistondagi chorva mollarini sotib olish va sotish uchun yagona raqamli platforma. AI yordamida tez, xavfsiz va shaffof.
                        </p>
                    </div>

                    {{-- Sahifalar --}}
                    <div>
                        <h4 class="text-white/40 font-semibold text-xs uppercase tracking-widest mb-5">Sahifalar</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ url('/marketplace') }}" class="text-white/70 hover:text-white text-sm transition-colors">Bozor</a></li>
                            <li><a href="{{ route('ai.index') }}" class="text-white/70 hover:text-white text-sm transition-colors">Ai Yordamchi</a></li>
                            <li><a href="{{ url('/#why') }}" class="text-white/70 hover:text-white text-sm transition-colors">Biz haqimizda</a></li>
                            <li><a href="{{ url('/#contact') }}" class="text-white/70 hover:text-white text-sm transition-colors">Aloqa</a></li>
                        </ul>
                    </div>

                    {{-- Hisob --}}
                    <div>
                        <h4 class="text-white/40 font-semibold text-xs uppercase tracking-widest mb-5">Hisob</h4>
                        <ul class="space-y-3">
                            @auth
                                <li><a href="{{ route('login') }}" class="text-white/70 hover:text-white text-sm transition-colors">Kirish</a></li>
                                <li><a href="{{ url('/register') }}" class="text-white/70 hover:text-white text-sm transition-colors">Ro'yxatdan o'tish</a></li>
                                <li><a href="{{ route('products.create') }}" class="text-white/70 hover:text-white text-sm transition-colors">E'lon berish</a></li>
                                <li><a href="{{ route('profile.my-products') }}" class="text-white/70 hover:text-white text-sm transition-colors">Mening e'lonlarim</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-white/70 hover:text-white text-sm transition-colors">Kirish</a></li>
                                <li><a href="{{ url('/register') }}" class="text-white/70 hover:text-white text-sm transition-colors">Ro'yxatdan o'tish</a></li>
                                <li><a href="{{ route('products.create') }}" class="text-white/70 hover:text-white text-sm transition-colors">E'lon berish</a></li>
                            @endauth
                        </ul>
                    </div>

                    {{-- Aloqa --}}
                    <div>
                        <h4 class="text-white/40 font-semibold text-xs uppercase tracking-widest mb-5">Aloqa</h4>
                        <ul class="space-y-3">
                            <li><span class="text-white/70 text-sm">+998 71 200 00 00</span></li>
                            <li><span class="text-white/70 text-sm">info@chorvaai.uz</span></li>
                            <li><span class="text-white/70 text-sm">Toshkent, O'zbekiston</span></li>
                        </ul>
                    </div>

                </div>

                <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <p class="text-white/30 text-xs">© {{ date('Y') }} ChorvaAI. Barcha huquqlar himoyalangan.</p>
                    <p class="text-white/20 text-xs">Maxfiylik siyosati · Foydalanish shartlari</p>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
