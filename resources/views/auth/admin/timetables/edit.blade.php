@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            max-width: 700px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #2B2D42;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #4f46e5;
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
        .btn-submit {
            background: #4f46e5;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Montserrat SemiBold', sans-serif;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #4338ca;
        }
        .btn-cancel {
            margin-left: 15px;
            background: #f3f4f6;
            color: #4b5563;
        }
        .conditional-field {
            display: none;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            margin-top: 10px;
        }
        .required::after {
            content: ' *';
            color: #ef4444;
        }
        .form-text {
            display: block;
            margin-top: 5px;
            font-size: 0.875rem;
            color: #6b7280;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h1 class="text-2xl font-bold mb-6">Создать новый слот</h1>

        <form action="{{ route('admin.timetables.store') }}" method="POST">
            @csrf

            <!-- Тип слота -->
            <div class="form-section">
                <h2 class="text-xl font-semibold mb-4">Тип слота</h2>
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
                        <label for="date" class="required">Дата</label>
                        <input type="date" name="date" id="date" class="form-control"
                               value="{{ old('date') }}" required>
                    </div>
                </div>

                <!-- Поля для регулярного слота -->
                <div id="recurring_fields" class="conditional-field">
                    <div class="form-group">
                        <label for="weekday" class="required">День недели</label>
                        <select name="weekday" id="weekday" class="form-control">
                            <option value="">Выберите день</option>
                            @foreach($weekdays as $day)
                                <option value="{{ $day }}" {{ old('weekday') == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ends_at">Повторять до</label>
                        <input type="date" name="ends_at" id="ends_at" class="form-control"
                               value="{{ old('ends_at') }}">
                    </div>
                </div>
            </div>

            <!-- Общие параметры -->
            <div class="form-section">
                <h2 class="text-xl font-semibold mb-4">Общие параметры</h2>

                <div class="form-group">
                    <label for="start_time" class="required">Время начала</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                           value="{{ old('start_time', '15:00') }}" required>
                </div>

                <div class="form-group">
                    <label for="duration" class="required">Длительность (минут)</label>
                    <input type="number" name="duration" id="duration" class="form-control"
                           min="15" max="240" value="{{ old('duration', 60) }}" required>
                    <small class="form-text">
                        Для тестовых уроков длительность автоматически устанавливается в 15 минут
                    </small>
                </div>

                <div class="form-group">
                    <label for="lesson_type" class="required">Тип занятия</label>
                    <select name="lesson_type" id="lesson_type" class="form-control" required>
                        <option value="">Выберите тип</option>
                        <option value="group" {{ old('lesson_type') == 'group' ? 'selected' : '' }}>Групповое</option>
                        <option value="individual" {{ old('lesson_type') == 'individual' ? 'selected' : '' }}>Индивидуальное</option>
                        <option value="test" {{ old('lesson_type') == 'test' ? 'selected' : '' }}>Тестовый урок</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="teacher_id" class="required">Преподаватель</label>
                    <select name="teacher_id" id="teacher_id" class="form-control" required>
                        <option value="">Выберите преподавателя</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="course_id">Привязать к курсу</label>
                    <select name="course_id" id="course_id" class="form-control">
                        <option value="">Без привязки к курсу</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="title_field">
                    <label for="title" class="required">Название занятия</label>
                    <input type="text" name="title" id="title" class="form-control"
                           value="{{ old('title') }}" placeholder="Введите название">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="active" id="active" value="1"
                            {{ old('active', true) ? 'checked' : '' }}>
                        <span>Активный слот</span>
                    </label>
                </div>

                <div class="form-group" id="public_field" style="display: none;">
                    <label>
                        <input type="checkbox" name="is_public" id="is_public" value="1"
                            {{ old('is_public', true) ? 'checked' : '' }}>
                        <span>Публичный слот (виден на сайте для записи)</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
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
            const lessonType = document.getElementById('lesson_type');
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

                // Для тестовых уроков
                if (isTestLesson) {
                    // Фиксируем длительность 15 мин
                    durationField.value = 15;
                    durationField.readOnly = true;

                    // Показываем опцию публичности
                    publicField.style.display = 'block';

                    // Автоматически включаем активность
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
