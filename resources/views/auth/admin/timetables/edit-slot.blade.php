@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            max-width: 500px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .btn-submit {
            background: #e6e2f8;
            color: #2B2D42;
            padding: 10px 20px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-family: 'Montserrat Medium', sans-serif;
            transition: background .3s;
        }
        .btn-submit:hover { background: #c4b6f3; }
        .btn-cancel {
            margin-left: 10px;
            font-family: 'Montserrat Medium', sans-serif;
            color: #555;
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Редактировать слот</h2>

        <form action="{{ route('admin.timetables.updateSlot', ['timetable'=>$timetable->id, 'date'=>$date]) }}"
              method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label>Применить изменения:</label>
                <div>
                    <label>
                        <input type="radio" name="apply_to" value="single" checked>
                        Только на этот урок ({{ $date }})
                    </label><br>
                    <label>
                        <input type="radio" name="apply_to" value="series">
                        На всю серию (каждую {{ $timetable->weekday }})
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="active">Активен?</label>
                <select name="active" id="active">
                    <option value="1" {{ $timetable->active ? 'selected':'' }}>Да</option>
                    <option value="0" {{ !$timetable->active ? 'selected':'' }}>Нет</option>
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">Преподаватель</label>
                <select name="user_id" id="user_id">
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $timetable->user_id == $t->id ? 'selected':'' }}>
                            {{ $t->first_name }} {{ $t->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit">Сохранить</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
