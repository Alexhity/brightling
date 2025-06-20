@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat-Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            /* убираем width: 100%; */
            /* делаем так, чтобы он равнялся контейнеру */
            width: 100%;  /* при условии, что родитель .admin-content-wrapper равен таблице */
        }
        .filter-bar input,
        .filter-bar select {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Montserrat-Medium', sans-serif;

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
            font-family: 'Montserrat-Medium', sans-serif;
            text-align: left;
        }
        .courses th {
            background: #fef6e0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
            font-size: 16px;
        }
        .courses td {
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
            font-size: 14px;
        }
        .toggle-btn {
            cursor: pointer;
            font-size: 16px;
            width: 24px;
            text-align: center;
        }
        .detail-row {
            display: none;
            background: #fafafa;
        }
        .detail-row td {
            padding: 16px 20px;
            font-size: 16px;
        }
        .detail-list {
            margin: 0;
            padding-left: 16px;
            font-family: 'Montserrat-Medium', sans-serif;
        }
        .participants-columns {
            display: flex;
            gap: 40px;
            margin-top: 12px;
        }
        .participants-columns div {
            flex: 1;
        }
        .participants-columns span {
            display: block;
            margin-bottom: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        .price-cell {
            font-family: 'Montserrat Medium', sans-serif;
        }
        .price-total {
            font-weight: bold;
            margin-right: 6px;
        }
        .price-unit {
            color: #555;
        }
        .sortable {
            cursor: pointer;
        }
        .sortable:hover {
            text-decoration: underline;
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
            padding: 10px 16px;           /* новое значение */
            background-color: #e6e2f8;    /* новый фон */
            color: black;                 /* новый цвет текста */
            text-decoration: none;
            border-radius: 7px;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .btn-create:hover {
            transform: scale(1.05);
        }
        .btn-create .icon-plus {
            font-size: 20px;
            margin-right: 6px;
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
                    <option value="completed" {{ $status=='completed'?'selected':'' }}>Завершен</option>
                </select>
                <select name="language">
                    <option value="">Все языки</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ $language==$lang->id?'selected':'' }}>{{ $lang->name }}</option>
                    @endforeach
                </select>
                <select name="level">
                    <option value="">Все уровни</option>
                    @foreach($levels as $key=>$label)
                        <option value="{{ $key }}" {{ $level==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="format">
                    <option value="">Все форматы</option>
                    @foreach($formats as $key=>$label)
                        <option value="{{ $key }}" {{ $format==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit">Применить</button>
            </div>
        </form>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="courses">
            <thead>
            <tr>
                <th></th>
                <th class="sortable" onclick="sortTable('title')" title="Сортировать по названию">
                    Название @if($sort=='title') (@if($dir=='asc')▲@else▼@endif) @endif
                </th>
                <th>Язык</th>
                <th>Уровень</th>
                <th>Формат</th>
                <th class="sortable" onclick="sortTable('price_total')" title="Сортировать по цене">
                    Цена @if($sort=='price_total') (@if($dir=='asc')▲@else▼@endif) @endif
                </th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($courses as $course)
                <tr>
                    <td class="toggle-btn" data-target="details-{{ $course->id }}">+</td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->language->name }}</td>
                    <td>{{ ucfirst($course->level) }}</td>
                    <td>{{ $formats[$course->format] ?? $course->format }}</td>
                    <td class="price-cell">
                        @php
                            $unit = $course->price->unit_price ?? 0;
                            $count = $course->lessons_count ?? 0;
                            $total = $unit * $count;
                        @endphp
                        <span class="price-total">{{ $total }} ₽</span>
                        <span class="price-unit">({{ $unit }} ₽/урок)</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.courses.edit', $course) }}">✏️</a>
                        <a href="{{ route('admin.courses.participants', $course) }}">👥</a>
                        <form action="{{ route('admin.courses.destroy', $course) }}"
                        method="POST"
                        style="display:inline"
                        onsubmit="return confirm('Вы уверены, что хотите удалить курс «{{ addslashes($course->title) }}»?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:#c00;"
                                title="Удалить курс">
                            🗑️
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
                            <li><strong>До:</strong> {{ $course->duration ? $course->duration->format('d.m.Y') : '—' }}</li>
                            <li><strong>Статус:</strong> @if($course->status==='recruiting') Набор @elseif($course->status==='not_recruiting') Без набора @else Завершен @endif</li>
                            <li>
                                <strong>Расписание:</strong>
                                <ul style="padding-left: 16px; margin: 4px 0;">
                                    @forelse($course->timetables->where('active', true) as $slot)
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
                        <div class="participants-columns">
                            <div>
                                <strong>Преподаватели:</strong>
                                @foreach($course->users->where('pivot.role','teacher') as $t)
                                    <span>{{ $t->first_name }} {{ $t->last_name }}</span>
                                @endforeach
                            </div>
                            <div>
                                <strong>Студенты:</strong>
                                @foreach($course->users->where('pivot.role','student') as $s)
                                    <span>{{ $s->first_name }} {{ $s->last_name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:20px;">Курсы не найдены</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $courses->links() }}</div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var row = document.getElementById(this.dataset.target);
                    if (row.style.display === 'table-row') {
                        row.style.display = 'none'; this.textContent = '+';
                    } else {
                        row.style.display = 'table-row'; this.textContent = '–';
                    }
                });
            });
        });
        // Функция сортировки
        function sortTable(field) {
            var url = new URL(window.location.href);
            var currentSort = url.searchParams.get('sort');
            var currentDir = url.searchParams.get('dir') || 'asc';
            var newDir = 'asc';
            if (currentSort === field && currentDir === 'asc') newDir = 'desc';
            url.searchParams.set('sort', field);
            url.searchParams.set('dir', newDir);
            window.location.href = url.toString();
        }
    </script>
@endsection

