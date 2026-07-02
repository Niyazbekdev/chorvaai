<style>
.site-navbar {
    position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;
    background: rgba(1,31,19,.85); backdrop-filter: blur(8px);
    border-bottom: 1px solid rgba(255,255,255,.15);
}
.site-navbar-inner {
    height: 76px; display: flex; align-items: center; justify-content: space-between;
    max-width: 1280px; margin: 0 auto; padding: 0 40px;
}
.site-logo { color: white; text-decoration: none; font-size: 26px; font-weight: 700; flex-shrink: 0; }
.site-logo span { color: #10b981; }
.site-links { display: flex; gap: 24px; align-items: center; }
.site-links a { color: white; text-decoration: none; font-weight: 600; text-transform: uppercase; font-size: .85rem; letter-spacing: .04em; }
.site-links a:hover { color: #10b981; }
.site-nav-ai-btn {
    background: rgba(16,185,129,.18); color: #6ee7b7 !important;
    border: 1px solid rgba(16,185,129,.4); border-radius: 999px;
    padding: 6px 14px; font-size: .8rem !important;
    transition: background .2s, color .2s !important;
}
.site-nav-ai-btn:hover { background: rgba(16,185,129,.32) !important; color: white !important; }
.site-announce-btn {
    background: #10b981; color: white !important; text-decoration: none;
    padding: 9px 20px; border-radius: 999px; font-weight: 700;
    font-size: .85rem; letter-spacing: .04em; text-transform: uppercase;
    transition: background .2s;
}
.site-announce-btn:hover { background: #059669 !important; color: white !important; }
.site-auth { display: flex; gap: 12px; align-items: center; }
.site-login { color: white; text-decoration: none; font-weight: 700; }
.site-register {
    background: #16a34a; color: white; text-decoration: none; border: 0;
    padding: 10px 20px; border-radius: 999px; font-weight: 700;
}
.site-register:hover { background: #15803d; color: white; }
.profile-wrap { position: relative; }
.profile-btn {
    background: #16a34a; color: white; border: 0;
    padding: 10px 20px; border-radius: 999px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: 6px;
    transition: background .2s;
}
.profile-btn:hover { background: #15803d; }
.profile-btn .arrow {
    display: inline-block; transition: transform .25s ease; font-size: .75rem;
}
.profile-btn.open .arrow { transform: rotate(180deg); }
.profile-menu {
    display: none; position: absolute; right: 0; top: 54px; width: 230px;
    background: white; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.25);
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
    color: #111827; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
    font-size: .88rem; line-height: 1.4;
}
.profile-menu a, .profile-menu button {
    display: block; width: 100%; padding: 11px 16px; text-align: left;
    color: #111827; text-decoration: none; background: white; border: 0;
    font-size: .9rem; cursor: pointer; transition: background .15s;
}
.profile-menu a:hover, .profile-menu button:hover { background: #f3f4f6; }
.profile-menu .logout-btn { color: #dc2626; }
.profile-menu .logout-btn:hover { background: #fee2e2; }

/* ── Language switcher ── */
.lang-switcher {
    display: flex; align-items: center;
    background: rgba(255,255,255,.12);
    border-radius: 999px;
    padding: 3px;
    gap: 2px;
    border: 1px solid rgba(255,255,255,.2);
}
.lang-btn {
    display: flex; align-items: center; gap: 4px;
    padding: 5px 11px; border-radius: 999px;
    font-size: .78rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase;
    color: rgba(255,255,255,.7); text-decoration: none;
    transition: all .2s; white-space: nowrap;
}
.lang-btn:hover { color: white; background: rgba(255,255,255,.12); }
.lang-btn.active {
    background: white; color: #065f46;
    box-shadow: 0 1px 4px rgba(0,0,0,.18);
}
.lang-flag { font-size: 1rem; line-height: 1; }

/* ── Hamburger (mobile only) ── */
.mobile-menu-btn {
    display: none;
    background: none; border: none; cursor: pointer;
    color: white; padding: 6px; border-radius: 8px;
    align-items: center; justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
}
.mobile-menu-btn:hover { background: rgba(255,255,255,.1); }

/* ── Mobile nav panel ── */
.mobile-nav {
    background: rgba(1,31,19,.97); backdrop-filter: blur(12px);
    border-top: 1px solid rgba(255,255,255,.1);
    padding: 8px 20px 24px;
    overflow-y: auto;
    max-height: calc(100dvh - 64px);
}
.mobile-nav-link {
    display: flex; align-items: center; gap: 10px;
    color: white; text-decoration: none; font-weight: 600;
    font-size: .95rem; padding: 14px 4px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    text-transform: uppercase; letter-spacing: .04em;
    transition: color .15s;
}
.mobile-nav-link:hover { color: #10b981; }
.mobile-nav-link:last-child { border-bottom: none; }
.mobile-nav-ai { color: #6ee7b7 !important; }
.mobile-nav-post {
    display: block; text-align: center;
    background: #10b981; color: white !important;
    padding: 13px; border-radius: 12px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; margin-top: 4px;
    text-transform: uppercase; letter-spacing: .05em;
}
.mobile-nav-post:hover { background: #059669; }
.mobile-user-header {
    padding: 16px 4px 12px;
    border-bottom: 1px solid rgba(255,255,255,.1);
    margin-bottom: 4px;
}
.mobile-nav-secondary {
    display: flex; align-items: center; gap: 10px;
    color: rgba(255,255,255,.75); text-decoration: none;
    font-size: .9rem; font-weight: 500;
    padding: 12px 4px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    transition: color .15s;
}
.mobile-nav-secondary:hover { color: white; }
.mobile-nav-logout {
    display: block; width: 100%; text-align: center;
    background: rgba(220,38,38,.15); color: #f87171;
    border: 1px solid rgba(220,38,38,.3);
    padding: 13px; border-radius: 12px;
    font-weight: 700; font-size: .9rem; cursor: pointer;
    margin-top: 12px; transition: background .15s;
}
.mobile-nav-logout:hover { background: rgba(220,38,38,.25); }
.mobile-nav-login {
    display: block; text-align: center;
    background: #10b981; color: white;
    padding: 13px; border-radius: 12px;
    font-weight: 700; font-size: .9rem;
    text-decoration: none; margin-top: 12px;
}

/* ── Responsive breakpoints ── */
@media (max-width: 900px) {
    .site-navbar-inner { padding: 0 20px; }
    .site-links { gap: 14px; }
    .site-links a { font-size: .78rem; }
}

@media (max-width: 768px) {
    .site-navbar-inner { padding: 0 16px; height: 64px; }
    .site-links { display: none; }
    .site-auth .site-register,
    .site-auth .profile-wrap { display: none; }
    .mobile-menu-btn { display: flex; }
}

@media (min-width: 769px) {
    .mobile-nav { display: none !important; }
    .mobile-menu-btn { display: none !important; }
}
</style>

<header class="site-navbar" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <div class="site-navbar-inner">
        <a href="{{ url('/') }}" class="site-logo">Chorva<span>AI</span></a>

        <nav class="site-links">
            <a href="{{ url('/marketplace') }}">{{ __('nav.marketplace') }}</a>
            <a href="{{ route('ai-assistant.index') }}" class="site-nav-ai-btn">
                <svg style="width:15px;height:15px;display:inline;margin-right:5px;vertical-align:-2px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                {{ __('nav.ai_assistant') }}
            </a>
            <a href="{{ route('products.create') }}" class="site-announce-btn">{{ __('nav.post_ad') }}</a>
            <a href="{{ url('/') }}#why">{{ __('nav.about') }}</a>
            <a href="{{ url('/') }}#contact">{{ __('nav.contact') }}</a>
        </nav>

        <div class="site-auth">
            @php $currentLocale = app()->getLocale(); @endphp

            <div class="lang-switcher">
                <a href="{{ route('lang.switch', 'uz') }}"
                   class="lang-btn {{ $currentLocale === 'uz' ? 'active' : '' }}">
                    <span class="lang-flag">🇺🇿</span> UZ
                </a>
                <a href="{{ route('lang.switch', 'ru') }}"
                   class="lang-btn {{ $currentLocale === 'ru' ? 'active' : '' }}">
                    <span class="lang-flag">🇷🇺</span> RU
                </a>
            </div>

            @guest
                <a href="{{ route('login') }}" class="site-register">{{ __('nav.login') }}</a>
            @endguest

            @auth
                <div class="profile-wrap" id="profileWrap">
                    <button class="profile-btn" id="profileBtn" onclick="toggleProfileMenu()">
                        {{ Auth::user()->first_name }}
                        <span class="arrow">▼</span>
                    </button>
                    <div class="profile-menu" id="profileMenu">
                        <span class="profile-menu-header">
                            <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong><br>
                            <span style="color:#6b7280;font-size:.8rem">{{ Auth::user()->phone }}</span>
                        </span>
                        <a href="{{ route('profile.edit') }}">{{ __('nav.profile_settings') }}</a>
                        <a href="{{ route('profile.my-products') }}">{{ __('nav.my_ads') }}</a>
                        <a href="{{ route('profile.favorites') }}" style="display:flex;align-items:center;justify-content:space-between">
                            {{ __('nav.favorites') }}
                            @php
                                $__favCount = auth()->user()?->favorites()->count() ?? 0;
                            @endphp
                            @if($__favCount > 0)
                                <span style="background:#ef4444;color:white;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:999px">{{ $__favCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('conversations.index') }}" style="display:flex;align-items:center;justify-content:space-between">
                            {{ __('nav.messages') }}
                            @php
                                $__unread = \App\Models\Message::whereHas('conversation', fn($q) =>
                                    $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id())
                                )->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($__unread > 0)
                                <span style="background:#2563eb;color:white;font-size:.7rem;font-weight:700;
                                             padding:2px 7px;border-radius:999px">{{ $__unread }}</span>
                            @endif
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-btn">{{ __('nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            @endauth

            {{-- Hamburger button (mobile only) --}}
            <button class="mobile-menu-btn" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()">
                <svg x-show="!mobileOpen" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            {{-- User info --}}
            <div class="mobile-user-header">
                <p style="color:white;font-weight:700;font-size:1rem">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <p style="color:rgba(255,255,255,.5);font-size:.82rem;margin-top:2px">{{ Auth::user()->phone }}</p>
            </div>
        @endauth

        {{-- Nav links --}}
        <a href="{{ url('/marketplace') }}" class="mobile-nav-link" @click="mobileOpen = false">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __('nav.marketplace') }}
        </a>
        <a href="{{ route('ai-assistant.index') }}" class="mobile-nav-link mobile-nav-ai" @click="mobileOpen = false">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
            {{ __('nav.ai_assistant') }}
        </a>
        <a href="{{ url('/') }}#why" class="mobile-nav-link" @click="mobileOpen = false">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('nav.about') }}
        </a>
        <a href="{{ url('/') }}#contact" class="mobile-nav-link" @click="mobileOpen = false">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ __('nav.contact') }}
        </a>

        @auth
            {{-- Auth quick links --}}
            <a href="{{ route('profile.edit') }}" class="mobile-nav-secondary" @click="mobileOpen = false">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ __('nav.profile_settings') }}
            </a>
            <a href="{{ route('profile.my-products') }}" class="mobile-nav-secondary" @click="mobileOpen = false">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('nav.my_ads') }}
            </a>
            <a href="{{ route('profile.favorites') }}" class="mobile-nav-secondary" @click="mobileOpen = false">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                {{ __('nav.favorites') }}
                @php $__favCount = auth()->user()?->favorites()->count() ?? 0; @endphp
                @if($__favCount > 0)
                    <span style="background:#ef4444;color:white;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:auto">{{ $__favCount }}</span>
                @endif
            </a>
            <a href="{{ route('conversations.index') }}" class="mobile-nav-secondary" @click="mobileOpen = false">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('nav.messages') }}
                @php
                    $__unread = \App\Models\Message::whereHas('conversation', fn($q) =>
                        $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id())
                    )->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                @endphp
                @if($__unread > 0)
                    <span style="background:#2563eb;color:white;font-size:.7rem;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:auto">{{ $__unread }}</span>
                @endif
            </a>
        @endauth

        {{-- Primary CTA --}}
        <a href="{{ route('products.create') }}" class="mobile-nav-post" @click="mobileOpen = false">
            + {{ __('nav.post_ad') }}
        </a>

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
    const btn  = document.getElementById('profileBtn');
    const menu = document.getElementById('profileMenu');
    btn.classList.toggle('open');
    menu.classList.toggle('open');
}

document.addEventListener('click', function (e) {
    const wrap = document.getElementById('profileWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('profileBtn')?.classList.remove('open');
        document.getElementById('profileMenu')?.classList.remove('open');
    }
});
</script>
