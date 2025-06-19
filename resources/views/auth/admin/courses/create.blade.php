@php use App\Models\User; @endphp
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 220px);
            font-family: 'Montserrat', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        form.course-form {
            background: #fff;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            max-width: 800px;
        }
        .course-form .form-group {
            margin-bottom: 16px;
        }
        .course-form label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .course-form input[type="text"],
        .course-form input[type="date"],
        .course-form textarea,
        .course-form select {
            width: 100%;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .course-form textarea {
            min-height: 100px;
            resize: vertical;
        }
        .course-form .buttons {
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }
        .course-form .btn-submit {
            background: #4CAF50;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
            transition: background .2s;
        }
        .course-form .btn-submit:hover {
            background: #449d48;
        }
        .course-form .btn-cancel {
            background: #f44336;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
            transition: background .2s;
            text-decoration: none;
        }
        .course-form .btn-cancel:hover {
            background: #d7372f;
        }
        .alert-success {
            margin-bottom: 20px;
            padding: 12px 16px;
            background: #d1e7dd;
            color: #0f5132;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
    </style>
@endsection

    @section('content')
        @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Создать курс</h2>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.courses.store') }}" method="POST" class="course-form">
            @csrf

            <div class="form-group">
                <label for="title">Название</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="language_id">Язык</label>
                <select id="language_id" name="language_id" required>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ old('language_id')==$lang->id?'selected':'' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>
                @error('language_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="price_id">Тариф</label>
                <select id="price_id" name="price_id" required>
                    @foreach($prices as $price)
                        <option value="{{ $price->id }}" {{ old('price_id')==$price->id?'selected':'' }}>
                            {{ $price->unit_price }} ₽ за {{ $price->lesson_duration }} мин
                        </option>
                    @endforeach
                </select>
                @error('price_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="level">Уровень</label>
                <select id="level" name="level" required>
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ old('level')==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('level') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="format">Формат</label>
                <select id="format" name="format" required>
                    @foreach($formats as $key => $label)
                        <option value="{{ $key }}" {{ old('format')==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('format') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="age_group">Возрастная группа</label>
                <input type="text" id="age_group" name="age_group" value="{{ old('age_group') }}">
                @error('age_group') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="lessons_count">Количество уроков</label>
                <input type="text" id="lessons_count" name="lessons_count" value="{{ old('lessons_count') }}" required>
                @error('lessons_count') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="duration">До какой даты (формат YYYY-MM-DD)</label>
                <input type="date" id="duration" name="duration" value="{{ old('duration') }}" required>
                @error('duration') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="status">Статус</label>
                <select id="status" name="status" required>
                    <option value="recruiting"    {{ old('status')=='recruiting'   ?'selected':'' }}>Набор</option>
                    <option value="not_recruiting" {{ old('status')=='not_recruiting'?'selected':'' }}>Без набора</option>
                    <option value="completed"      {{ old('status')=='completed'     ?'selected':'' }}>Завершен</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <h3>Слоты расписания</h3>
            <table id="slots-table" class="table">
                <thead>
                <tr>
                    <th>День недели</th>
                    <th>Дата (по желанию)</th>
                    <th>Время</th>
                    <th>Длит. (мин)</th>
                    <th>Тип</th>
                    <th>Преподаватель</th>
                    <th>Активен</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {{-- нет начальных слотов --}}
                </tbody>
            </table>
            <button type="button" id="btn-add-slot">Добавить слот</button>

            <div class="buttons">
                <button type="submit" class="btn-submit">Создать</button>
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Отмена</a>
            </div>
            @php
                $teachers = User::where('role','teacher')->get();
                    $teacherOptions = collect($teachers)->map(function($u) {
                        return [
                            'id'         => $u->id,
                            'first_name' => $u->first_name,
                            'last_name'  => $u->last_name,
                        ];
                    })->toArray();
            @endphp

            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    let idx = 0;

                    // Теперь просто встраиваем готовые PHP‑массивы
                    const weekdays = @json($weekdays);
                    const types    = @json($types);
                    const teachers = @json($teacherOptions);

                    const tbody = document.querySelector('#slots-table tbody');

                    function renderTeacherOptions(){
                        return teachers
                            .map(u => `<option value="${u.id}">${u.first_name} ${u.last_name}</option>`)
                            .join('');
                    }

                    document.getElementById('btn-add-slot').onclick = function(){
                        const tr = document.createElement('tr');
                        tr.dataset.index = idx;
                        tr.innerHTML = `
            <td>
              <select name="timetables[${idx}][weekday]" required>
                ${weekdays.map(d => `<option value="${d}">${d}</option>`).join('')}
              </select>
            </td>
            <td><input type="date" name="timetables[${idx}][date]"></td>
            <td><input type="time" name="timetables[${idx}][start_time]" required></td>
            <td><input type="number" name="timetables[${idx}][duration]" min="1" required></td>
            <td>
              <select name="timetables[${idx}][type]" required>
                ${Object.entries(types).map(([k,v]) => `<option value="${k}">${v}</option>`).join('')}
              </select>
            </td>
            <td>
              <select name="timetables[${idx}][user_id]" required>
                <option value="">—</option>
                ${renderTeacherOptions()}
              </select>
            </td>
            <td><input type="checkbox" name="timetables[${idx}][active]" value="1" checked></td>
            <td><button type="button" class="btn-remove-slot">×</button></td>
        `;
                        tbody.appendChild(tr);
                        idx++;
                    };

                    tbody.addEventListener('click', e => {
                        if (e.target.classList.contains('btn-remove-slot')) {
                            e.target.closest('tr').remove();
                        }
                    });
                });
            </script>
        </form>
    </div>

@endsection
