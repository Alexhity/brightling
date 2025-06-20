@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 0 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin: 30px 0;
        }
        .lessons-table {
            width: 100%;
            border-collapse: collapse;
        }
        .lessons-table th,
        .lessons-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .lessons-table th {
            background: #f7f7f7;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .btn-edit {
            padding: 6px 12px;
            background: #e6e2f8;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .status-present {
            background: #d4edda;
            color: #155724;
        }
        .status-absent {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    <div class="admin-content-wrapper">
        <h2>Мои уроки</h2>

        <table class="lessons-table">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Время</th>
                <th>Курс</th>
                <th>Тема</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lessons as $lesson)
                <tr>
                    <td>{{ $lesson->date->format('d.m.Y') }}</td>
                    <td>{{ $lesson->start_time->format('H:i') }}</td>
                    <td>{{ $lesson->course->title }}</td>
                    <td>{{ $lesson->topic ?? '—' }}</td>
                    <td>
                        @if($lesson->attendance)
                            <span class="status-badge status-present">Посещ.</span>
                        @else
                            <span class="status-badge status-absent">Не запол.</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('teacher.lessons.edit', $lesson) }}" class="btn-edit">
                            Редактировать
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $lessons->links() }}
        </div>
    </div>
@endsection
