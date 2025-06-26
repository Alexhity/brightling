@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #333333;
            font-size: 32px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-create {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            text-decoration: none;
            border-radius: 7px;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .btn-create:hover {
            transform: scale(1.05);
        }
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            width: 100%;
        }
        .filter-bar input,
        .filter-bar select {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .filter-bar button {
            padding: 8px 3px;
            background: #8986FF;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .filter-bar button:hover {
            transform: scale(1.05);
        }
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 1rem;
        }
        table.courses {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .courses th,
        .courses td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-family: 'Montserrat Medium', sans-serif;
            color: #333333;
        }
        .courses th {
            background: #fef6e0;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
        }
        .courses td {
            font-size: 14px;
        }
        .expand-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333333;
        }
        .detail-row {
            display: none;
        }

        .table-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #f0f0f0;
            color: #333;
            transition: background 0.2s;
        }

        .table-action-btn:hover {
            background: #d9d9d9;
        }

        /* Если нужно выделить кнопку удаления другим цветом */
        .table-action-delete {
            background: #ffcccc;
            color: #c00;
        }
        .table-action-delete:hover {
            background: #ffaaaa;
        }

        .table-action-btn i {
            font-size: 16px;
        }

    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Список курсов</h2>
            <a href="{{ route('admin.courses.create') }}" class="btn-create">
                <span class="icon-plus">＋</span>Создать курс
            </a>
        </div>

        <form method="GET" action="{{ route('admin.courses.index') }}">
            <div class="filter-bar">
                <input type="text" name="search" placeholder="Поиск курса" value="{{ $search }}" />
                <select name="status">
                    <option value="">Все статусы</option>
                    <option value="recruiting" {{ $status=='recruiting'?'selected':'' }}>Набор</option>
                    <option value="not_recruiting" {{ $status=='not_recruiting'?'selected':'' }}>Без набора</option>
                    <option value="completed" {{ $status=='completed'?'selected':'' }}>Завершён</option>
                </select>
                <select name="language">
                    <option value="">Все языки</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ $language==$lang->id?'selected':'' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>
                <select name="level">
                    <option value="">Все уровни</option>
                    @foreach($levels as $key=>$label)
                        <option value="{{ $key }}" {{ $level==$key?'selected':'' }}>
                            {{ $key === 'beginner' ? 'Начинающий' : $key }}
                        </option>
                    @endforeach
                </select>
                <select name="format">
                    <option value="">Все форматы</option>
                    @foreach($formats as $key=>$label)
                        <option value="{{ $key }}" {{ $format==$key?'selected':'' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <button type="submit">Применить</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="courses">
                <thead>
                <tr>
                    <th></th>
                    <th class="sortable" onclick="sortTable('title')" title="Сортировать по названию">
                        Название
                        @if($sort==='title')
                            ({{ $dir==='asc' ? '▲' : '▼' }})
                        @endif
                    </th>
                    <th>Язык</th>
                    <th>Уровень</th>
                    <th>Формат</th>
                    <th class="sortable" onclick="sortTable('price_total')" title="Сортировать по цене">
                        Цена
                        @if($sort==='price_total')
                            ({{ $dir==='asc' ? '▲' : '▼' }})
                        @endif
                    </th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($courses as $course)
                    @php
                        $unit  = $course->price->unit_price ?? 0;
                        $count = $course->lessons_count ?? 0;
                        $total = $unit * $count;
                        $students = $course->users
                            ->where('pivot.role','student')
                            ->map(fn($s) => $s->first_name.' '.$s->last_name)
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td><button class="expand-btn">＋</button></td>
                        <td>{{ $course->title }}</td>
                        <td>{{ $course->language->name }}</td>
                        <td>{{ $course->level === 'beginner' ? 'Начинающий' : $course->level }}</td>
                        <td>{{ $formats[$course->format] ?? $course->format }}</td>
                        <td>{{ $total }} BYN ({{ $unit }} BYN/урок)</td>
                        <td>
                            {{-- Редактировать --}}
                            <a href="{{ route('admin.courses.edit', $course) }}"
                               class="table-action-btn table-action-edit"
                               title="Редактировать">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            {{-- Участники --}}
                            <a href="{{ route('admin.courses.participants', $course) }}"
                               class="table-action-btn table-action-participants"
                               title="Участники">
                                <i class="bi bi-people-fill"></i>
                            </a>

                            {{-- Удалить --}}
                            <form action="{{ route('admin.courses.destroy', $course) }}"
                                  method="POST"
                                  style="display:inline"
                                  data-delete-form
                                  data-course-title="{{ addslashes($course->title) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="table-action-btn table-action-delete"
                                        title="Удалить">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                    <tr id="details-{{ $course->id }}" class="detail-row">
                        <td colspan="7">
                            <ul class="detail-list">
                                <li><strong>Описание:</strong> {{ $course->description ?? '—' }}</li>
                                <li><strong>Возрастная группа:</strong> {{ $course->age_group ?? '—' }}</li>
                                <li><strong>Уроков:</strong> {{ $course->lessons_count }}</li>
                                <li>
                                    <strong>До:</strong>
                                    {{ $course->duration ? $course->duration->format('d.m.Y') : '—' }}
                                </li>
                                <li>
                                    <strong>Статус:</strong>
                                    @if($course->status==='recruiting') Набор
                                    @elseif($course->status==='not_recruiting') Без набора
                                    @else Завершён
                                    @endif
                                </li>
                                <li>
                                    <strong>Расписание:</strong>
                                    <ul style="padding-left:16px; margin:4px 0;">
                                        @forelse($course->timetables->where('active',true) as $slot)
                                            @php
                                                $label = $slot->date
                                                    ? $slot->date->format('d.m.Y')
                                                    : ucfirst($slot->weekday);
                                                $time  = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                                            @endphp
                                            <li>
                                                {{ $label }} {{ $time }} ({{ $slot->duration }} мин)
                                                @if($slot->type==='free') — бесплатный @endif
                                            </li>
                                        @empty
                                            <li>Слоты не заданы</li>
                                        @endforelse
                                    </ul>
                                </li>
                            </ul>
                            <p><strong>Преподаватели:</strong>
                                {{ $course->users
                                    ->where('pivot.role','teacher')
                                    ->map(fn($t) => $t->first_name.' '.$t->last_name)
                                    ->implode(', ')
                                }}
                            </p>
                            <p><strong>Студенты:</strong> {{ $students ?: '—' }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:20px;">Курсы не найдены</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Success toast
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            @php session()->forget('success'); @endphp
            @endif

            // Expand details
            document.querySelectorAll('.expand-btn').forEach((btn, idx) => {
                btn.addEventListener('click', () => {
                    const detail = document.querySelectorAll('.detail-row')[idx];
                    Swal.fire({
                        title: btn.closest('tr').children[1].textContent,
                        html:
                            detail.querySelector('.detail-list').outerHTML +
                            detail.querySelector('p:nth-of-type(1)').outerHTML +
                            detail.querySelector('p:nth-of-type(2)').outerHTML,
                        width: 600,
                        confirmButtonText: 'Закрыть'
                    });
                });
            });

            // Sorting
            window.sortTable = function(field) {
                const url = new URL(window.location.href);
                const currentSort = url.searchParams.get('sort');
                const currentDir  = url.searchParams.get('dir') || 'asc';
                const newDir = (currentSort===field && currentDir==='asc') ? 'desc' : 'asc';
                url.searchParams.set('sort', field);
                url.searchParams.set('dir', newDir);
                window.location.href = url.toString();
            };

            // Delete confirmation
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        title: `Удалить курс «${form.dataset.courseTitle}»?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true
                    }).then(result => result.isConfirmed && form.submit());
                });
            });
        });
    </script>
@endsection
