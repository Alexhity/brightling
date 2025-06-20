    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Подключаем ассеты через Vite -->
        @vite('resources/js/app.js')
        <!-- Дополнительные стили для конкретной страницы -->
        @yield('styles')
        <style>
            /*Навигация слева*/
            .sidebar-navigation {
                position: fixed;
                left: 0;
                top: 0;
                width: 230px;
                height: 100vh;
                background-color: white;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                padding: 20px ;
                /*overflow-y: auto;*/
                padding-top: 107px;
                overflow-x: visible;
                z-index: 10;
            }
            .sidebar-navigation ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .sidebar-navigation ul li {
                position: relative;
            }
            .sidebar-navigation ul li a {
                text-decoration: none;
                color: #2B2D42;
                font-size: 18px;
                font-family: 'Montserrat Medium', sans-serif;
                transition: all 0.2s ease-in-out;
                padding: 10px;
                display: block;
                border-radius: 7px;
                margin-bottom: 8px;
            }

            .sidebar-navigation ul li a:hover {
                background-color: #8986FF;
                color: white;
                transform: scale(1.04);

            }

            .sidebar-navigation ul li a.active {
                background-color: #8986FF; /* Фиолетовый фон */
                color: white;
                transform: scale(1.04);
            }

            .content-wrapper {
                flex:1;
                margin-left:230px;
                padding:20px;
            }

            /* кнопка‑гамбургер */
            .sidebar-toggle {
                display:none;
                position:fixed;
                top:15px;
                left:15px;
                width:36px;
                height:36px;
                background:#8986FF;
                border:none;
                border-radius:4px;
                color:#fff;
                font-size:24px;
                align-items:center;
                justify-content:center;
                z-index:20;
                cursor:pointer;
            }


        </style>


        </style>
    </head>
    <body>
    <div class="dashboard-wrapper">
        <div class="sidebar-navigation">
            <ul>
                <li>
                    <a href="{{ route('admin.statistics') }}"
                       class="{{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                        Статистика
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.requests') }}"
                       class="{{ request()->routeIs('admin.requests') ? 'active' : '' }}">
                        Заявки
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        Пользователи
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.prices.index') }}"
                       class="{{ request()->routeIs('admin.prices.index') ? 'active' : '' }}">
                        Тарифы
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.courses.index') }}"
                       class="{{ request()->routeIs('admin.courses.index') ? 'active' : '' }}">
                        Курсы
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.timetables.index') }}"
                       class="{{ request()->routeIs('admin.timetables.index') ? 'active' : '' }}">
                        Расписание
                    </a>
                </li>


                <li>
                    <a href="{{ route('admin.languages.index') }}"
                       class="{{ request()->routeIs('admin.languages.index') ? 'active' : '' }}">
                        Языки
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.reviews.index') }}"
                       class="{{ request()->routeIs('admin.reviews.index') ? 'active' : '' }}">
                        Отзывы
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.messages.index') }}"
                       class="{{ request()->routeIs('admin.messages.index') ? 'active' : '' }}">
                        Сообщения
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.certificates.create') }}"
                       class="{{ request()->routeIs('admin.certificates.create') ? 'active' : '' }}">
                        Сертификаты
                    </a>
                </li>

            </ul>
        </div>
    </div>
    @yield('content')

    </body>
    </html>
