@extends('layouts.app')

@section('styles')
    <style>
        /* Общая обёртка */
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        /* Заголовок */
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin: 30px 0;
            text-align: center;
        }
        /* Форма */
        .lesson-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 80%;
            margin: 0 auto 40px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 4px; font-family: 'Montserrat SemiBold', sans-serif; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;
            font-family: 'Montserrat Medium', sans-serif; font-size: 14px;
            transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none; border-color: #615f5f;
        }
        .required::after { content: " *"; color: #ff4c4c; }
        /* Таблица студентов */
        .lesson-form table.students {
            width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px;
        }
        .students th,
        .students td {
            padding: 8px; border: 1px solid #eee; text-align: left;
        }
        /* Кнопки */
        .buttons {
            display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;
        }
        .btn-submit {
            background: #beffe6; padding: 10px 20px; border: none; border-radius: 7px;
            cursor: pointer; font-family: 'Montserrat Medium', sans-serif;
            transition: background .2s;
        }
        .btn-submit:hover { background: #93edca; }
        .btn-cancel {
            background: #f0f0f0; padding: 10px 20px; border-radius: 7px;
            text-decoration: none; color: inherit; font-family: 'Montserrat Medium', sans-serif;
        }

        /* Сгруппируем текущий файл и чекбокс в колонку, но близко друг к другу */
        .materials-group .current-file,
        .materials-group .remove-file {
            margin-bottom: 8px;  /* небольшой отступ */
            font-family: 'Montserrat Medium', sans-serif;
        }

        /* Сделаем чекбокс и текст в одну линию */
        .remove-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
        }

        /* Можно чуть поднять чекбокс, чтобы он выровнялся по тексту */
        .remove-checkbox input[type="checkbox"] {
            transform: translateY(1px);
        }

    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="admin-content-wrapper">
        <h2>Редактирование урока</h2>

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const list = @json($errors->all()).map(e => `<li>${e}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        <form action="{{ route('teacher.lessons.update', $lesson) }}"
              method="POST"
              class="lesson-form"
              enctype="multipart/form-data"
              id="lesson-edit-form"
              novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Дата и время</label>
                <input type="text" class="disabled"
                       value="{{ $lesson->date->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($lesson->time)->format('H:i') }}"
                       disabled>
            </div>

            <div class="form-group">
                <label>Тип урока</label>
                <input type="text" class="disabled" value="{{ ucfirst($lesson->type) }}" disabled>
            </div>

            <div class="form-group">
                <label>Курс</label>
                <input type="text" class="disabled"
                       value="{{ optional($lesson->course)->title ?? '—' }}" disabled>
            </div>

            <div class="form-group">
                <label for="zoom_link">Ссылка на урок</label>
                <input type="url" id="zoom_link" name="zoom_link"
                       value="{{ old('zoom_link', $lesson->zoom_link) }}">
            </div>

            <div class="form-group materials-group">
                <label for="material_path">Материал (файл):</label>

                @if($lesson->material_path)
                    <div class="current-file">
                        Текущий файл:
                        <a href="{{ asset($lesson->material_path) }}" target="_blank">
                            {{ \Illuminate\Support\Str::afterLast($lesson->material_path, '/') }}
                        </a>
                    </div>

                    <div class="remove-file">
                        <label class="remove-checkbox">
                            <input type="checkbox" name="remove_material" value="1">
                            Удалить материал
                        </label>
                    </div>
                @endif

                <input type="file" name="material_path" id="material_path">
                @error('material_path')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <table class="students">
                <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Статус</th>
{{--                    <th>Оценка</th>--}}
                </tr>
                </thead>
                <tbody>
                @php
                    $allLangs = \App\Models\Language::orderBy('name')->get();
                @endphp
                @foreach($lesson->users as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>

                        <td>
                            <select name="statuses[{{ $user->id }}]">
                                <option value="" {{ is_null($user->pivot->status) ? 'selected' : '' }}>—</option>
                                <option value="present" {{ $user->pivot->status==='present'?'selected':'' }}>Присутствовал</option>
                                <option value="absent"  {{ $user->pivot->status==='absent' ?'selected':'' }}>Отсутствовал</option>
                            </select>
                        </td>

                        <td>
                            <input type="number"
                                   name="marks[{{ $user->id }}]"
                                   value="{{ $user->pivot->mark }}"
                                   min="0" max="100"
                                {{ $user->pivot->status!=='present' ? 'disabled' : '' }}>
                        </td>

                        <td>
                            <select name="languages[{{ $user->id }}]">
                                <option value="">—</option>
                                @foreach($allLangs as $lang)
                                    <option value="{{ $lang->id }}"
                                        {{ $user->languages->contains($lang->id) ? 'selected' : '' }}>
                                        {{ $lang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <select name="levels[{{ $user->id }}]">
                                <option value="">—</option>
                                @foreach(['beginner','A1','A2','B1','B2','C1','C2'] as $lvl)
                                    @php
                                        // уровень из pivot в language_user
                                        $existing = $user->languages->firstWhere('id', request()->old("languages.{$user->id}", $user->languages->pluck('id')->first()));
                                        $current = $existing?->pivot->level;
                                    @endphp
                                    <option value="{{ $lvl }}"
                                        {{ $current === $lvl ? 'selected' : '' }}>
                                        {{ strtoupper($lvl) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="buttons">
                <button type="submit" class="btn-submit">Сохранить</button>
                <a href="{{ route('teacher.lessons.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        // Дополнительная фронтенд-валидация (примера ради)
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('lesson-edit-form');
            form.addEventListener('submit', e => {
                const errs = [];
                // Можно добавить проверки по желанию
                if (errs.length) {
                    e.preventDefault();
                    const list = errs.map(m => `<li>${m}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            });
        });
    </script>
@endsection
