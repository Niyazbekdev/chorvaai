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
.site-logo { color: white; text-decoration: none; font-size: 26px; font-weight: 700; }
.site-logo span { color: #10b981; }
.site-links { display: flex; gap: 24px; align-items: center; }
.site-links a { color: white; text-decoration: none; font-weight: 600; text-transform: uppercase; font-size: .85rem; letter-spacing: .04em; }
.site-links a:hover { color: #10b981; }
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
</style>

<header class="site-navbar">
    <div class="site-navbar-inner">
        <a href="{{ url('/') }}" class="site-logo">Chorva<span>AI</span></a>

        <nav class="site-links">
            <a href="{{ url('/marketplace') }}">{{ __('nav.marketplace') }}</a>
            <a href="{{ route('products.create') }}" class="site-announce-btn">{{ __('nav.post_ad') }}</a>
            <a href="{{ url('/about') }}">{{ __('nav.about') }}</a>
            <a href="{{ url('/contact') }}">{{ __('nav.contact') }}</a>
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
        </div>
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
