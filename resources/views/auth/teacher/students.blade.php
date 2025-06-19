@extends('layouts.app')

@section('title', 'Мои студенты')

@section('styles')
    <style>
        /* Основной контейнер для страницы "Мои студенты" */
        .students-container {
            margin-left: 200px; /* учитываем левую панель */
            padding: 20px;
            width: calc(100% - 220px);
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        /* Заголовок страницы */
        .students-container h1 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #2B2D42;
            margin-bottom: 30px;
        }
        /* Заголовок каждого курса */
        .students-container h2 {
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        /* Общие стили для текста */
        .students-container p {
            font-family: 'Montserrat Medium', sans-serif;
            color: #444;
            margin: 15px 0;
        }
        /* Стили для таблиц */
        .students-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .students-container th,
        .students-container td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .students-container th {
            background-color: #eff7ff;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
        }
        .students-container tr:hover {
            background-color: #f9f9f9;
        }
        /* Отделитель между курсами */
        .students-container hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 40px 0;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="students-container">
        <h1>Ученики</h1>

        @foreach($courses as $course)
            <h2>Курс: {{ $course->title }}</h2>
            @if($course->students->isEmpty())
                <p>Нет записанных учеников.</p>
            @else
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($course->students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->email }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            <hr>
        @endforeach
    </div>
@endsection
