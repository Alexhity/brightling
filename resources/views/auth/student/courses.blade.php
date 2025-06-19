{{-- resources/views/auth/student/courses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Мои курсы')

@section('styles')
    <style>
        .student-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #2B2D42;
            margin-bottom: 20px;
        }
        table.courses {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .courses th,
        .courses td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
        }
        .courses th {
            background-color: #fef6e0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
            text-align: center;
        }
        .courses td {
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
        }

        .courses tr:hover {
            background-color: #f9f9f9;
        }
        .btn-expand {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #2B2D42;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        <h2>Мои курсы</h2>
        <table class="courses">
            <thead>
            <tr>
                <th></th>
                <th>Название</th>
                <th>Формат</th>
                <th>Уровень</th>
                <th>Язык</th>
                <th>Преподаватель</th>
            </tr>
            </thead>
            <tbody>
            @foreach($courses as $course)
                <tr>
                    {{-- Плюсик для деталей --}}
                    <td>
                        <button class="btn-expand"
                                style="font-size: 18px"
                                data-title="{{ $course->title }}"
                                data-description="{{ $course->description }}"
                                data-format="{{ ucfirst($course->format) }}"
                                data-level="{{ strtoupper($course->level) }}"
                                data-language="{{ $course->language->name }}"
                                data-teacher="{{ $course->teachers->map(fn($t)=> $t->first_name.' '.$t->last_name)->implode(', ') }}"
                                data-schedule='@json($course->schedule_lines)'>
                            +
                        </button>
                    </td>
                    <td>{{ $course->title }}</td>
                    <td>{{ ucfirst($course->format) }}</td>
                    <td>{{ strtoupper($course->level) }}</td>
                    <td>{{ $course->language->name }}</td>
                    <td>{{ $course->teachers->map(fn($t)=> $t->first_name.' '.$t->last_name)->implode(', ') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-expand').forEach(btn => {
                btn.addEventListener('click', () => {
                    // парсим расписание
                    let sched;
                    try {
                        sched = JSON.parse(btn.dataset.schedule);
                        if (!Array.isArray(sched)) sched = [];
                    } catch {
                        sched = [];
                    }
                    const scheduleHtml = sched.length
                        ? sched.map(l => `<p>${l}</p>`).join('')
                        : '<p>—</p>';

                    Swal.fire({
                        title: btn.dataset.title,
                        html: `
                    <p><strong>Описание:</strong><br>${btn.dataset.description}</p>
                    <hr>
                    <p><strong>Формат:</strong> ${btn.dataset.format}</p>
                    <p><strong>Уровень:</strong> ${btn.dataset.level}</p>
                    <p><strong>Язык:</strong> ${btn.dataset.language}</p>
                    <hr>
                    <p><strong>Преподаватель:</strong> ${btn.dataset.teacher || '—'}</p>
                    <hr>
                    <p><strong>Расписание:</strong></p>
                    ${scheduleHtml}
                `,
                        width: 600,
                        confirmButtonText: 'Закрыть'
                    });
                });
            });
        });
    </script>
@endsection




