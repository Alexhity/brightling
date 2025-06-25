{{-- resources/views/auth/teacher/lessons/edit.blade.php --}}
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
        form.lesson-form {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .lesson-form .form-group {
            margin-bottom: 16px;
        }
        .lesson-form label {
            display: block;
            font-family: 'Montserrat SemiBold', sans-serif;
            margin-bottom: 6px;
            color: #333;
        }
        .lesson-form input[type="text"],
        .lesson-form input[type="url"],
        .lesson-form select,
        .lesson-form textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
        }
        .lesson-form .disabled {
            background-color: #f9f9f9;
            cursor: not-allowed;
        }
        .lesson-form table.students {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 14px;
        }
        .students th,
        .students td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .btn-submit {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background-color: #45A049;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="admin-content-wrapper">
        <h2>Редактирование урока</h2>

        <form action="{{ route('teacher.lessons.update', $lesson) }}"
              method="POST"
              class="lesson-form"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Дата и время --}}
            <div class="form-group">
                <label>Дата и время:</label>
                <input type="text" class="disabled" value="{{ $lesson->date->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($lesson->time)->format('H:i') }}" disabled>
            </div>

            {{-- Тип урока --}}
            <div class="form-group">
                <label>Тип урока:</label>
                <input type="text" class="disabled" value="{{ ucfirst($lesson->type) }}" disabled>
            </div>

            {{-- Курс --}}
            <div class="form-group">
                <label>Курс:</label>
                <input type="text" class="disabled" value="{{ $lesson->course->title }}" disabled>
            </div>

            {{-- Ссылка на урок --}}
            <div class="form-group">
                <label for="zoom_link">Ссылка на урок (Zoom, Meet и т.д.):</label>
                <input type="url" name="zoom_link" id="zoom_link" value="{{ old('zoom_link', $lesson->zoom_link) }}">
            </div>

            {{-- Материал урока --}}
            <div class="form-group">
                <label for="material_path">Материал (файл):</label>

                @if($lesson->material_path)
                    <p>
                        Текущий файл:
                        <a href="{{ asset($lesson->material_path) }}" target="_blank">
                            {{ \Illuminate\Support\Str::afterLast($lesson->material_path, '/') }}
                        </a>
                    </p>

                    {{-- Чекбокс удаления материала --}}
                    <div>
                        <label>
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

            @foreach($lesson->users as $user)
                <tr>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>
                        <select name="statuses[{{ $user->id }}]">
                            <option value="present" {{ $user->pivot->status=='present' ? 'selected':'' }}>
                                Присутствовал
                            </option>
                            <option value="absent" {{ $user->pivot->status=='absent' ? 'selected':'' }}>
                                Отсутствовал
                            </option>
                        </select>
                    </td>
                    <td>
                        <input type="number"
                               name="marks[{{ $user->id }}]"
                               value="{{ $user->pivot->mark }}"
                               min="0" max="100"
                            {{ $user->pivot->status!=='present' ? 'disabled' : '' }}>
                    </td>
                </tr>
            @endforeach



            <button type="submit" class="btn-submit">Сохранить изменения</button>
        </form>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
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
                @endif

                @if($errors->any())
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
                        customClass: { popup: 'swal2-toast' }
                    });
                });
                @endif
            });
        </script>
    @endsection
@endsection
