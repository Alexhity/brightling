@extends('layouts.app')

@section('title', 'Студент-панель - Статистика')

@section('styles')
    <style>
        /* Основной контейнер админ-панели */
        .student-container {
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
            font-size: 1.5чem;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')
    <div class="student-container">

        @if(isset($activeCourses))
            <div class="statistics">
                <div class="statistic-card">
                    <h5>Активные курсы</h5>
                    <p>{{ $activeCourses }}</p>
                </div>
                <div class="statistic-card" style="background-color: #eff7ff;">
                    <h5>Пройденные уроки</h5>
                    <p>0</p>
                </div>
                <div class="statistic-card" style="background-color: #fef6e0;">
                    <h5>Учится уже</h5>
                    <p>{{ $studyDuration }}</p>
                </div>
            </div>
        @endif

    </div>


@endsection
