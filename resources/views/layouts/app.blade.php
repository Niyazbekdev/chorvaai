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
            <main class="site-main-offset">{{ $slot }}</main>
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
                            {{ __('nav.footer_desc') }}
                        </p>
                        <p class="text-white/30 text-xs mt-6">info@chorvaai.uz</p>
                        <p class="text-white/30 text-xs">+998 90 000 00 00</p>
                    </div>

                    {{-- Quick links --}}
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ __('nav.footer_pages') }}</h4>
                        <ul class="space-y-2.5">
                            <li><a href="{{ url('/marketplace') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.marketplace') }}</a></li>
                            <li><a href="{{ route('ai-assistant.index') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.ai_assistant') }}</a></li>
                            <li><a href="{{ url('/#why') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.about') }}</a></li>
                            <li><a href="{{ url('/#contact') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.contact') }}</a></li>
                        </ul>
                    </div>

                    {{-- Account links --}}
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ __('nav.footer_account') }}</h4>
                        <ul class="space-y-2.5">
                            @auth
                                <li><a href="{{ route('products.create') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.footer_post_ad') }}</a></li>
                                <li><a href="{{ url('/dashboard') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.dashboard') }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.login') }}</a></li>
                                <li><a href="{{ url('/register') }}" class="text-white/50 hover:text-emerald-400 text-sm transition-colors">{{ __('nav.register') }}</a></li>
                            @endauth
                        </ul>
                    </div>

                </div>

                <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <p class="text-white/30 text-xs">{{ __('nav.footer_copyright', ['year' => date('Y')]) }}</p>
                    <p class="text-white/20 text-xs">{{ __('nav.footer_location') }}</p>
                </div>
            </div>
        </footer>

        <x-ai-chat-widget />
        @stack('scripts')
    </body>
</html>
