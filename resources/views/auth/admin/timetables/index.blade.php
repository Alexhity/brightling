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
            text-align: center;
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
            position: relative;
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
            margin-top: 5px;
        }
        .slot-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .slot-status {
            font-size: 12px;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .status-modified {
            background-color: #ffc107;
            color: #000;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }

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

        .slot {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .slot--inactive {
            opacity: 0.6;
            background-color: #f8f9fa;
        }

    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <div class="header-row">
            <div class="grid">
                <h2>Расписание</h2>
                <a href="{{ route('admin.timetables.index') }}" class="btn-create">Текущая неделя</a>
            </div>


        </div>

        <div class="week-nav">

            <a href="{{ route('admin.timetables.index', ['week_start' => $startOfWeek->copy()->subWeek()->toDateString()]) }}">← Пред. неделя</a>
            <span>{{ $startOfWeek->format('d.m.Y') }} — {{ $endOfWeek->format('d.m.Y') }}</span>
            <a href="{{ route('admin.timetables.index', ['week_start' => $startOfWeek->copy()->addWeek()->toDateString()]) }}">След. неделя →</a>

        </div>


        @php
            $days = collect(range(0, 6))
                ->map(fn($i) => $startOfWeek->copy()->addDays($i));

            $abbr = [
                'понедельник'=>'пн','вторник'=>'вт','среда'=>'ср',
                'четверг'=>'чт','пятница'=>'пт','суббота'=>'сб','воскресенье'=>'вс'
            ];
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
            @php
                // Проверяем существование переменной
                $safeGroupedSlots = $groupedSlots ?? collect();
            @endphp
            <tbody>
            <tr>
                @foreach ($days as $day)
                    @php
                        $dateKey = $day->toDateString();
                        $daySlots = $groupedSlots->get($dateKey, collect());

                        // Сортируем слоты по времени
                        $sortedDaySlots = $daySlots->sortBy(function ($slot) {
                            return \Carbon\Carbon::parse($slot->start_time);
                        });

                        // Группируем слоты по времени суток
                        $morningSlots = [];
                        $afternoonSlots = [];
                        $eveningSlots = [];

                        foreach ($sortedDaySlots as $slot) {
                            $hour = \Carbon\Carbon::parse($slot->start_time)->hour;

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
                        {{-- Утренние слоты (до 12:00) --}}
                        @if (count($morningSlots))
                            <div class="time-period-header">Утро</div>
                            @foreach ($morningSlots as $slot)
                                @php
                                    // Проверяем существование свойства через isset
                                    $isException = isset($slot->parent_id) && $slot->parent_id !== null;
                                    $isCancelled = $slot->cancelled ?? false;
                                    $teacher = $slot->overrideTeacher ?? $slot->teacher ?? null;
                                    $baseSlot = $isException ? ($slot->parent ?? $slot) : $slot;
                                @endphp

                                <div class="slot slot-morning @if(!$slot->active || $isCancelled) slot--inactive @endif">
                                    <div class="slot-header">
                                        <strong>{{ $baseSlot->course->title ?? ($baseSlot->title ?? 'Без названия') }}</strong>
                                        <div>
                                            @if($isException)
                                                <span class="slot-status status-modified">Изменено</span>
                                            @endif
                                            @if($isCancelled)
                                                <span class="slot-status status-cancelled">Отменено</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                        ({{ $slot->duration }} мин)
                                    </div>
                                    @if($teacher)
                                        <div>
                                            Преподаватель:
                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        {{-- Дневные слоты (12:00-17:00) --}}
                        @if (count($afternoonSlots))
                            <div class="time-period-header">День</div>
                            @foreach ($afternoonSlots as $slot)
                                @php
                                    $isException = isset($slot->parent_id) && $slot->parent_id !== null;
                                    $isCancelled = $slot->cancelled ?? false;
                                    $teacher = $slot->overrideTeacher ?? $slot->teacher ?? null;
                                    $baseSlot = $isException ? ($slot->parent ?? $slot) : $slot;
                                @endphp

                                <div class="slot slot-afternoon @if(!$slot->active || $isCancelled) slot--inactive @endif">
                                    <div class="slot-header">
                                        <strong>{{ $baseSlot->course->title ?? ($baseSlot->title ?? 'Без названия') }}</strong>
                                        <div>
                                            @if($isException)
                                                <span class="slot-status status-modified">Изменено</span>
                                            @endif
                                            @if($isCancelled)
                                                <span class="slot-status status-cancelled">Отменено</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                        ({{ $slot->duration }} мин)
                                    </div>
                                    @if($teacher)
                                        <div>
                                            Преподаватель:
                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        {{-- Вечерние слоты (после 17:00) --}}
                        @if (count($eveningSlots))
                            <div class="time-period-header">Вечер</div>
                            @foreach ($eveningSlots as $slot)
                                @php
                                    $isException = isset($slot->parent_id) && $slot->parent_id !== null;
                                    $isCancelled = $slot->cancelled ?? false;
                                    $teacher = $slot->overrideTeacher ?? $slot->teacher ?? null;
                                    $baseSlot = $isException ? ($slot->parent ?? $slot) : $slot;
                                @endphp

                                <div class="slot slot-evening @if(!$slot->active || $isCancelled) slot--inactive @endif">
                                    <div class="slot-header">
                                        <strong>{{ $baseSlot->course->title ?? ($baseSlot->title ?? 'Без названия') }}</strong>
                                        <div>
                                            @if($isException)
                                                <span class="slot-status status-modified">Изменено</span>
                                            @endif
                                            @if($isCancelled)
                                                <span class="slot-status status-cancelled">Отменено</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                        ({{ $slot->duration }} мин)
                                    </div>
                                    @if($teacher)
                                        <div>
                                            Преподаватель:
                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
    </div>
@endsection
