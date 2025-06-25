@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .form-section {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .form-header {
            margin-bottom: 20px;
        }
        .btn-add-slot {
            background: #6c5ce7;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }
        .slots-table {
            width: 100%;
            border-collapse: collapse;
        }
        .slots-table th, .slots-table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        .slots-table th {
            background: #f7f7f7;
            font-weight: 600;
        }
        .btn-remove-slot {
            background: #ff7675;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-submit {
            background: #00b894;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-cancel {
            background: #dfe6e9;
            color: #2d3436;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="form-section">
            <div class="form-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Создание регулярных слотов</h2>
                <button type="button" id="btn-add-slot" class="btn-add-slot">
                    <i class="fas fa-plus"></i> Добавить слот
                </button>
            </div>

            <form method="POST" action="{{ route('admin.timetables.store-slot') }}">
                @csrf
                <div class="slots-container">
                    <table id="slots-table" class="slots-table">
                        <thead>
                        <tr>
                            <th>День недели *</th>
                            <th>Время начала *</th>
                            <th>Длительность (мин) *</th>
                            <th>Тип занятия *</th>
                            <th>Преподаватель *</th>
                            <th>Дата окончания</th> <!-- Новая колонка -->
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Начальная строка -->
                        <tr data-index="0">
                            <td>
                                <select name="timetables[0][weekday]" required>
                                    @foreach($weekdays as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="time" name="timetables[0][start_time]" required>
                            </td>
                            <td>
                                <input type="number" name="timetables[0][duration]"
                                       min="30" max="240" value="60" required>
                            </td>
                            <td>
                                <select name="timetables[0][type]" required>
                                    @foreach($types as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="timetables[0][user_id]" required>
                                    <option value="">Выберите преподавателя</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">
                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Добавленное поле для даты окончания -->
                            <td>
                                <input type="date" name="timetables[0][ends_at]"
                                       min="{{ now()->format('Y-m-d') }}">
                            </td>
                            <td>
                                <button type="button" class="btn-remove-slot">×</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Сохранить слоты</button>
                    <a href="{{ route('admin.timetables.index') }}" class="btn-cancel">Назад к расписанию</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let idx = 1;
            const weekdays = @json($weekdays);
            const types = @json($types);
            const teachers = @json($teachers);

            // Функция для генерации строки расписания
            function generateSlotRow(index) {
                const row = document.createElement('tr');
                row.dataset.index = index;

                let weekdaysOptions = '';
                weekdays.forEach(day => {
                    weekdaysOptions += `<option value="${day}">${day}</option>`;
                });

                let typesOptions = '';
                for (const [key, value] of Object.entries(types)) {
                    typesOptions += `<option value="${key}">${value}</option>`;
                }

                let teachersOptions = '<option value="">Выберите преподавателя</option>';
                teachers.forEach(teacher => {
                    teachersOptions += `
                        <option value="${teacher.id}">
                            ${teacher.first_name} ${teacher.last_name}
                        </option>
                    `;
                });

                row.innerHTML = `
                    <td>
                        <select name="timetables[${index}][weekday]" required>
                            ${weekdaysOptions}
                        </select>
                    </td>
                    <td>
                        <input type="time" name="timetables[${index}][start_time]" required>
                    </td>
                    <td>
                        <input type="number" name="timetables[${index}][duration]"
                               min="30" max="240" value="60" required>
                    </td>
                    <td>
                        <select name="timetables[${index}][type]" required>
                            ${typesOptions}
                        </select>
                    </td>
                    <td>
                        <select name="timetables[${index}][user_id]" required>
                            ${teachersOptions}
                        </select>
                    </td>
                    <td>
                        <input type="date" name="timetables[${index}][ends_at]">
                    </td>
                    <td>
                        <button type="button" class="btn-remove-slot">×</button>
                    </td>


                `;
                return row;
            }

            // Добавление нового слота
            document.getElementById('btn-add-slot').addEventListener('click', function() {
                const tbody = document.querySelector('#slots-table tbody');
                const newRow = generateSlotRow(idx);
                tbody.appendChild(newRow);
                idx++;
            });

            // Удаление слота
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-slot')) {
                    const row = e.target.closest('tr');
                    // Не позволяем удалить последнюю строку
                    if (document.querySelectorAll('#slots-table tbody tr').length > 1) {
                        row.remove();
                    }
                }
            });
        });
    </script>
@endsection
