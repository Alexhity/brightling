{{-- resources/views/auth/teacher/lessons/index.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #333333;
            font-size: 32px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .header-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .header-row .range-info {
            font-size:14px;
            color:#666;
        }
        table.lessons {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .lessons th,
        .lessons td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .lessons th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .lessons td {
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
            font-size: 14px;
        }
        .table-action-btn {
            display: inline-block;
            padding: 6px 12px;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            min-width: 100px;
        }
        .table-action-edit {
            background: #f0f0f0;
            color: black;
        }
        .table-action-edit:hover {
            background: #d9d9d9;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Мои уроки</h2>
            <div class="range-info">
                Показаны уроки с
                <strong>{{ $windowStart->format('d.m.Y') }}</strong>
                по
                <strong>{{ $windowEnd->format('d.m.Y') }}</strong>
            </div>
        </div>

        <table class="lessons">
            <thead>
            <tr style="background:#e3effc;">
                <th>Курс</th>
                <th>Дата и время</th>
                <th>Ссылка</th>
                <th>Материал</th>
                <th>Тип</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($lessons as $lesson)
                @php
                    // корректно собираем дату и время из отдельных полей
                    $dateString = $lesson->date instanceof \Carbon\Carbon
                        ? $lesson->date->toDateString()
                        : $lesson->date;
                    $timeString = is_object($lesson->time)
                        ? $lesson->time->format('H:i:s')
                        : $lesson->time;
                    $dateTime = \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString . ' ' . $timeString
                    );
                    $isPast = $dateTime->lt(now());
                    // перевод типов на русский
                    $types = [
                        'group'      => 'Групповой',
                        'individual' => 'Индивидуальный',
                        'test'       => 'Тестовый',
                    ];
                @endphp
                <tr>
                    <td>{{ optional($lesson->course)->title ?? '—' }}</td>
                                    <td>{{ $dateTime->format('d.m.Y') }} в {{ $dateTime->format('H:i') }}</td>
                                    <td>
                                        @if($lesson->zoom_link)
                                            <a href="{{ $lesson->zoom_link }}" target="_blank">Открыть</a>
                                        @else
                                            —
                                        @endif

                                    </td>
                                    <td>
                                        @if($lesson->material_path)
                                            <a href="{{ asset($lesson->material_path) }}" target="_blank">
                                            {{ \Illuminate\Support\Str::afterLast($lesson->material_path, '/') }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $types[$lesson->type] ?? ucfirst($lesson->type) }}</td>
                    <td>{{ $isPast ? 'Завершён' : 'Предстоящий' }}</td>
                    <td>
                        <a href="{{ route('teacher.lessons.edit', $lesson) }}"
                           class="table-action-btn table-action-edit">
                            Редактировать
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px 0; text-align: center; color: #666;">
                        Уроков в этом периоде нет
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const errors = @json($errors->all());

            errors.forEach(msg => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: msg,
                    showConfirmButton: false,
                    timer: 10000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-toast'
                    }
                });
            });
        });
    </script>
@endsection
