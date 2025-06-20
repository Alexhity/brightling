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

        .dashboard-wrapper {
            display: flex;
        }


    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <div class="sidebar-navigation">
        <ul>
            <li><a href="{{ route('teacher.statistics') }}" class="{{ request()->routeIs('teacher.statistics') ? 'active' : '' }}">Статистика</a></li>
            <li><a href="{{ route('teacher.courses.index') }}" class="{{ request()->routeIs('teacher.courses.index') ? 'active' : '' }}">Курсы</a></li>
            <li><a href="{{ route('teacher.timetable') }}" class="{{ request()->routeIs('teacher.timetable') ? 'active' : '' }}">Расписание</a></li>
{{--            <li><a href="{{ route('teacher.lessons.index') }}" class="{{ request()->routeIs('teacher.lessons.index') ? 'active' : '' }}">Уроки</a></li>--}}
            <li><a href="{{ route('teacher.homeworks.index') }}" class="{{ request()->routeIs('teacher.homeworks.index') ? 'active' : '' }}">Домашние задания</a></li>
{{--            <li><a href="#" class="#">Посещаемость</a></li>--}}
            <li><a href="{{ route('teacher.messages.index') }}" class="{{ request()->routeIs('teacher.messages.index') ? 'active' : '' }}">Сообщения</a></li>
            <li><a href="{{ route('teacher.profile.edit') }}" class="{{ request()->routeIs('teacher.profile.edit') ? 'active' : '' }}">Профиль</a></li>

{{--            <li><a href="{{ route('teacher.students') }}" class="{{ request()->routeIs('teacher.students') ? 'active' : '' }}">Ученики</a></li>--}}
        </ul>
    </div>
</div>
@yield('content')
</body>
</html>
