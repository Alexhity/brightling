@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 220px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin: 0;
        }
        .section {
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .section h3 {
            font-family: 'Montserrat SemiBold', sans-serif;
            margin-bottom: 12px;
            color: #2B2D42;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-family: 'Montserrat Medium', sans-serif;
            text-align: left;
        }
        .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #000;      /* иконка чёрная */
            transition: color .2s;
        }
        .btn-delete:hover {
            color: #c0392b;
        }
        .form-add {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 16px;
        }
        .form-add select {
            flex: 1;
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
            background: #fff;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-add button {
            padding: 8px 16px;
            background: #8986FF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            cursor: pointer;
            transition: transform .2s;
        }
        .form-add button:hover {
            transform: scale(1.05);
        }
        .btn-back {
            text-decoration: none;
            color: #8986FF;
            font-family: 'Montserrat Medium', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Участники курса: «{{ $course->title }}»</h2>
            <a href="{{ route('admin.courses.index') }}" class="btn-back">&larr; Назад к списку курсов</a>
        </div>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', ()=> {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: { popup: 'swal2-toast' }
                    });
                });
            </script>
            @php session()->forget('success'); @endphp
        @endif

        {{-- Преподаватели --}}
        <div class="section">
            <h3>Преподаватели</h3>

            {{-- Форма добавления преподавателя --}}
            <form action="{{ route('admin.courses.addTeacher', $course) }}"
                  method="POST"
                  class="form-add"
                  novalidate>
                @csrf
                <select name="user_id" required>
                    <option value="">Выберите преподавателя</option>
                    @foreach($allTeachers as $ut)
                        @if(!$teachers->contains($ut))
                            <option value="{{ $ut->id }}">
                                {{ $ut->first_name }} {{ $ut->last_name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <button type="submit">Добавить преподавателя</button>
            </form>

            {{-- Список текущих --}}
            <table>
                <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                @forelse($teachers as $t)
                    <tr>
                        <td>{{ $t->first_name }} {{ $t->last_name }}</td>
                        <td>{{ $t->email }}</td>
                        <td>
                            <form action="{{ route('admin.courses.removeTeacher', [$course, $t]) }}"
                                  method="POST"
                                  data-delete-form
                                  data-name="{{ $t->first_name }} {{ $t->last_name }}">
                                @csrf @method('DELETE')
                                <!-- сделаем submit-кнопкой -->
                                <button type="submit" class="btn-delete" title="Удалить преподавателя">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">Нет преподавателей</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Студенты --}}
        <div class="section">
            <h3>Студенты</h3>

            {{-- Форма добавления студента --}}
            <form action="{{ route('admin.courses.addStudent', $course) }}"
                  method="POST"
                  class="form-add"
                  novalidate>
                @csrf
                <select name="user_id" required>
                    <option value="">Выберите студента</option>
                    @foreach($allStudents as $us)
                        @if(!$students->contains($us))
                            <option value="{{ $us->id }}">
                                {{ $us->first_name }} {{ $us->last_name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <button type="submit">Добавить студента</button>
            </form>

            {{-- Список текущих --}}
            <table>
                <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                @forelse($students as $s)
                    <tr>
                        <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                        <td>{{ $s->email }}</td>
                        <td>
                            <form action="{{ route('admin.courses.removeStudent', [$course, $s]) }}"
                                  method="POST"
                                  onsubmit="return false"
                                  data-delete-form
                                  data-name="{{ $s->first_name }} {{ $s->last_name }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Удалить студента">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">Нет студентов</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Подтверждение удаления
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        title: `Удалить «${form.dataset.name}»?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true
                    }).then(res => res.isConfirmed && form.submit());
                });
            });
        });
    </script>

@endsection

