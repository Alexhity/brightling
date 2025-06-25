{{-- resources/views/auth/teacher/timetables/index.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .teacher-content-wrapper {
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
        }
        .week-nav a, .week-nav span {
            font-size: 18px;
            color: #333;
            text-decoration: none;
        }
        .btn-current-week {
            background: #e6e2f8;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 10px;
            display: inline-block;
        }
        .slots-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .slots-table th, .slots-table td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            vertical-align: top;
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
            padding: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            line-height: 1.3;
            box-sizing: border-box;
            position: relative;
        }
        .slot--cancelled {
            background: #f0f0f0;
            color: #888;
        }
        .slot--cancelled::after {
            content: 'Отменено';
            position: absolute;
            top: 4px;
            right: 8px;
            font-size: 12px;
            color: #b00;
        }
        .slot-header {
            font-family: 'Montserrat SemiBold', sans-serif;
            margin-bottom: 4px;
        }
        .slot-time {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .slot-type {
            font-style: italic;
            color: #6c757d;
        }
        .time-period-header {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
            border-top: 1px solid #eee;
            padding-top: 4px;
            color: #555;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')
    @php
        $lessonsByDate = $lessonsByDate ?? collect();
    @endphp

    <div class="teacher-content-wrapper">
        <h2>Моё расписание</h2>
        <div class="week-nav">
            <a href="{{ route('teacher.timetable', ['week_start'=>$startOfWeek->copy()->subWeek()->toDateString()]) }}">← Пред. неделя</a>
            <span>{{ $startOfWeek->format('d.m.Y') }} — {{ $endOfWeek->format('d.m.Y') }}</span>
            <a href="{{ route('teacher.timetable', ['week_start'=>$startOfWeek->copy()->addWeek()->toDateString()]) }}">След. неделя →</a>
            <a href="{{ route('teacher.timetable') }}" class="btn-current-week">Текущая неделя</a>
        </div>

        @php
            // группируем уроки по дате и периоду суток
            $dates = collect(range(0,6))->map(fn($i)=>$startOfWeek->copy()->addDays($i)->toDateString());
        @endphp


        <table class="slots-table">
            <thead>
            <tr>
                @foreach($dates as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('d.m') }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach($dates as $date)
                    @php
                        $lessons = $lessonsByDate->get($date, collect());
                       $morning  = $lessons->filter(fn($l)=>\Carbon\Carbon::parse($l->time)->hour<12);
                       $afternoon= $lessons->filter(fn($l)=>\Carbon\Carbon::parse($l->time)->hour>=12 && \Carbon\Carbon::parse($l->time)->hour<17);
                       $evening  = $lessons->filter(fn($l)=>\Carbon\Carbon::parse($l->time)->hour>=17);
                    @endphp
                    <td>
                        @if($morning->count())<div class="time-period-header">Утро</div>@endif
                        @foreach($morning as $l)
                            <div class="slot @if($l->status=='cancelled') slot--cancelled @endif">
                                <div class="slot-header">{{ $l->course->title ?? 'Тестовый урок' }}</div>
                                <div class="slot-time">{{ \Carbon\Carbon::parse($l->time)->format('H:i') }}</div>
                                <div class="slot-type">
                                    @switch($l->type)
                                        @case('group') Групповое @break
                                        @case('individual') Индивидуальное @break
                                        @case('test') Тестовый @break
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                        @if($afternoon->count())<div class="time-period-header">День</div>@endif
                        @foreach($afternoon as $l)
                            <div class="slot @if($l->status=='cancelled') slot--cancelled @endif">
                                <div class="slot-header">{{ $l->course->title ?? 'Тестовый урок' }}</div>
                                <div class="slot-time">{{ \Carbon\Carbon::parse($l->time)->format('H:i') }}</div>
                                <div class="slot-type">
                                    @switch($l->type)
                                        @case('group') Групповое @break
                                        @case('individual') Индивидуальное @break
                                        @case('test') Тестовый @break
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                        @if($evening->count())<div class="time-period-header">Вечер</div>@endif
                        @foreach($evening as $l)
                            <div class="slot @if($l->status=='cancelled') slot--cancelled @endif">
                                <div class="slot-header">{{ $l->course->title ?? 'Тестовый урок' }}</div>
                                <div class="slot-time">{{ \Carbon\Carbon::parse($l->time)->format('H:i') }}</div>
                                <div class="slot-type">
                                    @switch($l->type)
                                        @case('group') Групповое @break
                                        @case('individual') Индивидуальное @break
                                        @case('test') Тестовый @break
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
