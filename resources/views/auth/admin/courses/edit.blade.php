@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px); /* Исправлено: добавлены пробелы вокруг «-» */
            font-family: 'Montserrat', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin: 20px 0;
        }
        .course-form {
            background: #fff;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            max-width: 800px;
            margin-bottom: 40px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .buttons {
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }
        .btn-submit {
            background: #4CAF50;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .btn-cancel {
            background: #ccc;
            color: #333;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .alert-success {
            margin-bottom: 20px;
            padding: 12px 16px;
            background: #d1e7dd;
            color: #0f5132;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .error {
            color: #c0392b;
            font-size: 14px;
            margin-top: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        /* Стили для расписания */
        #slots-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        #slots-table th,
        #slots-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
        }
        #btn-add-slot {
            margin-top: 10px;
            background: #e6e2f8;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .btn-remove-slot {
            background: #ffcccc;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Редактировать курс</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.courses.update', $course) }}" method="POST" class="course-form">
            @csrf
            @method('PUT')

            {{-- Название --}}
            <div class="form-group">
                <label for="title">Название</label>
                <input id="title" name="title" type="text" value="{{ old('title', $course->title) }}" required>
                @error('title')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Описание --}}
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description">{{ old('description', $course->description) }}</textarea>
                @error('description')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Язык --}}
            <div class="form-group">
                <label for="language_id">Язык</label>
                <select id="language_id" name="language_id" required>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}"
                            {{ old('language_id', $course->language_id)==$lang->id ? 'selected' : '' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>
                @error('language_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Тариф --}}
            <div class="form-group">
                <label for="price_id">Тариф</label>
                <select id="price_id" name="price_id" required>
                    @foreach($prices as $price)
                        <option value="{{ $price->id }}"
                            {{ old('price_id', $course->price_id)==$price->id ? 'selected' : '' }}>
                            {{ $price->unit_price }} ₽ за {{ $price->lesson_duration }} мин
                        </option>
                    @endforeach
                </select>
                @error('price_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Уровень --}}
            <div class="form-group">
                <label for="level">Уровень</label>
                <select id="level" name="level" required>
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('level', $course->level)==$key ? 'selected' : '' }}>
                            {{ $label=='beginner' ? 'Начинающий' : $label }}
                        </option>
                    @endforeach
                </select>
                @error('level')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Формат --}}
            <div class="form-group">
                <label for="format">Формат</label>
                <select id="format" name="format" required>
                    @foreach($formats as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('format', $course->format)==$key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('format')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Возрастная группа --}}
            <div class="form-group">
                <label for="age_group">Возрастная группа</label>
                <input id="age_group" name="age_group" type="text" value="{{ old('age_group', $course->age_group) }}">
                @error('age_group')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Количество уроков --}}
            <div class="form-group">
                <label for="lessons_count">Количество уроков</label>
                <input id="lessons_count" name="lessons_count" type="text" value="{{ old('lessons_count', $course->lessons_count) }}" required>
                @error('lessons_count')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- До какой даты --}}
            <div class="form-group">
                <label for="duration">До какой даты</label>
                <input id="duration" name="duration" type="date"
                       value="{{ old('duration', $course->duration ? $course->duration->format('Y-m-d') : '') }}" required>
                @error('duration')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Статус --}}
            <div class="form-group">
                <label for="status">Статус</label>
                <select id="status" name="status" required>
                    <option value="recruiting"    {{ old('status', $course->status)=='recruiting'    ? 'selected':'' }}>Набор</option>
                    <option value="not_recruiting" {{ old('status', $course->status)=='not_recruiting' ? 'selected':'' }}>Без набора</option>
                    <option value="completed"      {{ old('status', $course->status)=='completed'      ? 'selected':'' }}>Завершен</option>
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Слоты расписания --}}
            <h3>Слоты расписания</h3>
            <button type="button" id="btn-add-slot" class="btn-create">+ Добавить слот</button>
            <table id="slots-table" class="table">
                <thead>
                <tr>
                    <th>День недели</th>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Длит. (мин)</th>
                    <th>Тип</th>
                    <th>Преподаватель</th>
                    <th>Активен</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php $idx = 0; @endphp
                @foreach($slots as $slot)
                    <tr data-index="{{ $idx }}">
                        <td>
                            <select name="timetables[{{ $idx }}][weekday]" class="form-control">
                                @foreach($weekdays as $wd)
                                    <option value="{{ $wd }}" @if($slot->weekday === $wd) selected @endif>
                                        {{ ucfirst($wd) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="date"
                                   name="timetables[{{ $idx }}][date]"
                                   class="form-control"
                                   value="{{ old("timetables.$idx.date", $slot->date) }}">
                        </td>
                        <td>
                            <input type="time"
                                   name="timetables[{{ $idx }}][start_time]"
                                   class="form-control"
                                   value="{{ old("timetables.$idx.start_time", $slot->start_time) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number"
                                   name="timetables[{{ $idx }}][duration]"
                                   class="form-control"
                                   min="1"
                                   value="{{ old("timetables.$idx.duration", $slot->duration) }}"
                                   required>
                        </td>
                        <td>
                            <select name="timetables[{{ $idx }}][type]" class="form-control">
                                @foreach($types as $key=>$label)
                                    <option value="{{ $key }}" @if($slot->type === $key) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="timetables[{{ $idx }}][user_id]" class="form-control">
                                <option value="">—</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" @if($slot->user_id === $t->id) selected @endif>
                                        {{ $t->first_name }} {{ $t->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="checkbox"
                                   name="timetables[{{ $idx }}][active]"
                                   value="1"
                                   @if($slot->active) checked @endif>
                        </td>
                        <td>
                            <button type="button" class="btn-remove-slot">×</button>
                        </td>
                        <input type="hidden"
                               name="timetables[{{ $idx }}][id]"
                               value="{{ $slot->id }}">
                    </tr>
                    @php $idx++; @endphp
                @endforeach
                </tbody>
            </table>

            {{-- Кнопки --}}
            <button type="submit" class="btn-submit">Сохранить</button>
            <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Отмена</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            let idx = {{ $idx }};
            const weekdays = @json($weekdays);
            const types    = @json($types);
            const teachers = @json($teachers->map(fn($u)=>['id'=>$u->id,'first_name'=>$u->first_name,'last_name'=>$u->last_name])->toArray());
            const tbody    = document.querySelector('#slots-table tbody');

            function renderTeacherOptions(){
                return teachers.map(u=>`<option value="${u.id}">${u.first_name} ${u.last_name}</option>`).join('');
            }

            document.getElementById('btn-add-slot').addEventListener('click', ()=>{
                const tr = document.createElement('tr');
                tr.dataset.index = idx;
                tr.innerHTML = `
            <td>
              <select name="timetables[${idx}][weekday]" class="form-control" required>
                ${weekdays.map(d=>`<option value="${d}">${d}</option>`).join('')}
              </select>
            </td>
            <td><input type="date" name="timetables[${idx}][date]" class="form-control"></td>
            <td><input type="time" name="timetables[${idx}][start_time]" class="form-control" required></td>
            <td><input type="number" name="timetables[${idx}][duration]" class="form-control" min="1" required></td>
            <td>
              <select name="timetables[${idx}][type]" class="form-control" required>
                ${Object.entries(types).map(([k,v])=>`<option value="${k}">${v}</option>`).join('')}
              </select>
            </td>
            <td>
              <select name="timetables[${idx}][user_id]" class="form-control" required>
                <option value="">—</option>
                ${renderTeacherOptions()}
              </select>
            </td>
            <td><input type="checkbox" name="timetables[${idx}][active]" value="1" checked></td>
            <td><button type="button" class="btn-remove-slot">×</button></td>
        `;
                tbody.appendChild(tr);
                idx++;
            });

            tbody.addEventListener('click', e=>{
                if(e.target.classList.contains('btn-remove-slot')){
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endpush

