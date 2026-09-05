<style>
/* ── Navbar ── */
.site-navbar {
    position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;
    background: #ffffff;
    border-bottom: 1px solid #EDF0E5;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.site-navbar-inner {
    height: 64px; display: flex; align-items: center; justify-content: space-between;
    max-width: 1280px; margin: 0 auto; padding: 0 40px;
}
.site-logo {
    display: flex; align-items: center; gap: 8px;
    color: #191D14; text-decoration: none; font-family: 'Zilla Slab', serif;
    font-size: 20px; font-weight: 700; flex-shrink: 0;
}
.site-logo svg { flex-shrink: 0; }
.site-links { display: flex; gap: 4px; align-items: center; }
.site-links a {
    color: #191D14; text-decoration: none; font-weight: 600; font-size: .875rem;
    padding: 7px 14px; border-radius: 8px; transition: background .15s, color .15s;
}
.site-links a:hover { background: #EDF0E5; color: #1D3520; }

/* "E'lon berish" green button */
.site-announce-btn {
    background: #1D3520 !important; color: white !important;
    padding: 8px 18px; border-radius: 8px; font-weight: 700;
    font-size: .875rem; display: flex; align-items: center; gap: 6px;
    transition: background .2s;
}
.site-announce-btn:hover { background: #2C4E2E !important; }

/* Auth buttons */
.site-auth { display: flex; gap: 8px; align-items: center; }
.site-login-btn {
    color: #191D14; text-decoration: none; font-weight: 600;
    font-size: .875rem; padding: 7px 16px; border-radius: 8px;
    transition: background .15s;
}
.site-login-btn:hover { background: #EDF0E5; }
.site-register-btn {
    background: #1D3520; color: white; text-decoration: none;
    padding: 8px 18px; border-radius: 8px; font-weight: 700;
    font-size: .875rem; transition: background .2s;
    border: none; cursor: pointer;
}
.site-register-btn:hover { background: #2C4E2E; color: white; }

/* Language switcher */
.lang-switcher {
    display: flex; align-items: center;
    background: #EDF0E5; border-radius: 8px; padding: 3px;
    gap: 2px;
}
.lang-btn {
    display: flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 6px;
    font-size: .78rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase;
    color: #5C6352; text-decoration: none;
    transition: all .15s; white-space: nowrap;
}
.lang-btn:hover { color: #1D3520; }
.lang-btn.active {
    background: white; color: #1D3520;
    box-shadow: 0 1px 3px rgba(0,0,0,.12);
}

/* Profile dropdown */
.profile-wrap { position: relative; }
.profile-btn {
    background: #1D3520; color: white; border: 0;
    padding: 7px 14px; border-radius: 8px; font-weight: 700;
    font-size: .875rem; cursor: pointer; display: flex; align-items: center; gap: 6px;
    transition: background .2s;
}
.profile-btn:hover { background: #2C4E2E; }
.profile-btn .arrow { display: inline-block; transition: transform .25s ease; font-size: .7rem; }
.profile-btn.open .arrow { transform: rotate(180deg); }
.profile-menu {
    display: none; position: absolute; right: 0; top: 50px; width: 230px;
    background: white; border-radius: 14px;
    border: 1px solid #EDF0E5;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    overflow: hidden; z-index: 100;
    animation: dropIn .18s ease;
}
.profile-menu.open { display: block; }
@keyframes dropIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.profile-menu-header {
    display: block; width: 100%; padding: 14px 16px;
    color: #191D14; background: #F8FCF7; border-bottom: 1px solid #EDF0E5;
    font-size: .88rem; line-height: 1.4;
}
.profile-menu a, .profile-menu button {
    display: block; width: 100%; padding: 10px 16px; text-align: left;
    color: #191D14; text-decoration: none; background: white; border: 0;
    font-size: .875rem; cursor: pointer; transition: background .12s;
}
.profile-menu a:hover, .profile-menu button:hover { background: #F8FCF7; }
.profile-menu .logout-btn { color: #A34F30; }
.profile-menu .logout-btn:hover { background: #F5E3DB; }

/* Icon buttons (bell, chat) */
.nav-icon-btn {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #5C6352; background: transparent; border: none; cursor: pointer;
    transition: background .15s, color .15s;
    text-decoration: none;
}
.nav-icon-btn:hover { background: #EDF0E5; color: #1D3520; }

/* ── Hamburger (mobile only) ── */
.mobile-menu-btn {
    display: none; background: none; border: none; cursor: pointer;
    color: #191D14; padding: 6px; border-radius: 8px;
    align-items: center; justify-content: center;
    transition: background .15s; flex-shrink: 0;
}
.mobile-menu-btn:hover { background: #EDF0E5; }

/* ── Mobile nav panel ── */
.mobile-nav {
    background: #ffffff;
    border-top: 1px solid #EDF0E5;
    padding: 8px 20px 24px;
    overflow-y: auto;
    max-height: calc(100dvh - 64px);
    box-shadow: 0 8px 20px rgba(0,0,0,.08);
}
.mobile-nav-link {
    display: flex; align-items: center; gap: 10px;
    color: #191D14; text-decoration: none; font-weight: 600;
    font-size: .95rem; padding: 14px 4px;
    border-bottom: 1px solid #EDF0E5;
    transition: color .15s;
}
.mobile-nav-link:hover { color: #1D3520; }
.mobile-nav-link:last-child { border-bottom: none; }
.mobile-nav-post {
    display: block; text-align: center;
    background: #1D3520; color: white !important;
    padding: 13px; border-radius: 10px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; margin-top: 8px;
}
.mobile-nav-post:hover { background: #2C4E2E; }
.mobile-user-header {
    padding: 16px 4px 12px;
    border-bottom: 1px solid #EDF0E5;
    margin-bottom: 4px;
}
.mobile-nav-secondary {
    display: flex; align-items: center; gap: 10px;
    color: #5C6352; text-decoration: none;
    font-size: .9rem; font-weight: 500;
    padding: 12px 4px;
    border-bottom: 1px solid #EDF0E5;
    transition: color .15s;
}
.mobile-nav-secondary:hover { color: #191D14; }
.mobile-nav-logout {
    display: block; width: 100%; text-align: center;
    background: #F5E3DB; color: #A34F30;
    border: none;
    padding: 13px; border-radius: 10px;
    font-weight: 700; font-size: .9rem; cursor: pointer;
    margin-top: 12px; transition: background .15s;
}
.mobile-nav-logout:hover { background: #f0d0c0; }
.mobile-nav-login {
    display: block; text-align: center;
    background: #1D3520; color: white;
    padding: 13px; border-radius: 10px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; margin-top: 12px;
}
.mobile-nav-login:hover { background: #2C4E2E; }

/* ── Main content offset below fixed navbar ── */
.site-main-offset { padding-top: 64px; }
.site-hero-fullscreen { margin-top: -64px; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .site-navbar-inner { padding: 0 20px; }
    .site-links { gap: 2px; }
    .site-links a { font-size: .82rem; padding: 6px 10px; }
}
@media (max-width: 768px) {
    .site-navbar-inner { padding: 0 16px; }
    .site-links { display: none; }
    .site-auth .site-register-btn,
    .site-auth .profile-wrap { display: none; }
    .site-auth .nav-icon-btn { display: none; }
    .mobile-menu-btn { display: flex; }
}
@media (min-width: 769px) {
    .mobile-nav { display: none !important; }
    .mobile-menu-btn { display: none !important; }
}
</style>

<header class="site-navbar" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <div class="site-navbar-inner">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="site-logo">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <circle cx="16" cy="16" r="16" fill="#E2ECDF"/>
                <path d="M9 22c0-3.9 3.1-7 7-7s7 3.1 7 7" stroke="#1D3520" stroke-width="2" stroke-linecap="round"/>
                <circle cx="16" cy="12" r="3.5" fill="#1D3520"/>
                <path d="M13 10.5c-.5-1-1.5-1.5-2.5-1" stroke="#1D3520" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M19 10.5c.5-1 1.5-1.5 2.5-1" stroke="#1D3520" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            ChorvaAI
        </a>

        {{-- Nav links --}}
        <nav class="site-links">
            <a href="{{ url('/marketplace') }}">{{ __('nav.marketplace') }}</a>
            <a href="{{ route('ai.index') }}">{{ __('nav.ai_assistant') }}</a>
            <a href="{{ url('/') }}#why">{{ __('nav.about') }}</a>
            <a href="{{ url('/') }}#contact">{{ __('nav.contact') }}</a>
        </nav>

        {{-- Right auth area --}}
        <div class="site-auth">
            @php $currentLocale = app()->getLocale(); @endphp

            {{-- Language switcher --}}
            <div class="lang-switcher">
                <a href="{{ route('lang.switch', 'uz') }}"
                   class="lang-btn {{ $currentLocale === 'uz' ? 'active' : '' }}">UZ</a>
                <a href="{{ route('lang.switch', 'ru') }}"
                   class="lang-btn {{ $currentLocale === 'ru' ? 'active' : '' }}">RU</a>
            </div>

            @guest
                <a href="{{ route('login') }}" class="site-login-btn">{{ __('nav.login') }}</a>
                <a href="{{ url('/register') }}" class="site-register-btn">{{ __('nav.register') }}</a>
            @endguest

            @auth
                {{-- Bell icon --}}
                <a href="{{ route('profile.my-products') }}" class="nav-icon-btn" title="{{ __('nav.my_ads_icon') }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 17H20L18.595 15.595A1 1 0 0118 14.828V11A6.002 6.002 0 0012 6a6 6 0 00-6 5v3.828a1 1 0 01-.293.707L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>

                {{-- Chat icon --}}
                <a href="{{ url('/marketplace') }}" class="nav-icon-btn" title="{{ __('nav.marketplace_title') }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </a>

                {{-- Profile dropdown --}}
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" onclick="toggleProfileMenu()">
                        <span style="width:22px;height:22px;border-radius:50%;background:#E2ECDF;color:#1D3520;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 2)) }}
                        </span>
                        {{ Auth::user()->first_name }}
                        <span class="arrow">▼</span>
                    </button>
                    <div class="profile-menu" id="profileMenu">
                        <span class="profile-menu-header">
                            <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong><br>
                            <span style="color:#5C6352;font-size:.8rem">{{ Auth::user()->phone }}</span>
                        </span>
                        <a href="{{ route('profile.edit') }}">{{ __('nav.profile_settings') }}</a>
                        <a href="{{ route('profile.my-products') }}">{{ __('nav.my_ads') }}</a>
                        <a href="{{ route('profile.favorites') }}" style="display:flex;align-items:center;justify-content:space-between">
                            {{ __('nav.favorites') }}
                            @php $__favCount = auth()->user()?->favorites()->count() ?? 0; @endphp
                            @if($__favCount > 0)
                                <span style="background:#3E683F;color:white;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:999px">{{ $__favCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('ai.index') }}" style="color:#3E683F;font-weight:600">{{ __('nav.market_analysis') }}</a>
                        <a href="{{ route('banners.my') }}">{{ __('nav.my_banners') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-btn">{{ __('nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            @endauth

            {{-- Hamburger (mobile) --}}
            <button class="mobile-menu-btn" @click="mobileOpen = !mobileOpen">
                <svg x-show="!mobileOpen" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="mobile-nav"
         x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1">

        @auth
            <div class="mobile-user-header">
                <p style="color:#191D14;font-weight:700;font-size:1rem">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p style="color:#5C6352;font-size:.82rem;margin-top:2px">{{ Auth::user()->phone }}</p>
            </div>
        @endauth

        <a href="{{ url('/marketplace') }}" class="mobile-nav-link" @click="mobileOpen = false">{{ __('nav.marketplace') }}</a>
        <a href="{{ route('ai.index') }}" class="mobile-nav-link" @click="mobileOpen = false">{{ __('nav.ai_assistant') }}</a>
        <a href="{{ url('/') }}#why" class="mobile-nav-link" @click="mobileOpen = false">{{ __('nav.about') }}</a>
        <a href="{{ url('/') }}#contact" class="mobile-nav-link" @click="mobileOpen = false">{{ __('nav.contact') }}</a>

        @auth
            <a href="{{ route('profile.edit') }}" class="mobile-nav-secondary" @click="mobileOpen = false">{{ __('nav.profile_settings') }}</a>
            <a href="{{ route('profile.my-products') }}" class="mobile-nav-secondary" @click="mobileOpen = false">{{ __('nav.my_ads') }}</a>
            <a href="{{ route('profile.favorites') }}" class="mobile-nav-secondary" @click="mobileOpen = false">{{ __('nav.favorites') }}</a>
            <a href="{{ route('ai.index') }}" class="mobile-nav-secondary" style="color:#3E683F;font-weight:600" @click="mobileOpen = false">{{ __('nav.market_analysis') }}</a>
            <a href="{{ route('banners.my') }}" class="mobile-nav-secondary" @click="mobileOpen = false">{{ __('nav.my_banners') }}</a>
        @endauth

        <a href="{{ route('products.create') }}" class="mobile-nav-post" @click="mobileOpen = false">{{ __('nav.post_ad') }}</a>

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-nav-logout">{{ __('nav.logout') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-login" @click="mobileOpen = false">{{ __('nav.login') }}</a>
        @endauth
    </div>
</header>

<script>
function toggleProfileMenu() {
    document.getElementById('profileBtn')?.classList.toggle('open');
    document.getElementById('profileMenu')?.classList.toggle('open');
}
document.addEventListener('click', function (e) {
    const wrap = document.getElementById('profileWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('profileBtn')?.classList.remove('open');
        document.getElementById('profileMenu')?.classList.remove('open');
    }
});
</script>
