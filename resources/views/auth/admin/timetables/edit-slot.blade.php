@extends('layouts.app')

@section('styles')
    <style>
        .edit-slot-container {
            margin-left: 200px;
            padding: 20px;
        }
        .slot-info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }

         .multiselect {
             background: white;
             border-radius: 8px;
             padding: 15px;
         }

        .form-check {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .form-check:last-child {
            border-bottom: none;
        }

        .form-check-input {
            margin-right: 10px;
        }

        .form-check-label {
            cursor: pointer;
            width: 100%;
            display: block;
        }
    </style>
@endsection

    @section('content')
        @include('layouts.left_sidebar_admin')

        <div class="edit-slot-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Редактирование слота</h1>
                <a href="{{ route('admin.timetables.index') }}" class="btn btn-light">
                    ← Назад к расписанию
                </a>
            </div>

            <div class="slot-info-card">

                <h4>Информация о слоте</h4>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Дата:</strong> {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y (l)') }}</p>
                        <p><strong>Время:</strong> {{ $timetable->start_time }}</p>
                        <p><strong>Длительность:</strong> {{ $timetable->duration }} минут</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Курс:</strong> {{ $timetable->course->title ?? $timetable->title }}</p>
{{--                        <p><strong>Текущий преподаватель:</strong>--}}
{{--                            {{ $timetable->teacher->first_name }} {{ $timetable->teacher->last_name }}</p>--}}
                        <p><strong>Текущий преподаватель:</strong>
                            @php
                                // Определяем актуального преподавателя
                                $teacher = $overrideSlot->overrideTeacher ?? $overrideSlot->teacher ?? $timetable->teacher;
                            @endphp
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.timetables.update-slot', ['timetable' => $timetable->id, 'date' => $date]) }}">
                @csrf
                @method('PUT')

                <div class="slot-info-card">
                    {{-- ... существующие поля ... --}}
                </div>

                <div class="mb-4">
                    <h4 class="mb-3">Управление занятием</h4>

                    <!-- Поле для отмены занятия -->
                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cancelled" id="cancelled"
                                   value="1" {{ $overrideSlot && $overrideSlot->cancelled ? 'checked' : '' }}>
                            <label class="form-check-label" for="cancelled">
                                Отменить занятие
                            </label>
                        </div>
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle"></i> При отмене занятия все изменения преподавателя будут сброшены
                        </div>
                    </div>

                    <!-- Поле выбора преподавателя -->
                    <div class="form-group">
                        <label for="teacher_id" class="form-label">Выберите нового преподавателя:</label>
                        <select name="teacher_id" id="teacher_id" class="form-select"
                            {{ $overrideSlot && $overrideSlot->cancelled ? 'disabled' : '' }}>
                            <option value="{{ $timetable->user_id }}"
                                {{ (!$overrideSlot || ($overrideSlot && !$overrideSlot->override_user_id)) ? 'selected' : '' }}>
                                {{ $timetable->teacher->first_name }} {{ $timetable->teacher->last_name }} (основной)
                            </option>
                            @foreach($teachers as $teacher)
                                @if ($teacher->id != $timetable->user_id)
                                    <option value="{{ $teacher->id }}"
                                        {{ $overrideSlot && $overrideSlot->override_user_id == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
{{--                <div class="form-group mb-4">--}}
{{--                    <label class="form-label">Участники занятия:</label>--}}
{{--                    <div class="multiselect" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">--}}
{{--                        @foreach($allStudents as $student)--}}
{{--                            <div class="form-check">--}}
{{--                                <input class="form-check-input" type="checkbox"--}}
{{--                                       name="students[]"--}}
{{--                                       id="student_{{ $student->id }}"--}}
{{--                                       value="{{ $student->id }}"--}}
{{--                                    {{ in_array($student->id, $selectedStudents) ? 'checked' : '' }}>--}}
{{--                                <label class="form-check-label" for="student_{{ $student->id }}">--}}
{{--                                    {{ $student->first_name }} {{ $student->last_name }}--}}
{{--                                </label>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

                <button type="submit" class="btn btn-primary w-100 py-2">Сохранить изменения</button>
            </form>

            <script>
                // Блокировка выбора преподавателя при отмене занятия
                document.getElementById('cancelled').addEventListener('change', function() {
                    const teacherSelect = document.getElementById('teacher_id');
                    teacherSelect.disabled = this.checked;

                    if (this.checked) {
                        // Сбрасываем на основного преподавателя при отмене
                        teacherSelect.value = '{{ $timetable->user_id }}';
                    }
                });
            </script>
    @endsection
