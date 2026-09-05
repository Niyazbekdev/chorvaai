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
    </head>
    <body class="font-sans text-ink antialiased" style="background:#F8FCF7;">

        <div class="min-h-screen flex">

            {{-- ── Left panel (dark green) ── --}}
            <div class="hidden lg:flex flex-col justify-between w-[420px] flex-shrink-0 p-10 relative overflow-hidden"
                 style="background:#1D3520;">

                {{-- Decorative silhouette --}}
                <div class="absolute bottom-0 left-0 right-0 opacity-10 pointer-events-none">
                    <svg viewBox="0 0 400 320" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="200" cy="240" rx="160" ry="110"/>
                        <ellipse cx="200" cy="200" rx="130" ry="100"/>
                        <circle cx="130" cy="160" r="60"/>
                        <rect x="120" y="290" width="24" height="70" rx="8"/>
                        <rect x="160" y="300" width="24" height="60" rx="8"/>
                        <rect x="240" y="290" width="24" height="70" rx="8"/>
                        <rect x="280" y="300" width="24" height="60" rx="8"/>
                        <ellipse cx="130" cy="155" rx="40" ry="52"/>
                        <circle cx="112" cy="128" r="10"/>
                        <path d="M92 120 Q82 106 88 96" stroke="white" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <path d="M112 120 Q102 104 108 94" stroke="white" stroke-width="6" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2" style="text-decoration:none;">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <circle cx="16" cy="16" r="16" fill="#3E683F"/>
                        <path d="M9 22c0-3.9 3.1-7 7-7s7 3.1 7 7" stroke="#E2ECDF" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="16" cy="12" r="3.5" fill="#E2ECDF"/>
                    </svg>
                    <span class="font-serif text-xl font-bold text-white">ChorvaAI</span>
                </a>

                {{-- Tagline --}}
                <div class="relative z-10">
                    <h2 class="font-serif text-3xl font-bold text-white leading-tight mb-4">
                        {{ __('auth.guest_tagline') }}
                    </h2>
                    <p class="text-white/60 text-sm leading-relaxed">
                        {{ __('auth.guest_desc') }}
                    </p>

                    {{-- Stats --}}
                    <div class="mt-8 pt-6" style="border-top:1px solid rgba(255,255,255,.1);">
                        @php
                            $guestStats = [
                                'products' => \App\Models\Product::where('status_id', 1)->count(),
                                'users'    => \App\Models\User::count(),
                            ];
                        @endphp
                        <p class="text-white font-bold text-2xl font-serif">{{ number_format($guestStats['users']) }}</p>
                        <p class="text-white/50 text-xs mt-0.5">{{ __('auth.guest_farmers_joined') }}</p>
                    </div>
                </div>
            </div>

            {{-- ── Right panel (form area) ── --}}
            <div class="flex-1 flex flex-col">

                {{-- Top bar --}}
                <div class="flex items-center justify-between px-6 py-4">
                    {{-- Mobile logo --}}
                    <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-2" style="text-decoration:none;">
                        <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" fill="#E2ECDF"/>
                            <path d="M9 22c0-3.9 3.1-7 7-7s7 3.1 7 7" stroke="#1D3520" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="16" cy="12" r="3.5" fill="#1D3520"/>
                        </svg>
                        <span class="font-serif text-lg font-bold text-ink">ChorvaAI</span>
                    </a>
                    <div class="lg:hidden flex-1"></div>

                    {{-- Language switcher --}}
                    @php $locale = app()->getLocale(); @endphp
                    <div style="display:flex;align-items:center;background:#EDF0E5;border-radius:8px;padding:3px;gap:2px;">
                        <a href="{{ route('lang.switch', 'uz') }}"
                           style="padding:5px 12px;border-radius:6px;font-size:.78rem;font-weight:700;text-decoration:none;transition:all .15s;{{ $locale === 'uz' ? 'background:white;color:#1D3520;box-shadow:0 1px 3px rgba(0,0,0,.1);' : 'color:#5C6352;' }}">
                            🇺🇿 UZ
                        </a>
                        <a href="{{ route('lang.switch', 'ru') }}"
                           style="padding:5px 12px;border-radius:6px;font-size:.78rem;font-weight:700;text-decoration:none;transition:all .15s;{{ $locale === 'ru' ? 'background:white;color:#1D3520;box-shadow:0 1px 3px rgba(0,0,0,.1);' : 'color:#5C6352;' }}">
                            🇷🇺 RU
                        </a>
                    </div>
                </div>

                {{-- Form --}}
                <div class="flex-1 flex items-center justify-center px-6 py-8">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>
