<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title    ', 'BrightLing')</title>
    <!-- Подключаем ассеты через Vite -->
    @vite('resources/js/app.js')
    <!-- Дополнительные стили для конкретной страницы -->
    @yield('styles')
    <style>
        @font-face {
            font-family: 'Montserrat Medium';
            src: url('{{ asset('fonts/montserrat/Montserrat-Medium.ttf') }}') format('truetype');

            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Montserrat Bold';
            src: url('{{ asset('fonts/montserrat/Montserrat-Bold.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Montserrat SemiBold';
            src: url('{{ asset('fonts/montserrat/Montserrat-SemiBold.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    </style>
</head>
<body>
@include('partials.header')

<main class="container py-4">
    @yield('content')
</main>

@include('partials.footer')
</body>
</html>
