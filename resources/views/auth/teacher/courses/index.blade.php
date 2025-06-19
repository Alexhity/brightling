@extends('layouts.app')

@section('title', 'Мои курсы')

@section('styles')
    <style>
        .teacher-content-wrapper {
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
            text-align: left;
        }
        .courses th {
            background-color: #fef6e0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
            text-align: center;
        }
        .courses td {
            font-size: 14px;
            text-align: center;
        }
        .courses tr:hover {
            background-color: #f9f9f9;
        }
        .table-action-btn {
            display: inline-block;
            padding: 6px 12px;
            background: #e6f7ff;
            border-radius: 7px;
            text-decoration: none;
            font-family: 'Montserrat Medium', sans-serif;
            color: #2B2D42;
            transition: background .2s;
        }
        .table-action-btn:hover {
            background: #b3e5ff;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">
        @if(session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            </script>
        @endif

        <h2>Мои курсы</h2>
        <table class="courses">
            <thead>
            <tr>
                <th></th>
                <th>Название</th>
                <th>Формат</th>
                <th>Уровень</th>
                <th>Язык</th>
                <th>Студентов</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($courses as $course)
                <tr>
                    {{-- Плюсик для подробностей --}}
                    <td>
                        <button class="btn-expand"
                                style="font-size: 18px"
                                data-title="{{ $course->title }}"
                                data-description="{{ $course->description }}"
                                data-format="{{ ucfirst($course->format) }}"
                                data-level="{{ strtoupper($course->level) }}"
                                data-language="{{ $course->language->name }}"
                                data-teachers='@json($course->teachers->map(fn($t)=> $t->first_name.' '.$t->last_name))'
                                data-students='@json($course->students->map(fn($s)=> $s->first_name.' '.$s->last_name))'
                                data-schedule='@json($course->schedule_lines)'>
                            +
                        </button>
                    </td>

                    <td>{{ $course->title }}</td>
                    <td>{{ ucfirst($course->format) }}</td>
                    <td>{{ strtoupper($course->level) }}</td>
                    <td>{{ $course->language->name }}</td>
                    <td>{{ $course->students_count }}</td>

                    {{-- Редактировать уровень --}}
                    <td>
                        <a href="{{ route('teacher.courses.editLevel', $course) }}"
                           class="table-action-btn">
                            Редактировать уровень
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-expand').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Парсим преподавателей и студентов (всегда массивы)
                    const teachers = (() => {
                        try { return JSON.parse(btn.dataset.teachers) || []; }
                        catch { return []; }
                    })().join(', ') || '—';

                    const students = (() => {
                        try { return JSON.parse(btn.dataset.students) || []; }
                        catch { return []; }
                    })().join(', ') || '—';

                    // Парсим расписание, или ставим пустой массив
                    let scheduleArr;
                    try {
                        scheduleArr = JSON.parse(btn.dataset.schedule);
                        if (!Array.isArray(scheduleArr)) scheduleArr = [];
                    } catch {
                        scheduleArr = [];
                    }

                    const scheduleHtml = scheduleArr.length
                        ? scheduleArr.map(line => `<p>${line}</p>`).join('')
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
                    <p><strong>Преподаватели:</strong> ${teachers}</p>
                    <p><strong>Студенты:</strong> ${students}</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
@endsection

