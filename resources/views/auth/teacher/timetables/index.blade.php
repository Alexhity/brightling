@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 180px);
            padding: 0 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin: 30px 0;
        }
        .week-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .week-nav a {
            text-decoration: none;
            font-size: 18px;
            color: #333;
        }
        .slots-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .slots-table th,
        .slots-table td {
            vertical-align: top;
            padding: 10px;
            border: 1px solid #e0e0e0;
            width: 14.285%;
        }
        .slots-table th {
            background: #f7f7f7;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            text-align: center;
        }
        .slots-table td {
            background: #ffffff;
            height: 180px;
            overflow-y: auto;
        }
        .slot {
            background: #e6e2f8;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            font-size: 14px;
            line-height: 1.4;
            width: 100%;
            box-sizing: border-box;
            position: relative;
        }
        .slot--inactive {
            background: #f0f0f0 !important;
            color: #888;
        }
        .slot--inactive::after {
            content: 'Отменено';
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 12px;
            font-weight: bold;
            color: #b00;
            background: rgba(255,255,255,0.7);
            padding: 2px 6px;
            border-radius: 3px;
        }
        .slot-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .slot-title {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 15px;
            color: #2c3e50;
        }
        .slot-time {
            margin-bottom: 6px;
            color: #2B2D42;
        }
        .slot-course {
            margin-bottom: 6px;
            color: #555;
        }
        .slot-type {
            font-style: italic;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .slot-status {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        .slot-empty {
            text-align: center;
            color: #95a5a6;
            padding: 15px 0;
            font-size: 14px;
        }

        /* Новые стили для группировки по времени */
        .time-period-header {
            font-weight: bold;
            padding: 8px 0 4px;
            margin-top: 10px;
            border-top: 1px solid #eee;
            color: #555;
        }
        .slot-morning { background-color: #fff9db; border-left: 3px solid #ffd43b; }
        .slot-afternoon { background-color: #d3f9d8; border-left: 3px solid #40c057; }
        .slot-evening { background-color: #e7f5ff; border-left: 3px solid #4dabf7; }

        /* Стиль для кнопки "Текущая неделя" */
        .btn-current-week {
            display: inline-block;
            background: #e6e2f8;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    @php
        // В начале секции content
        $groupedSlots = $groupedSlots ?? collect();
    @endphp
    <div class="admin-content-wrapper">
        <h2>Мое расписание</h2>

        <div class="role-info">
            Преподаватель: {{ $teacher->first_name }} {{ $teacher->last_name }}
        </div>

        <div class="week-nav">
            <a href="{{ route('teacher.timetable', ['week_start' => $startOfWeek->copy()->subWeek()->toDateString()]) }}">
                ← Предыдущая неделя
            </a>
            <span>
                {{ $startOfWeek->format('d.m.Y') }} — {{ $endOfWeek->format('d.m.Y') }}
            </span>
            <a href="{{ route('teacher.timetable', ['week_start' => $startOfWeek->copy()->addWeek()->toDateString()]) }}">
                Следующая неделя →
            </a>
            <!-- Добавляем кнопку "Текущая неделя" -->
            <a href="{{ route('teacher.timetable') }}" class="btn-current-week">
                Текущая неделя
            </a>
        </div>

        <table class="slots-table">
            <thead>
            <tr>
                @php
                    $abbr = [
                        'понедельник' => 'пн',
                        'вторник' => 'вт',
                        'среда' => 'ср',
                        'четверг' => 'чт',
                        'пятница' => 'пт',
                        'суббота' => 'сб',
                        'воскресенье' => 'вс'
                    ];
                @endphp

                @foreach ($days as $day)
                    @php
                        $weekday = mb_strtolower($day->translatedFormat('l'));
                    @endphp
                    <th>
                        <div>{{ $abbr[$weekday] ?? $weekday }}</div>
                        <div>{{ $day->format('d.m') }}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach ($days as $day)
                    @php
                        $dateKey = $day->toDateString();
                        $daySlots = $groupedSlots->get($dateKey, []);

                        // Группируем слоты по времени суток
                        $morningSlots = [];
                        $afternoonSlots = [];
                        $eveningSlots = [];

                        foreach ($daySlots as $slot) {
                            $hour = Carbon::parse($slot->start_time)->hour;

                            if ($hour < 12) {
                                $morningSlots[] = $slot;
                            } elseif ($hour < 17) {
                                $afternoonSlots[] = $slot;
                            } else {
                                $eveningSlots[] = $slot;
                            }
                        }
                    @endphp

                    <td>
                        @if(count($morningSlots) || count($afternoonSlots) || count($eveningSlots))
                            @if(count($morningSlots))
                                <div class="time-period-header">Утро</div>
                                @foreach($morningSlots as $s)
                                    @include('auth.teacher.timetables.slot', [
                                        'slot' => $s,
                                        'teacher' => $teacher
                                    ])
                                @endforeach
                            @endif

                            @if(count($afternoonSlots))
                                <div class="time-period-header">День</div>
                                @foreach($afternoonSlots as $s)
                                    @include('auth.teacher.timetables.slot', [
                                        'slot' => $s,
                                        'teacher' => $teacher
                                    ])
                                @endforeach
                            @endif

                            @if(count($eveningSlots))
                                <div class="time-period-header">Вечер</div>
                                @foreach($eveningSlots as $s)
                                    @include('auth.teacher.timetables.slot', [
                                        'slot' => $s,
                                        'teacher' => $teacher
                                    ])
                                @endforeach
                            @endif
                        @else
                            <div class="slot-empty">
                                Нет занятий
                            </div>
                        @endif
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
