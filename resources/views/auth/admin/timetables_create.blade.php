
{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    @include('layouts.left_sidebar_admin')--}}
{{--    <div class="section-timetable" style="margin-left: 150px">--}}
{{--    <h1>Добавить новый слот расписания</h1>--}}

{{--    <form method="POST" action="{{ route('admin.timetable.store') }}">--}}
{{--        @csrf--}}

{{--        <div class="form-group">--}}
{{--            <label>Курс (необязательно):</label>--}}
{{--            <select name="course_id" class="form-control">--}}
{{--                <option value="">— без курса —</option>--}}
{{--                @foreach($courses as $course)--}}
{{--                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>--}}
{{--                        {{ $course->title }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>День недели:</label>--}}
{{--            <select name="weekday" class="form-control" required>--}}
{{--                @foreach(['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'] as $d)--}}
{{--                    <option value="{{ $d }}" {{ old('weekday') == $d ? 'selected' : '' }}>--}}
{{--                        {{ ucfirst($d) }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Время начала:</label>--}}
{{--            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '18:00') }}" required>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Длительность (мин):</label>--}}
{{--            <input type="number" name="duration" class="form-control" value="{{ old('duration', 60) }}" min="1" required>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Тип:</label>--}}
{{--            <select name="type" class="form-control" required>--}}
{{--                <option value="group" {{ old('type') == 'group' ? 'selected' : '' }}>Групповой</option>--}}
{{--                <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Индивидуальный</option>--}}
{{--                <option value="free" {{ old('type') == 'free' ? 'selected' : '' }}>Бесплатный</option>--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Преподаватель (необязательно):</label>--}}
{{--            <select name="user_id" class="form-control">--}}
{{--                <option value="">— без преподавателя —</option>--}}
{{--                @foreach($teachers as $teacher)--}}
{{--                    <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>--}}
{{--                        {{ $teacher->first_name }} {{ $teacher->last_name }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Статус (активен):</label>--}}
{{--            <select name="active" class="form-control" required>--}}
{{--                <option value="1" {{ old('active',1) == 1 ? 'selected' : '' }}>Да</option>--}}
{{--                <option value="0" {{ old('active') == 0 ? 'selected' : '' }}>Нет</option>--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Привязка к заявке (если слот для free):</label>--}}
{{--            <select name="request_id" class="form-control">--}}
{{--                <option value="">— нет заявки —</option>--}}
{{--                @foreach($requests as $req)--}}
{{--                    <option value="{{ $req->id }}" {{ old('request_id') == $req->id ? 'selected' : '' }}>--}}
{{--                        №{{ $req->id }} — {{ $req->name }} ({{ $req->email }})--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <button type="submit" class="btn btn-primary">Сохранить слот</button>--}}
{{--    </form>--}}
{{--    </div>--}}
{{--@endsection--}}
