@extends('layouts.app')

@section('styles')
    <style>
        .lesson-form {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-family: 'Montserrat SemiBold';
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .student-list {
            margin-top: 30px;
        }
        .student-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .student-name {
            flex: 1;
            font-family: 'Montserrat Medium';
        }
        .attendance-select {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .btn-save {
            padding: 10px 20px;
            background: #4a86e8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="admin-content-wrapper">
        <h2>Редактировать урок</h2>
        <div class="lesson-form">
            <form method="POST" action="{{ route('teacher.lessons.update', $lesson) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Тема урока:</label>
                    <input type="text" name="topic" value="{{ $lesson->topic }}" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Zoom ссылка:</label>
                    <input type="url" name="zoom_link" value="{{ $lesson->zoom_link }}" class="form-input">
                </div>

                <div class="student-list">
                    <h3>Посещаемость студентов</h3>
                    @foreach($lesson->students as $student)
                        <div class="student-item">
                            <div class="student-name">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </div>
                            <select name="attendance[{{ $student->id }}]" class="attendance-select">
                                <option value="present" {{ $lesson->attendance[$student->id] == 'present' ? 'selected' : '' }}>
                                    Присутствовал
                                </option>
                                <option value="absent" {{ $lesson->attendance[$student->id] == 'absent' ? 'selected' : '' }}>
                                    Отсутствовал
                                </option>
                            </select>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn-save">Сохранить изменения</button>
            </form>
        </div>
    </div>
@endsection
