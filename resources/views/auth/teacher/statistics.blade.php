@extends('layouts.app')

@section('title', 'Учитель-панель - Статистика')

@section('styles')
    <style>
        /* Основной контейнер админ-панели */
        .teacher-container {
            max-width: 1250px;

            font-family: 'Montserrat Medium', sans-serif;
            font-size: 18px;

            margin-left: 200px;
            /*width: calc(100% - 270px);*/
            padding: 20px;
        }

        /* Статистика */
        .statistics {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }
        .statistic-card {
            flex: 1 1 calc(33.33% - 20px);
            background-color: #f0efff; /* сиреневые оттенки */
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: #2B2D42;
        }
        .statistic-card h5 {
            font-family: 'Montserrat Bold', sans-serif;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .statistic-card p {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 1.5em;
            margin: 0;
        }

    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="teacher-container">

        @if(isset($teacherCourses))
            <div class="statistics">
                <div class="statistic-card">
                    <h5>Мои курсы</h5>
                    <p>{{ $teacherCourses }}</p>
                </div>
                <div class="statistic-card" style="background-color: #eff7ff;">
                    <h5>Студенты</h5>
                    <p>{{ $totalStudents }}</p>
                </div>
                <div class="statistic-card" style="background-color: #fef6e0;">
                    <h5>Работаю</h5>
                    <p>{{ $workDuration }}</p>
                </div>
            </div>
        @endif

    </div>


@endsection
