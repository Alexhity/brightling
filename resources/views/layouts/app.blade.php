<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    @vite(['resources/css/app.css', 'resources/css/components/header.css'])
    <!-- Scripts -->
    @vite(['resources/css/auth.css', 'resources/js/app.js'])

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
@include('partials.headerdash')

<main class="container py-4" style="margin-top: 65px;">
    @yield('content')
</main>
@stack('scripts')
{{--@include('partials.footerdash')--}}
</body>
</html>
