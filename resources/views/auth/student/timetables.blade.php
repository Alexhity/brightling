@extends('layouts.app')

@section('styles')
    <style>
        .container-timetable{
            padding-left: 170px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')
    <div class="container-timetable">
        <h1>Расписание</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('student.timetable.index', ['start' => $start->copy()->subWeek()->toDateString()]) }}"
               class="btn btn-outline-primary">‹ Неделя</a>
            <h4>{{ $start->format('d.m.Y') }} – {{ $start->copy()->addWeek()->subDay()->format('d.m.Y') }}</h4>
            <a href="{{ route('student.timetable.index', ['start' => $start->copy()->addWeek()->toDateString()]) }}"
               class="btn btn-outline-primary">Неделя ›</a>
        </div>

        <table class="table table-bordered calendar">
            <thead>
            <tr>
                @foreach(['Пн','Вт','Ср','Чт','Пт','Сб','Вс'] as $i => $day)
                    <th class="text-center">
                        {{ $day }}<br>
                        {{ $start->copy()->addDays($i)->format('d.m') }}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            {{-- Здесь можно разбить по часам или просто в одну строку --}}
            <tr>
                @foreach(range(0,6) as $i)
                    @php
                        $date = $start->copy()->addDays($i)->toDateString();
                        $dayLessons = $lessons->where('date',$date);
                    @endphp
                    <td style="vertical-align: top; min-width: 150px;">
                        @foreach($dayLessons as $lesson)
                            <div class="card mb-2">
                                <div class="card-body p-2">
                                    <strong>{{ \Carbon\Carbon::parse($lesson->time)->format('H:i') }}</strong>
                                    — {{ Str::limit($lesson->course->title, 20) }}<br>
                                    <small>
                                        {{ optional($lesson->instructor)->first_name }}
                                        {{ optional($lesson->instructor)->last_name }}
                                    </small><br><br>
                                    @if($lesson->status === 'cancelled')
                                        <span class="badge badge-danger">Отменён</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>


    <script>
        // передать дату в форму модального окна
        $('#addOneOff').on('show.bs.modal', function(e){
            var date = e.relatedTarget.dataset.date;
            this.querySelector('#oneOffDate').value = date;
        });
    </script>

@endsection

