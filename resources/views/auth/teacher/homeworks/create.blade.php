@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 20px;
            font-family:'Montserrat Medium',sans-serif;
        }
        h2 { font-family:'Montserrat Bold',sans-serif; font-size:32px; margin-bottom:20px; }
        .header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .btn-back { padding:8px 14px; background:#e6e2f8; border-radius:7px; text-decoration:none; color:#000; }
        .btn-back:hover { background:#c4b6f3; }
        .homework-form { background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; margin-bottom:6px; font-family:'Montserrat SemiBold',sans-serif; }
        .form-group select,
        .form-group textarea,
        .form-group input {
            width:100%; padding:8px 12px; font-size:15px; border:1px solid #ccc; border-radius:4px;
            font-family:'Montserrat Medium',sans-serif;
        }
        .form-group textarea { min-height:100px; }
        .btn-submit { padding:10px 18px; background:#8986FF; color:#fff; border:none; border-radius:7px; cursor:pointer; }
        .btn-submit:hover { background:#6f6cff; }
        .error { color:#c00; font-size:14px; margin-top:4px; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Создать домашнее задание</h2>
            <a href="{{ route('teacher.homeworks.index') }}" class="btn-back">← Назад</a>
        </div>

        <form action="{{ route('teacher.homeworks.store') }}"
              method="POST"
              class="homework-form">
            @csrf

            <div class="form-group">
                <label for="lesson_id">Урок</label>
                <select name="lesson_id" id="lesson_id">
                    <option value="">— выберите урок —</option>
                    @foreach($courses as $course)
                        <optgroup label="{{ $course->title }}">
                            @foreach($course->lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson_id')==$lesson->id?'selected':'' }}>
                                    {{ $lesson->date->format('d.m.Y') }} {{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('lesson_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea name="description" id="description">{{ old('description') }}</textarea>
                @error('description')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="deadline">Дедлайн</label>
                <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}">
                @error('deadline')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="link">Ссылка (если есть)</label>
                <input
                    type="text"
                    name="link"
                    id="link"
                    value="{{ old('link') }}"
                    placeholder="https://example.com/your-homework"
                >
                @error('link')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Создать</button>
        </form>
    </div>
@endsection
