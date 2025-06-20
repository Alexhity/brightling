@extends('layouts.app')

@section('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .conditional-field {
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #e2e8f0;
        }
        .btn-submit {
            background: #3b82f6;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #2563eb;
        }
        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            margin-left: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="container py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Создать новый слот</h1>
            <a href="{{ route('admin.timetables.index') }}" class="btn-secondary">
                Назад к расписанию
            </a>
        </div>

        <form action="{{ route('admin.timetables.store') }}" method="POST">
            @csrf

            <!-- Тип слота -->
            <div class="form-section">
                <div class="form-title">Тип слота</div>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="slot_type" value="single" checked id="slot_type_single">
                        <span>Разовый слот</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="slot_type" value="recurring" id="slot_type_recurring">
                        <span>Регулярное занятие</span>
                    </label>
                </div>

                <!-- Поля для разового слота -->
                <div id="single_fields" class="conditional-field">
                    <div class="form-group">
                        <label for="date" class="form-label">Дата</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                </div>

                <!-- Поля для регулярного слота -->
                <div id="recurring_fields" class="conditional-field" style="display:none">
                    <div class="form-group">
                        <label for="weekday" class="form-label">День недели</label>
                        <select name="weekday" id="weekday" class="form-control">
                            <option value="">Выберите день</option>
                            @foreach($weekdays as $key => $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ends_at" class="form-label">Повторять до</label>
                        <input type="date" name="ends_at" id="ends_at" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Общие параметры -->
            <div class="form-section">
                <div class="form-title">Параметры занятия</div>

                <div class="form-group">
                    <label for="start_time" class="form-label">Время начала</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                           value="15:00" required>
                </div>

                <div class="form-group">
                    <label for="duration" class="form-label">Длительность (минут)</label>
                    <input type="number" name="duration" id="duration" class="form-control"
                           min="15" max="240" value="60" required>
                    <small class="text-gray-500 text-sm">Для тестовых уроков длительность автоматически 15 мин</small>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">Тип занятия</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Выберите тип</option>
                        <option value="group">Групповое</option>
                        <option value="individual">Индивидуальное</option>
                        <option value="test">Тестовый урок</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="teacher_id" class="form-label">Преподаватель</label>
                    <select name="teacher_id" id="teacher_id" class="form-control" required>
                        <option value="">Выберите преподавателя</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="course_id" class="form-label">Привязать к курсу</label>
                    <select name="course_id" id="course_id" class="form-control">
                        <option value="">Без привязки к курсу</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="title_field">
                    <label for="title" class="form-label">Название занятия</label>
                    <input type="text" name="title" id="title" class="form-control"
                           placeholder="Введите название занятия">
                </div>

                <div class="form-group">
                    <label class="flex items-center">
                        <input type="checkbox" name="active" id="active" value="1" checked>
                        <span class="ml-2">Активный слот</span>
                    </label>
                </div>

                <div class="form-group" id="public_field" style="display:none">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_public" id="is_public" value="1" checked>
                        <span class="ml-2">Публичный слот (виден для записи)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-submit">Создать слот</button>
                <a href="{{ route('admin.timetables.index') }}" class="btn-submit btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Элементы формы
            const slotTypeSingle = document.getElementById('slot_type_single');
            const slotTypeRecurring = document.getElementById('slot_type_recurring');
            const singleFields = document.getElementById('single_fields');
            const recurringFields = document.getElementById('recurring_fields');
            const courseSelect = document.getElementById('course_id');
            const titleField = document.getElementById('title_field');
            const publicField = document.getElementById('public_field');
            const lessonType = document.getElementById('type');
            const durationField = document.getElementById('duration');

            // Переключение между разовым и регулярным слотом
            function toggleSlotTypeFields() {
                if (slotTypeSingle.checked) {
                    singleFields.style.display = 'block';
                    recurringFields.style.display = 'none';
                    document.getElementById('date').required = true;
                    document.getElementById('weekday').required = false;
                } else {
                    singleFields.style.display = 'none';
                    recurringFields.style.display = 'block';
                    document.getElementById('date').required = false;
                    document.getElementById('weekday').required = true;
                }
            }

            // Обработка привязки к курсу
            function toggleTitleField() {
                titleField.style.display = courseSelect.value ? 'none' : 'block';
            }

            // Обработка типа занятия
            function handleLessonType() {
                const isTestLesson = lessonType.value === 'test';

                if (isTestLesson) {
                    durationField.value = 15;
                    durationField.readOnly = true;
                    publicField.style.display = 'block';
                    document.getElementById('active').checked = true;
                } else {
                    durationField.readOnly = false;
                    publicField.style.display = 'none';
                }
            }

            // Инициализация
            toggleSlotTypeFields();
            toggleTitleField();
            handleLessonType();

            // Слушатели событий
            slotTypeSingle.addEventListener('change', toggleSlotTypeFields);
            slotTypeRecurring.addEventListener('change', toggleSlotTypeFields);
            courseSelect.addEventListener('change', toggleTitleField);
            lessonType.addEventListener('change', handleLessonType);
        });
    </script>
@endsection
