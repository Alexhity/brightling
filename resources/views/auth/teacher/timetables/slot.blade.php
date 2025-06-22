@props(['slot', 'teacher']) {{-- Добавляем эту строку в начало --}}
@php
    $isOverride = $slot->override_user_id == $teacher->id;
    $isCancelled = $slot->cancelled;

    // Определяем период времени для стиля
    $hour = \Carbon\Carbon::parse($slot->start_time)->hour;
    $timeClass = '';
    if ($hour < 12) {
        $timeClass = 'slot-morning';
    } elseif ($hour < 17) {
        $timeClass = 'slot-afternoon';
    } else {
        $timeClass = 'slot-evening';
    }
@endphp

<div class="slot {{ $timeClass }} @if($isCancelled) slot--inactive @endif">
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
