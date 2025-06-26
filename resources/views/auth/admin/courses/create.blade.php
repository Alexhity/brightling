{{-- resources/views/auth/admin/courses/create.blade.php --}}
@php use App\Models\User; @endphp
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin: 30px 0;
            text-align: center;
        }
        .course-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 80%;            /* полностью ширина родителя */
            margin: 0 auto 40px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 4px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 8px; border: 1px solid #ccc;
            border-radius: 5px; font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px; transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none; border-color: #615f5f;
        }
        .required::after { content: " *"; color: #ff4c4c; }
        .form-section { margin-bottom: 24px; }
        .form-section h3 {
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 18px; margin-bottom: 12px;
        }
        .slots-container { margin-top: 12px; }
        .slots-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .slots-table th,
        .slots-table td { padding: 8px; border: 1px solid #eee; font-size: 14px; }
        .btn-remove-slot {
            background: #fee2e2; color: #ef4444; border: none;
            width: 24px; height: 24px; border-radius: 50%;
            cursor: pointer; transition: background .2s;
        }
        .btn-remove-slot:hover { background: #fecaca; }
        #btn-add-slot {
            padding: 6px 12px; background: #e0e7ff; color: #4f46e5; border: none;
            border-radius: 5px; cursor: pointer; font-size: 14px;
            transition: background .2s;
        }
        #btn-add-slot:hover { background: #c7d2fe; }
        .buttons { display: flex; justify-content: flex-end; gap: 8px; }
        .btn-submit { background: #beffe6; padding: 10px 20px; border: none; border-radius: 7px; cursor: pointer; }
        .btn-submit:hover { background: #93edca; }
        .btn-cancel { background: #f0f0f0; padding: 10px 20px; border-radius: 7px; text-decoration: none; color: inherit; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Создать новый курс</h2>

        {{-- Validation errors --}}
        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const list = @json($errors->all()).map(e => `<li>${e}</li>`).join('');
                    Swal.fire({
                        toast:true,position:'top-end',icon:'error',
                        title:'Ошибка валидации',
                        html:`<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton:false,timer:5000,timerProgressBar:true
                    });
                });
            </script>
        @endif

        {{-- Success --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        toast:true,position:'top-end',icon:'success',
                        title:@json(session('success')),
                        showConfirmButton:false,timer:3000,timerProgressBar:true
                    });
                });
            </script>
        @endif

        <form id="course-create-form" action="{{ route('admin.courses.store') }}" method="POST" class="course-form" novalidate>
            @csrf

            <div class="form-section">
                <h3>Основная информация</h3>

                <div class="form-group">
                    <label for="title" class="required">Название курса</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label for="language_id" class="required">Язык</label>
                    <select id="language_id" name="language_id" required>
                        <option value="" disabled {{ old('language_id')?'':'selected' }}>Выберите язык</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id')==$lang->id?'selected':'' }}>
                                {{ $lang->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="price_id" class="required">Тариф</label>
                    <select id="price_id" name="price_id" required>
                        <option value="" disabled {{ old('price_id')?'':'selected' }}>Выберите тариф</option>
                        @foreach($prices as $p)
                            <option value="{{ $p->id }}" {{ old('price_id')==$p->id?'selected':'' }}>
                                {{ $p->unit_price }} BYN / {{ $p->lesson_duration }} мин
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="level" class="required">Уровень</label>
                    <select id="level" name="level" required>
                        @foreach($levels as $key=>$label)
                            <option value="{{ $key }}" {{ old('level')==$key?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="format" class="required">Формат</label>
                    <select id="format" name="format" required>
                        <option value="" disabled {{ old('format')?'':'selected' }}>Выберите формат</option>
                        <option value="individual" {{ old('format')=='individual'?'selected':'' }}>Индивидуальный</option>
                        <option value="group" {{ old('format')=='group'?'selected':'' }}>Групповой</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lessons_count" class="required">Количество уроков в месяц</label>
                    <input id="lessons_count" name="lessons_count" type="number" min="1" value="{{ old('lessons_count') }}" required>
                </div>

                <div class="form-group">
                    <label for="duration" class="required">Дата окончания</label>
                    <input id="duration" name="duration" type="date" value="{{ old('duration') }}" required>
                </div>

                {{-- Возрастная группа --}}
                <div class="form-group">
                    <label for="age_group">Возрастная группа</label>
                    <input id="age_group" name="age_group" type="text" value="{{ old('age_group') }}">
                    @error('age_group')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="required">Статус</label>
                    <select id="status" name="status" required>
                        <option value="recruiting" {{ old('status')=='recruiting'?'selected':'' }}>Набор</option>
                        <option value="not_recruiting" {{ old('status')=='not_recruiting'?'selected':'' }}>Без набора</option>
                        <option value="completed" {{ old('status')=='completed'?'selected':'' }}>Завершен</option>
                    </select>
                    <div style="font-size:12px; color:#575757; margin-top:4px;">
                        *на главной странице отображаются курсы с пометкой "Набор"
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Описание курса</h3>
                <div class="form-group">
                    <label for="description" class="required">Описание</label>
                    <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>Расписание занятий</h3>
                <button type="button" id="btn-add-slot">＋ Добавить слот</button>
                <div class="slots-container">
                    <table id="slots-table" class="slots-table">
                        <thead>
                        <tr>
                            <th>День недели *</th><th>Начало *</th><th>Длительность *</th><th>Тип *</th><th>Преподаватель *</th><th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(old('timetables'))
                            @foreach(old('timetables') as $i => $t)
                                <tr data-index="{{ $i }}">
                                    <td><select name="timetables[{{ $i }}][weekday]" required>@foreach($weekdays as $d)
                                                <option value="{{ $d }}" {{ $t['weekday']==$d?'selected':'' }}>{{ $d }}</option>
                                            @endforeach</select></td>
                                    <td><input type="time" name="timetables[{{ $i }}][start_time]" value="{{ $t['start_time'] }}" required></td>
                                    <td><input type="number" name="timetables[{{ $i }}][duration]" min="30" max="240" value="{{ $t['duration'] }}" required></td>
                                    <td><select name="timetables[{{ $i }}][type]" required>
                                            <option value="individual" {{ $t['type']=='individual'?'selected':'' }}>Индивидуал.</option>
                                            <option value="group" {{ $t['type']=='group'?'selected':'' }}>Групп.</option>
                                        </select></td>
                                    <td><select name="timetables[{{ $i }}][user_id]" required>
                                            <option value="">Преподаватель</option>
                                            @foreach($teachers as $tc)
                                                <option value="{{ $tc->id }}" {{ $t['user_id']==$tc->id?'selected':'' }}>
                                                    {{ $tc->first_name }} {{ $tc->last_name }}
                                                </option>
                                            @endforeach
                                        </select></td>
                                    <td><button type="button" class="btn-remove-slot">×</button></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="buttons">
                <button type="submit" class="btn-submit">Создать курс</button>
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let idx = {{ old('timetables') ? count(old('timetables')) : 0 }};
            const weekdays = @json($weekdays), teachers = @json($teachers);

            document.getElementById('btn-add-slot').addEventListener('click', () => {
                const tbody = document.querySelector('#slots-table tbody');
                const row = document.createElement('tr');
                row.dataset.index = idx;
                row.innerHTML = `
                <td><select name="timetables[${idx}][weekday]" required>${
                    weekdays.map(d=>`<option>${d}</option>`).join('')
                }</select></td>
                <td><input type="time" name="timetables[${idx}][start_time]" required></td>
                <td><input type="number" name="timetables[${idx}][duration]" min="30" max="240" required></td>
                <td><select name="timetables[${idx}][type]" required>
                    <option value="individual">Индивидуал.</option>
                    <option value="group">Групп.</option>
                </select></td>
                <td><select name="timetables[${idx}][user_id]" required>
                    <option value="">Преподаватель</option>${
                    teachers.map(t=>`<option value="${t.id}">${t.first_name} ${t.last_name}</option>`).join('')
                }
                </select></td>
                <td><button type="button" class="btn-remove-slot">×</button></td>`;
                tbody.appendChild(row);
                idx++;
            });

            document.addEventListener('click', e => {
                if (e.target.classList.contains('btn-remove-slot')) {
                    const tr = e.target.closest('tr'); tr.remove();
                }
            });

            // client-side validation
            const form = document.getElementById('course-create-form');
            form.addEventListener('submit', e => {
                const errs = [];
                const f = (id,msg)=>{ if(!form[id].value.trim()) errs.push(msg) };
                f('title','Введите название'); f('description','Добавьте описание');
                if(!form.language_id.value) errs.push('Выберите язык');
                if(!form.price_id.value) errs.push('Выберите тариф');
                if(!form.level.value) errs.push('Выберите уровень');
                if(!form.format.value) errs.push('Выберите формат');
                if(!form.lessons_count.value || +form.lessons_count.value<1) errs.push('Укажите уроков');
                if(!form.duration.value) errs.push('Укажите дату окончания');
                if(!form.status.value) errs.push('Выберите статус');
                // расписание
                if(!document.querySelectorAll('#slots-table tbody tr').length)
                    errs.push('Добавьте хотя бы один слот расписания');
                if(errs.length){
                    e.preventDefault();
                    const list = errs.map(m=>`<li>${m}</li>`).join('');
                    Swal.fire({
                        toast:true,position:'top-end',icon:'error',
                        title:'Ошибка валидации',
                        html:`<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton:false,timer:5000,timerProgressBar:true
                    });
                }
            });
        });
    </script>
@endsection
