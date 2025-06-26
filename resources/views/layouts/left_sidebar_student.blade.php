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
            <li><a href="{{ route('student.statistics') }}" class="{{ request()->routeIs('student.statistics') ? 'active' : '' }}">Статистика</a></li>
            <li><a href="{{ route('student.courses') }}" class="{{ request()->routeIs('student.courses') ? 'active' : '' }}">Мои курсы</a></li>
            <li><a href="{{ route('student.timetable') }}" class="{{ request()->routeIs('student.timetable') ? 'active' : '' }}">Расписание</a></li>
            <li><a href="{{ route('student.lessons.index') }}" class="{{ request()->routeIs('student.lessons.index') ? 'active' : '' }}">Мои уроки</a></li>
            <li><a href="{{ route('student.homeworks') }}" class="{{ request()->routeIs('student.homeworks') ? 'active' : '' }}">Домашние задания</a></li>
            <li><a href="{{ route('student.attendance') }}" class="{{ request()->routeIs('student.attendance') ? 'active' : '' }}">Посещаемость</a></li>
{{--            <li><a href="#" class="#">Домашние задания</a></li>--}}
{{--            <li><a href="#" class="#">Посещаемость</a></li>--}}
            <li><a href="{{ route('student.reviews.index') }}" class="{{ request()->routeIs('student.reviews.index') ? 'active' : '' }}">Оставить отзыв</a></li>
            <li><a href="{{ route('student.messages.index') }}" class="{{ request()->routeIs('student.messages.index') ? 'active' : '' }}">Сообщения</a></li>
            <li><a href="{{ route('student.profile.edit') }}" class="{{ request()->routeIs('student.profile.edit') ? 'active' : '' }}">Профиль</a></li>

        </ul>
    </div>
</div>
@yield('content')
</body>
</html>
