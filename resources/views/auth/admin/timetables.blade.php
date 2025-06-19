{{--@extends('layouts.app')--}}

{{--@section('styles')--}}
{{--    <style>--}}
{{--        .container-timetable{--}}
{{--            padding-left: 170px;--}}
{{--        }--}}
{{--    </style>--}}
{{--@endsection--}}

{{--@section('content')--}}
{{--    @include('layouts.left_sidebar_admin')--}}
{{--    <div class="container-timetable">--}}
{{--        <h1>Расписание</h1>--}}

{{--        <div style="display:flex; align-items:center; gap:12px;">--}}

{{--            --}}{{-- Кнопка назад --}}
{{--            <a href="{{ route('admin.timetable.index', array_merge(request()->except(['week_start','today']), [--}}
{{--          $view === 'month' ? 'month_start' : 'week_start' => $periodStart->copy()->sub($view==='month'? '1 month':'1 week')->toDateString()--}}
{{--      ])) }}"--}}
{{--               class="btn-nav">←</a>--}}

{{--            --}}{{-- Кнопка Сегодня --}}
{{--            <a href="{{ route('admin.timetable.index', array_merge(request()->except(['week_start','today','month_start']), [--}}
{{--          'today' => true--}}
{{--      ])) }}"--}}
{{--               class="btn-nav">Сегодня</a>--}}

{{--            --}}{{-- Кнопка вперёд --}}
{{--            <a href="{{ route('admin.timetable.index', array_merge(request()->except(['week_start','today']), [--}}
{{--          $view === 'month' ? 'month_start' : 'week_start' => $periodStart->copy()->add($view==='month'? '1 month':'1 week')->toDateString()--}}
{{--      ])) }}"--}}
{{--               class="btn-nav">→</a>--}}

{{--            --}}{{-- Тумблер вида --}}
{{--            <div class="view-toggle" style="display:flex; gap:4px; margin-left:16px;">--}}
{{--                <a href="{{ route('admin.timetable.index', array_merge(request()->except(['view']), ['view'=>'week'])) }}"--}}
{{--                   class="btn-toggle {{ $view==='week'?'active':'' }}">--}}
{{--                    Неделя--}}
{{--                </a>--}}
{{--                <a href="{{ route('admin.timetable.index', array_merge(request()->except(['view']), ['view'=>'month'])) }}"--}}
{{--                   class="btn-toggle {{ $view==='month'?'active':'' }}">--}}
{{--                    Месяц--}}
{{--                </a>--}}
{{--            </div>--}}

{{--        </div>--}}

{{--        <div class="d-flex justify-content-between align-items-center mb-3">--}}
{{--            <a href="{{ route('admin.timetable.index', ['start' => $start->copy()->subWeek()->toDateString()]) }}"--}}
{{--               class="btn btn-outline-primary">‹ Неделя</a>--}}
{{--            <h4>{{ $start->format('d.m.Y') }} – {{ $start->copy()->addWeek()->subDay()->format('d.m.Y') }}</h4>--}}
{{--            <a href="{{ route('admin.timetable.index', ['start' => $start->copy()->addWeek()->toDateString()]) }}"--}}
{{--               class="btn btn-outline-primary">Неделя ›</a>--}}
{{--        </div>--}}

{{--        <table class="table table-bordered calendar">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                @foreach(['Пн','Вт','Ср','Чт','Пт','Сб','Вс'] as $i => $day)--}}
{{--                    <th class="text-center">--}}
{{--                        {{ $day }}<br>--}}
{{--                        {{ $start->copy()->addDays($i)->format('d.m') }}--}}
{{--                    </th>--}}
{{--                @endforeach--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            --}}{{-- Здесь можно разбить по часам или просто в одну строку --}}
{{--            <tr>--}}
{{--                @foreach(range(0,6) as $i)--}}
{{--                    @php--}}
{{--                        $date = $start->copy()->addDays($i)->toDateString();--}}
{{--                        $dayLessons = $lessons->where('date',$date);--}}
{{--                    @endphp--}}
{{--                    <td style="vertical-align: top; min-width: 150px;">--}}
{{--                        @foreach($dayLessons as $lesson)--}}
{{--                            <div class="card mb-2">--}}
{{--                                <div class="card-body p-2">--}}
{{--                                    <strong>{{ \Carbon\Carbon::parse($lesson->time)->format('H:i') }}</strong>--}}
{{--                                    — {{ Str::limit($lesson->course->title, 20) }}<br>--}}
{{--                                    <small>--}}
{{--                                        {{ optional($lesson->instructor)->first_name }}--}}
{{--                                        {{ optional($lesson->instructor)->last_name }}--}}
{{--                                    </small><br><br>--}}
{{--                                    @if($lesson->status === 'cancelled')--}}
{{--                                        <span class="badge badge-danger">Отменён</span>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                        --}}{{-- Кнопка добавить разовый слот --}}
{{--                        <button type="button"--}}
{{--                                class="btn btn-sm btn-outline-success mt-1"--}}
{{--                                data-toggle="modal"--}}
{{--                                data-target="#addOneOff"--}}
{{--                                data-date="{{ $date }}">--}}
{{--                            + Разовый--}}
{{--                        </button>--}}
{{--                    </td>--}}
{{--                @endforeach--}}
{{--            </tr>--}}
{{--            </tbody>--}}
{{--        </table>--}}

{{--        --}}{{-- Модальное окно “Добавить разовый урок” --}}
{{--        <div class="modal fade" id="addOneOff" tabindex="-1">--}}
{{--            <div class="modal-dialog">--}}
{{--                <form method="POST" action="#">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="is_once" value="1">--}}
{{--                    <div class="modal-content">--}}
{{--                        <div class="modal-header">--}}
{{--                            <h5 class="modal-title">Добавить однократный урок</h5>--}}
{{--                            <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--                        </div>--}}
{{--                        <div class="modal-body">--}}
{{--                            <input type="hidden" name="date" id="oneOffDate">--}}
{{--                            <div class="form-group">--}}
{{--                                <label>Время</label>--}}
{{--                                <input type="time" name="time" class="form-control" required>--}}
{{--                            </div>--}}
{{--                            <div class="form-group">--}}
{{--                                <label>Курс</label>--}}
{{--                                <select name="course_id" class="form-control" required>--}}
{{--                                    @foreach($courses as $c)--}}
{{--                                        <option value="{{ $c->id }}">{{ $c->title }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="submit" class="btn btn-primary">Добавить</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <script>--}}
{{--        // передать дату в форму модального окна--}}
{{--        $('#addOneOff').on('show.bs.modal', function(e){--}}
{{--            var date = e.relatedTarget.dataset.date;--}}
{{--            this.querySelector('#oneOffDate').value = date;--}}
{{--        });--}}
{{--    </script>--}}

{{--@endsection--}}

