<header class="header">
    <div class="header__logo">
        <a href="{{ auth()->check() && auth()->user()->is_admin ? '/admin/attendance/list' : '/' }}">
            <img src="{{ asset('img/logo.png') }}" class="img-full" alt="ロゴ">
        </a>
    </div>

    @auth
        @unless (request()->is('login', 'register', 'email/*', 'admin/login'))
            @include(auth()->user()->is_admin ? 'components.header-admin-nav' : 'components.header-user-nav')
        @endunless
    @endauth
</header>
