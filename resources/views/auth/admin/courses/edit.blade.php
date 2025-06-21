@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat', sans-serif;
            background: #f8fafc;
            padding: 30px;
            min-height: 100vh;
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #2B2D42;
            margin-bottom: 25px;
            position: relative;
            padding-left: 15px;
        }

        h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            height: 24px;
            width: 4px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border-radius: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            border-left: 4px solid #10b981;
        }

        .course-form {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #edf2f7;
        }

        .form-section h3 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 20px;
            color: #2B2D42;
            margin-bottom: 25px;
            position: relative;
            padding-left: 10px;
        }

        .form-section h3::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            height: 20px;
            width: 3px;
            background: #4f46e5;
            border-radius: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #475569;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            outline: none;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .error {
            color: #ef4444;
            font-size: 13px;
            margin-top: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .slots-container {
            margin-top: 30px;
        }

        .slots-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .slots-table th,
        .slots-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
            font-size: 14px;
        }

        .slots-table th {
            background: #f8fafc;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #475569;
        }

        .slots-table input,
        .slots-table select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .slots-table input:focus,
        .slots-table select:focus {
            border-color: #4f46e5;
            outline: none;
        }

        .btn-remove-slot {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-remove-slot:hover {
            background: #fecaca;
            transform: scale(1.1);
        }

        #btn-add-slot {
            padding: 12px 20px;
            background: #e0e7ff;
            color: #4f46e5;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

        #btn-add-slot:hover {
            background: #c7d2fe;
            transform: translateY(-2px);
        }

        #btn-add-slot i {
            margin-right: 8px;
            font-size: 18px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #edf2f7;
        }

        .btn-submit {
            padding: 14px 30px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(106, 17, 203, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(106, 17, 203, 0.3);
        }

        .btn-cancel {
            padding: 14px 30px;
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            transform: translateY(-3px);
        }

        @media (max-width: 1200px) {
            .admin-content-wrapper {
                margin-left: 0;
                width: 100%;
                padding: 20px 15px;
            }

            .slots-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                text-align: center;
            }
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
{{--                        <td>--}}
{{--                            <input type="date"--}}
{{--                                   name="timetables[{{ $idx }}][date]"--}}
{{--                                   class="form-control"--}}
{{--                                   value="{{ old("timetables.$idx.date", $slot->date) }}">--}}
{{--                        </td>--}}
                        <td>
                            <input type="time"
                                   name="timetables[{{ $idx }}][start_time]"
                                   class="form-control"
                                   value="{{ old("timetables.$idx.start_time", $slot->start_time->format('H:i:s')) }}"
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const errors = @json($errors->all());

        errors.forEach(msg => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: msg,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-toast'
                }
            });
        });
    });
</script>
