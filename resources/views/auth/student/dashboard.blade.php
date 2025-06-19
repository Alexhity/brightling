@extends('layouts.app')

@section('title', 'Студенческая панель - Мои курсы')

@section('styles')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @include('layouts.left_sidebar_student')
    <div class="courses-container">
        <div class="student-header">
            <h1>Панель ученика</h1>
        </div>
        <h2>Мои курсы</h2>
        @if($courses->isEmpty())
            <p style="text-align: center;">Вы не записаны ни на один курс.</p>
        @else
            @foreach($courses as $course)
                <div class="course-card">
                    <div class="course-header">
                        <h2>{{ $course->title }}</h2>
                        <!-- Характеристики курса в виде кнопок -->
                        <div class="characteristic">
                            <button class="characteristic-btn">Формат: {{ $course->format }}</button>
                            <button class="characteristic-btn">Возраст: {{ $course->age_group ?? 'Не указана' }}</button>
                            <button class="characteristic-btn">Уроков: {{ $course->lesson_count ? $course->lesson_count : '0' }}</button>
                            <button class="characteristic-btn">Длительность: {{ $course->duration }}</button>
                            <button class="characteristic-btn">Язык: {{ $course->language ? $course->language->name : 'Не указан' }}</button>
                        </div>
                    </div>

                    <div class="course-description">
                        <p>{{ $course->description }}</p>
                    </div>

                    <div class="course-teacher">
                        Преподаватель: {{ $course->teacher ? $course->teacher->first_name . ' ' . $course->teacher->last_name : 'Не указан' }}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
