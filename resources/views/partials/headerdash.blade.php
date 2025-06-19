<header class="site-header">
    <div class="header-container">
        <!-- Логотип -->
        <a href="{{ route('main') }}" class="logo-link">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Логотип BrightLing"
                class="logo-image"
            >
        </a>
        <div class="nav-placeholder"></div>
        <!-- Кнопки авторизации и регистрации -->
        <div class="header-actions">
            @guest

            @else
                <!-- Для авторизованных пользователей показываем название пользователя с выпадающим меню -->
                <div class="user-dropdown">
                    <span class="user-name dropdown-toggle">{{ Auth::user()->first_name }}</span>
                    <div class="dropdown-content">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('main') }}">Главная страница</a>
                        @elseif(Auth::user()->role === 'teacher')
                            <a href="{{ route('main') }}">Главная страница</a>
                        @elseif(Auth::user()->role === 'student')
                            <a href="{{ route('main') }}">Главная страница</a>
                        @endif
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Выйти
                        </a>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endguest
        </div>
    </div>
</header>

