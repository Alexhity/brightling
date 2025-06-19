@extends('layouts.app')

@section('title', 'Редактировать уровень курса')

@section('styles')
    <style>
        .teacher-content-wrapper {
            margin-left: 200px;
            padding: 30px;
            font-family: 'Montserrat Medium', sans-serif;
            display: flex;
            justify-content: center;
        }
        .level-form {
            background: #fff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 400px;
        }
        .level-form h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display:block; margin-bottom:6px; color:#333; }
        .form-group select {
            width:100%; padding:8px; font-size:14px;
            border:1px solid #ccc; border-radius:5px;
            transition:border-color .2s;
        }
        .form-group select:focus { border-color:#615f5f; }
        .btn-submit {
            background:#beffe6; color:#333;
            padding:10px 20px; border:none; border-radius:7px;
            width:100%; font-family:'Montserrat Medium',sans-serif;
            cursor:pointer; transition:background .3s;
        }
        .btn-submit:hover { background:#93edca; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_teacher')

    <div class="teacher-content-wrapper">
        <form action="{{ route('teacher.courses.updateLevel', $course) }}"
              method="POST"
              class="level-form"
              novalidate>
            @csrf
            @method('PATCH')

            <h2>Уровень курса<br>«{{ $course->title }}»</h2>

            <div class="form-group">
                <label for="level">Уровень</label>
                <select id="level" name="level"
                        class="@error('level') input-error @enderror" required>
                    <option value="">— выберите —</option>
                    @foreach($levels as $key=>$label)
                        <option value="{{ $key }}"
                            {{ old('level', $course->level) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('level')<div class="error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">Сохранить</button>
        </form>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        </script>
    @endif
@endsection
