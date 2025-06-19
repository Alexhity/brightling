{{-- resources/views/auth/admin/timetables/create.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper{ margin-left:200px; width:calc(100% - 200px); padding:20px; }
        .form-group{ margin-bottom:16px; }
        label{ display:block; margin-bottom:4px; font-weight:bold; }
        .form-control{ width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; }
        .btn-submit{ background:#4CAF50; color:#fff; padding:8px 16px; border:none; border-radius:4px; }
        .btn-cancel{ background:#f44336; color:#fff; padding:8px 16px; border:none; border-radius:4px; margin-left:8px; }
        #block-recurring{ display:none; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Создать слот</h2>
        <form action="{{ route('admin.timetables.store') }}" method="POST">
            @csrf



            <div class="form-group">
                <label>Курс (необязательно)</label>
                <select name="course_id" class="form-control">
                    <option value="">— без привязки —</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ old('course_id')==$c->id?'selected':'' }}>
                            {{ $c->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Тариф</label>
                <select name="price_id" class="form-control" required>
                    <option value="">— выберите тариф —</option>
                    @foreach($prices as $p)
                        <option value="{{ $p->id }}" {{ old('price_id')==$p->id?'selected':'' }}>
                            {{ $p->lesson_duration }} мин — {{ $p->unit_price }} BYN
                        </option>
                    @endforeach
                </select>
                @error('price_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Тип слота</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="group"      {{ old('type')=='group'?'selected':'' }}>Групповой</option>
                    <option value="individual" {{ old('type')=='individual'?'selected':'' }}>Индивидуальный</option>
                    <option value="free"       {{ old('type')=='free'?'selected':'' }}>Бесплатный</option>
                </select>
                @error('type')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Преподаватель</label>
                <select name="user_id" class="form-control" required>
                    <option value="">— выберите —</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('user_id')==$t->id?'selected':'' }}>
                            {{ $t->first_name }} {{ $t->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Повторяемость</label><br>
                <label>
                    <input type="radio" name="repeat_type" value="single"
                        {{ old('repeat_type','single')==='single'?'checked':'' }}>
                    Разовый
                </label>
                <label style="margin-left:12px">
                    <input type="radio" name="repeat_type" value="recurring"
                        {{ old('repeat_type')==='recurring'?'checked':'' }}>
                    Регулярный
                </label>
                @error('repeat_type')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Разовый --}}
            <div id="block-single">
                <div class="form-group">
                    <label>Дата слота</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date') }}">
                    @error('date')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Регулярный --}}
            <div id="block-recurring">
                <div class="form-group">
                    <label>День недели</label>
                    <select name="weekday" class="form-control">
                        <option value="">—</option>
                        @foreach($weekdays as $wd)
                            <option value="{{ $wd }}" {{ old('weekday')==$wd?'selected':'' }}>
                                {{ ucfirst($wd) }}
                            </option>
                        @endforeach
                    </select>
                    @error('weekday')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Дата окончания</label>
                    <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                    @error('ends_at')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Время начала</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                @error('start_time')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Длительность (мин)</label>
                <input type="number" name="duration" class="form-control" min="1" value="{{ old('duration') }}" required>
                @error('duration')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" value="1" {{ old('active',1)?'checked':'' }}>
                    Активен
                </label>
            </div>

            <button type="submit" class="btn-submit">Создать слот</button>
            <a href="{{ route('admin.timetables.index') }}" class="btn-cancel">Отмена</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rSingle = document.querySelector('input[value="single"]');
            const rRec    = document.querySelector('input[value="recurring"]');
            const bs      = document.getElementById('block-single');
            const br      = document.getElementById('block-recurring');

            function toggleRepeat() {
                if (rRec.checked) {
                    bs.style.display = 'none';
                    br.style.display = 'block';
                } else {
                    bs.style.display = 'block';
                    br.style.display = 'none';
                }
            }
            rSingle.addEventListener('change', toggleRepeat);
            rRec.addEventListener('change', toggleRepeat);
            toggleRepeat();
        });
    </script>
@endpush
