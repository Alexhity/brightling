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
        .debug-info {
            background: #fff3cd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        .role-info {
            margin-bottom: 15px;
            font-size: 16px;
            color: #555;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
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
                    @endphp

                    <td>
                        @forelse($daySlots as $slot)
                            @php
                                $isOverride = $slot->override_user_id == $teacher->id;
                                $isCancelled = $slot->cancelled;
                            @endphp

                            <div class="slot @if($isCancelled) slot--inactive @endif">
                                <div class="slot-header">
                                        <span class="slot-title">
                                            {{ $slot->course->title ?? $slot->title }}
                                        </span>
                                    @if($isCancelled)
                                        <span class="slot-status status-cancelled">Отменено</span>
                                    @endif
                                </div>

                                <div class="slot-time">
                                    <strong>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</strong>
                                    ({{ $slot->duration }} мин)
                                </div>

                                <div class="slot-course">
                                    @if($slot->course)
                                        Курс: {{ $slot->course->title }}
                                    @else
                                        {{ $slot->title }}
                                    @endif
                                </div>

                                <div class="slot-type">
                                    @if($isOverride)
                                        <i>(замена)</i>
                                    @endif
                                    @if($slot->type === 'group')
                                        Групповое занятие
                                    @elseif($slot->type === 'individual')
                                        Индивидуальное занятие
                                    @else
                                        Тестовый урок
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="slot-empty">
                                Нет занятий
                            </div>
                        @endforelse
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
