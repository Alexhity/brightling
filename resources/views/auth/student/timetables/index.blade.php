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
        .time-period-header {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 4px;
            border-top: 1px solid #eee;
            padding-top: 4px;
            color: #555;
        }
        .slot {
            background: #e6e2f8;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            line-height: 1.3;
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
            display: flex;
            justify-content: space-between;
        }
        .slot-time {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .slot-type {
            font-style: italic;
            color: #6c757d;
        }
        .slot-teacher {
            margin-bottom: 4px;
            color: #555;
        }
        .slot-empty {
            text-align: center;
            color: #95a5a6;
            padding: 15px 0;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    @php
        use Carbon\Carbon;
        // Если контроллер по какой-то причине не передал — чтобы не было ошибки:
        $lessonsByDate = $lessonsByDate ?? collect();
    @endphp

    <div class="admin-content-wrapper">
        <h2>Моё расписание</h2>

        <div class="week-nav">
            <a href="{{ route('student.timetable', ['week_start' => $startOfWeek->copy()->subWeek()->toDateString()]) }}">← Пред. неделя</a>
            <span>{{ $startOfWeek->format('d.m.Y') }} — {{ $endOfWeek->format('d.m.Y') }}</span>
            <a href="{{ route('student.timetable', ['week_start' => $startOfWeek->copy()->addWeek()->toDateString()]) }}">След. неделя →</a>
            <a href="{{ route('student.timetable') }}" class="btn-current-week">Текущая неделя</a>
        </div>

        <table class="slots-table">
            <thead>
            <tr>
                @php
                    $abbr = [
                        'понедельник'=>'пн','вторник'=>'вт','среда'=>'ср',
                        'четверг'=>'чт','пятница'=>'пт','суббота'=>'сб','воскресенье'=>'вс',
                    ];
                @endphp
                @foreach($days as $date)
                    @php
                        $wd = mb_strtolower(Carbon::parse($date)->translatedFormat('l'));
                    @endphp
                    <th>
                        <div>{{ $abbr[$wd] ?? $wd }}</div>
                        <div>{{ Carbon::parse($date)->format('d.m') }}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach($days as $date)
                    @php
                        // получаем коллекцию уроков на эту дату (или пустую)
                        $lessons = $lessonsByDate->get($date, collect());

                        // разобьём по периодам
                        $morning = []; $afternoon = []; $evening = [];
                        foreach($lessons as $lesson){
                            $h = Carbon::parse($lesson->time)->hour;
                            if ($h < 12) {
                                $morning[] = $lesson;
                            } elseif ($h < 17) {
                                $afternoon[] = $lesson;
                            } else {
                                $evening[] = $lesson;
                            }
                        }
                    @endphp

                    <td>
                        {{-- УТРО --}}
                        @if(count($morning))
                            <div class="time-period-header">Утро</div>
                            @foreach($morning as $l)
                                <div class="slot @if($l->status==='cancelled') slot--cancelled @endif">
                                    <div class="slot-header">
                                        <span>{{ $l->course->title }}</span>
                                    </div>
                                    <div class="slot-time">{{ Carbon::parse($l->time)->format('H:i') }}</div>
                                    <div class="slot-type">
                                        @if($l->type==='group') Групповое
                                        @elseif($l->type==='individual') Индивидуальное
                                        @else Тестовый
                                        @endif
                                    </div>
                                    <div class="slot-teacher">
                                        {{ $l->teacher->first_name }} {{ $l->teacher->last_name }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- ДЕНЬ --}}
                        @if(count($afternoon))
                            <div class="time-period-header">День</div>
                            @foreach($afternoon as $l)
                                <div class="slot @if($l->status==='cancelled') slot--cancelled @endif">
                                    <div class="slot-header">
                                        <span>{{ $l->course->title }}</span>
                                    </div>
                                    <div class="slot-time">{{ Carbon::parse($l->time)->format('H:i') }}</div>
                                    <div class="slot-type">
                                        @if($l->type==='group') Групповое
                                        @elseif($l->type==='individual') Индивидуальное
                                        @else Тестовый
                                        @endif
                                    </div>
                                    <div class="slot-teacher">
                                        {{ $l->teacher->first_name }} {{ $l->teacher->last_name }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- ВЕЧЕР --}}
                        @if(count($evening))
                            <div class="time-period-header">Вечер</div>
                            @foreach($evening as $l)
                                <div class="slot @if($l->status==='cancelled') slot--cancelled @endif">
                                    <div class="slot-header">
                                        <span>{{ $l->course->title }}</span>
                                    </div>
                                    <div class="slot-time">{{ Carbon::parse($l->time)->format('H:i') }}</div>
                                    <div class="slot-type">
                                        @if($l->type==='group') Групповое
                                        @elseif($l->type==='individual') Индивидуальное
                                        @else Тестовый
                                        @endif
                                    </div>
                                    <div class="slot-teacher">
                                        {{ $l->teacher->first_name }} {{ $l->teacher->last_name }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- НЕТ УРОКОВ --}}
                        @if(!count($morning) && !count($afternoon) && !count($evening))
                            <div class="slot-empty">Нет занятий</div>
                        @endif
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
