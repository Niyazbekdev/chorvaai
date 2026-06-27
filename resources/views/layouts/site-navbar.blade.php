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
    background: #16a34a; color: white; text-decoration: none; border: 0;
    padding: 10px 20px; border-radius: 999px; font-weight: 700; cursor: pointer;
}
.profile-menu {
    display: none; position: absolute; right: 0; top: 54px; width: 230px;
    background: white; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.2);
    overflow: hidden;
}
.profile-wrap:hover .profile-menu { display: block; }
.profile-menu-header {
    display: block; width: 100%; padding: 14px 16px;
    color: #111827; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
    font-size: .9rem;
}
.profile-menu a, .profile-menu button {
    display: block; width: 100%; padding: 11px 16px; text-align: left;
    color: #111827; text-decoration: none; background: white; border: 0;
    font-size: .9rem; cursor: pointer;
}
.profile-menu a:hover, .profile-menu button:hover { background: #f3f4f6; }
.profile-menu .logout-btn { color: #dc2626; }
.profile-menu .logout-btn:hover { background: #fee2e2; }
</style>

<header class="site-navbar">
    <div class="site-navbar-inner">
        <a href="{{ url('/') }}" class="site-logo">Chorva<span>AI</span></a>

        <nav class="site-links">
            <a href="{{ url('/marketplace') }}">Marketplace</a>
            <a href="{{ route('products.create') }}" class="site-announce-btn">+ E'lon berish</a>
            <a href="{{ url('/about') }}">About</a>
            <a href="{{ url('/contact') }}">Contact</a>
        </nav>

        <div class="site-auth">
            @guest
                <a href="{{ route('login') }}" class="site-login">Kirish</a>
                <a href="{{ route('register') }}" class="site-register">Ro'yxatdan o'tish</a>
            @endguest

            @auth
                <div class="profile-wrap">
                    <button class="profile-btn">
                        {{ Auth::user()->first_name }} ▾
                    </button>
                    <div class="profile-menu">
                        <span class="profile-menu-header">
                            <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong><br>
                            <small style="color:#6b7280">{{ Auth::user()->phone }}</small>
                        </span>
                        <a href="{{ route('profile.edit') }}">Profil sozlamalari</a>
                        <a href="{{ route('profile.edit') }}#my-products">Mening e'lonlarim</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-btn">Chiqish</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
