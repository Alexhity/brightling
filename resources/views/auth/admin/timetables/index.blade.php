{{-- resources/views/auth/admin/timetables/index.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left:200px;
            width:calc(100% - 200px);
            font-family:'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family:'Montserrat Bold', sans-serif;
            font-size:32px;
            margin:30px 0;
        }
        .week-nav {
            display:flex;
            align-items:center;
            gap:1rem;
            margin-bottom:20px;
        }
        .week-nav a {
            text-decoration:none;
            font-size:18px;
            color: #333;
        }
        .slots-table {
            width:100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .slots-table th,
        .slots-table td {
            vertical-align: top;
            padding:10px;
            border:1px solid #e0e0e0;
            width:14.285%;
        }
        .slots-table th {
            background:#f7f7f7;
            font-family:'Montserrat SemiBold', sans-serif;
            font-size:16px;
            text-align:center;
        }
        .slots-table td {
            background:#ffffff;
            height:180px;
            overflow-y:auto;
        }
        .slot {
            background:#e6e2f8;
            border-radius:8px;
            padding:8px;
            margin-bottom:8px;
            font-size:14px;
            line-height:1.3;
            width:100%;
            box-sizing:border-box;
        }
        .btn-create {
            display:inline-block;
            background:#e6e2f8;
            padding:8px 12px;
            border-radius:7px;
            text-decoration:none;
            color:black;
            font-family:'Montserrat Medium', sans-serif;
        }
        .slot--inactive {
            background: #f0f0f0 !important;
            color: #888;
            position: relative;
        }
        .slot--inactive::after {
            content: 'Не активен';
            position: absolute;
            top: 4px;
            right: 8px;
            font-size: 12px;
            color: #b00;
        }
        .btn-edit {
            display: inline-block;
            font-size: 13px;
            color: #2B2D42;
            text-decoration: underline;
            cursor: pointer;
        }

    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Расписание</h2>
            <a href="{{ route('admin.timetables.create') }}" class="btn-create">+ Добавить слот</a>
        </div>

        <div class="week-nav">
            <a href="{{ route('admin.timetables.index') }}">Текущая неделя</a>
            <a href="{{ route('admin.timetables.index', ['week_start' => $startOfWeek->copy()->subWeek()->toDateString()]) }}">← Пред. неделя</a>
            <span>{{ $startOfWeek->format('d.m.Y') }} — {{ $endOfWeek->format('d.m.Y') }}</span>
            <a href="{{ route('admin.timetables.index', ['week_start' => $startOfWeek->copy()->addWeek()->toDateString()]) }}">След. неделя →</a>
        </div>

        @php
            // Чтобы Blade «увидел» переменные и не ругался на daysUntil, сделаем простой массив из 7 дней
            $days = [];
            for ($i = 0; $i < 7; $i++) {
                $days[] = $startOfWeek->copy()->addDays($i);
            }
            $abbr = [
                'понедельник'=>'пн','вторник'=>'вт','среда'=>'ср',
                'четверг'=>'чт','пятница'=>'пт','суббота'=>'сб','воскресенье'=>'вс'
            ];
            $slots = $slots ?? collect();

        @endphp

        <table class="slots-table">
            <thead>
            <tr>
                @foreach ($days as $day)
                    @php $wd = mb_strtolower($day->translatedFormat('l')); @endphp
                    <th>
                        <div>{{ $abbr[$wd] }}</div>
                        <div>{{ $day->format('d.m') }}</div>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach ($days as $day)
                    @php
                        $dateKey = $day->format('Y-m-d');
                        $wd      = mb_strtolower($day->translatedFormat('l'));

                        // Собираем все слоты этого дня (разовые + шаблоны)
                        $raw = collect($slots->get($dateKey, collect()))
                               ->merge($slots->get($wd, collect()));

                        // Фильтруем по границам курса для конкретного дня
                        $daySlots = $raw->filter(function($slot) use ($day) {
                            // начало и конец курса
                            $start = $slot->course->created_at->startOfDay();
                            $end   = $slot->course->duration
                                ? \Carbon\Carbon::parse($slot->course->duration)->endOfDay()
                                : null;

                            // 1) Если слот с конкретной датой — убедимся, что он именно в этот день
                            if ($slot->date) {
                                return $day->toDateString() === $slot->date;
                            }

                            // 2) Повторяющийся слот: показываем, если
                            //    — день недели совпадает,
                            //    — и этот день лежит в период курса
                            return $day->gte($start)
                                && (!$end || $day->lte($end))
                                && mb_strtolower($slot->weekday) === mb_strtolower($day->translatedFormat('l'));
                        });
                    @endphp

                    <td>
                        @foreach ($daySlots as $slot)
                            <div class="slot @unless($slot->active) slot--inactive @endunless">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                    ({{ $slot->duration }} мин)<br>
                                    <strong>{{ $slot->course->title }}</strong>
                                    {{ $slot->teacher->first_name }} {{ $slot->teacher->last_name }}
                                {{-- Ссылка на редактирование этого слота именно в день $currDate --}}
                                <a href="{{ route('admin.timetables.editSlot', [$slot->id, $day->toDateString()]) }}"
                                   class="btn-edit">
                                    Редактировать
                                </a>
                        @endforeach
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
