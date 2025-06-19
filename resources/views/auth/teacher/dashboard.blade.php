@extends('layouts.app')

@section('title', 'Преподавательская панель - Мои курсы')

@section('styles')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="courses-container">
        <div class="teacher-header">
            <h1>Панель преподавателя</h1>
        </div>
        @foreach($courses as $course)
        <h2>Мои курсы</h2>
        @if($courses->isEmpty())
            <p style="text-align: center;">У вас пока нет курсов.</p>
        @else

                <div class="course-card">
                    <div class="course-header">
                        <h2>{{ $course->title }}</h2>
                        <!-- Выводим характеристики курса в виде кнопок -->
                        <div class="characteristic">
                            <button class="characteristic-btn">Формат: {{ $course->format }}</button>
                            <button class="characteristic-btn">Возраст: {{ $course->age_group ?? 'Не указана' }}</button>
                            <button class="characteristic-btn">
                                Уроков: {{ $course->lesson_count ? $course->lesson_count : '0' }}
                            </button>
                            <button class="characteristic-btn">Длительность: {{ $course->duration }}</button>
{{--                            <button class="characteristic-btn">--}}
{{--                                Тариф:--}}
{{--                                @if($course->pricing)--}}
{{--                                    {{ $course->lesson_count * $course->pricing->amount }} {{ $course->pricing->currency }}--}}
{{--                                @else--}}
{{--                                    Не указан--}}
{{--                                @endif--}}
{{--                            </button>--}}
                            <button class="characteristic-btn">
                                Язык: {{ $course->language ? $course->language->name : 'Не указан' }}
                            </button>
                        </div>
                    </div>

                    <div class="course-description">
                        <p>{{ $course->description }}</p>

                    </div>

                    <div class="students-list">
                        <h3>Записанные ученики:</h3>
                        @if($course->students && $course->students->count() > 0)
                            @foreach($course->students as $student)
                                <div class="student-item">
                                    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})
                                </div>
                            @endforeach
                        @else
                            <p>Нет записанных учеников.</p>
                        @endif
                    </div>
                </div>

        @endif
    </div>
    @endforeach
@endsection
