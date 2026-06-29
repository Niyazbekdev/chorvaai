<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ChorvaAI') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.site-navbar')
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <main>{{ $slot }}</main>
        </div>
        {{-- ===== FOOTER ===== --}}
        <footer style="background:#011f13; border-top:1px solid rgba(255,255,255,0.06);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

                    {{-- Brand --}}
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="font-serif text-2xl font-bold text-white">Chorva<span class="text-emerald-400">AI</span></span>
                        </div>
                        <p class="text-white/50 text-sm leading-relaxed max-w-xs">
                            O'zbekistondagi chorva mollarini sotib olish va sotish uchun yagona raqamli platform. AI yordamida tez, xavfsiz va shaffof.
                        </p>
                        <p class="text-white/30 text-xs mt-6">info@chorvaai.uz</p>
                        <p class="text-white/30 text-xs">+998 90 000 00 00</p>
                    </div>

                    {{-- Quick links --}}
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Sahifalar</h4>
                        <ul class="space-y-2.5">
                            <li><a href="{{ url('/marketplace') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Bozor</a></li>
                            <li><a href="{{ route('ai-assistant.index') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">AI Yordamchi</a></li>
                            <li><a href="{{ url('/#why') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Biz haqimizda</a></li>
                            <li><a href="{{ url('/#contact') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Aloqa</a></li>
                        </ul>
                    </div>

                    {{-- Account links --}}
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Hisob</h4>
                        <ul class="space-y-2.5">
                            @auth
                                <li><a href="{{ route('products.create') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">E'lon berish</a></li>
                                <li><a href="{{ url('/dashboard') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Kabinet</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Kirish</a></li>
                                <li><a href="{{ url('/register') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">Ro'yxatdan o'tish</a></li>
                            @endauth
                        </ul>
                    </div>

                </div>

                <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <p class="text-white/30 text-xs">© {{ date('Y') }} ChorvaAI. Barcha huquqlar himoyalangan.</p>
                    <p class="text-white/20 text-xs">O'zbekiston, Qoraqalpog'iston</p>
                </div>
            </div>
        </footer>

        <x-ai-chat-widget />
        @stack('scripts')
    </body>
</html>
