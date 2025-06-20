<header class="site-header">
    <div class="header-container">
        <!-- Логотип -->
        <a href="{{ route('main') }}" class="logo-link">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Логотип BrightLing"
                class="logo-image">
        </a>

        <!-- Навигационное меню -->
        <nav class="nav-menu">
            <ul class="nav-list">
                <a href="{{ route('courses') }}"
                   class="{{ request()->routeIs('courses') ? 'active' : '' }}">
                    Курсы
                </a>
                <a href="{{ route('teachers') }}"
                   class="{{ request()->routeIs('teachers') ? 'active' : '' }}">
                    Преподаватели
                </a>
                <a href="{{ route('prices') }}"
                   class="{{ request()->routeIs('prices') ? 'active' : '' }}">
                    Стоимость
                </a>
                <li class="nav-item nav-item--dropdown">
                    <a href="{{ route('about.school', ['#faq']) }}" class="nav-link dropdown-toggle">О школе</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('about.school', ['#faq']) }}" class="dropdown-link">Часто задаваемые вопросы</a></li>
                        <li><a href="{{ route('about.school', ['#reviews']) }}" class="dropdown-link">Отзывы</a></li>
                        <li><a href="{{ route('about.school', ['#history']) }}" class="dropdown-link">Контакты</a></li>
                    </ul>
                </li>

            </ul>
        </nav>

        <!-- Кнопки авторизации и регистрации -->
        <div class="header-actions">
            @guest
                <!-- Для гостей показываем ссылки "Войти" и "Записаться на урок" -->
                <a href="{{ route('login') }}" class="btn-login">Войти</a>
{{--                <a href="#signup" class="btn-signup">Записаться на урок</a>--}}
            @else
                <!-- Для авторизованных пользователей показываем название пользователя с выпадающим меню -->
                <div class="user-dropdown">
                    <span class="user-name dropdown-toggle" >{{ Auth::user()->first_name }}</span>
                    <div class="dropdown-content">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.statistics') }}">Личный кабинет</a>
                        @elseif(Auth::user()->role === 'teacher')
                            <a href="{{ route('teacher.statistics') }}">Личный кабинет</a>
                        @elseif(Auth::user()->role === 'student')
                            <a href="{{ route('student.statistics') }}">Личный кабинет</a>
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

