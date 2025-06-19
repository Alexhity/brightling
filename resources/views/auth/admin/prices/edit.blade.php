{{-- resources/views/auth/admin/prices/edit.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            font-family: 'Montserrat Medium', sans-serif;
            width: calc(100% - 200px);
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
        }
        .course-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 40px auto;
        }
        .form-group {
            position: relative;
            margin-bottom: 6px;
            padding-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            font-family: 'Montserrat Medium', sans-serif;
            border: 1px solid #cccccc;
            border-radius: 5px;
            transition: border-color 0.2s;
            background-color: #ffffff;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #615f5f;
        }
        .input-error {
            border-color: #ff4c4c !important;
        }
        .error {
            position: absolute;
            bottom: 0;
            left: 0;
            font-size: 13px;
            color: #ff4c4c;
            font-family: 'Montserrat Medium',sans-serif;
        }
        .buttons {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-submit {
            background-color: #beffe6;
            color: #333333;
            padding: 10px 20px;
            font-size: 16px;
            font-family: 'Montserrat Medium', sans-serif;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .btn-submit:hover {
            background-color: #93edca;
        }
        .btn-cancel {
            background-color: #f0f0f0;
            color: #333333;
            padding: 10px 20px;
            font-size: 16px;
            font-family: 'Montserrat Medium', sans-serif;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-cancel:hover {
            background-color: #d9d9d9;
        }
        .alert-success {
            background-color: #e6f7e6;
            color: #2e7d32;
            padding: 12px 20px;
            margin-bottom: 30px;
            border: 1px solid #c8e6c9;
            border-radius: 7px;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Редактировать тариф</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.prices.update', $price) }}" method="POST" class="course-form" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Название</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $price->name) }}"
                       required class="{{ $errors->has('name') ? 'input-error' : '' }}">
                @if($errors->has('name'))
                    @php $msg = $errors->first('name'); @endphp
                    <div class="error">
                        {{ str_contains($msg, 'unique') ? 'Тариф с данным названием уже существует.' : 'Пожалуйста, введите название тарифа.' }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="lesson_duration">Длительность (мин)</label>
                <input id="lesson_duration" name="lesson_duration" type="number"
                       value="{{ old('lesson_duration', $price->lesson_duration) }}"
                       required class="{{ $errors->has('lesson_duration') ? 'input-error' : '' }}">
                @if($errors->has('lesson_duration'))
                    <div class="error">Пожалуйста, укажите длительность урока.</div>
                @endif
            </div>

            <div class="form-group">
                <label for="unit_price">Цена за урок, ₽</label>
                <input id="unit_price" name="unit_price" type="number" step="0.01"
                       value="{{ old('unit_price', $price->unit_price) }}"
                       required class="{{ $errors->has('unit_price') ? 'input-error' : '' }}">
                @if($errors->has('unit_price'))
                    <div class="error">Пожалуйста, введите цену за урок.</div>
                @endif
            </div>

            <div class="form-group">
                <label for="format">Формат</label>
                <select id="format" name="format" required class="{{ $errors->has('format') ? 'input-error' : '' }}">
                    <option value="individual" {{ old('format', $price->format)=='individual'?'selected':'' }}>Индивидуальный</option>
                    <option value="group" {{ old('format', $price->format)=='group'?'selected':'' }}>Групповой</option>
                </select>
                @if($errors->has('format'))
                    <div class="error">Пожалуйста, выберите формат занятия.</div>
                @endif
            </div>

            <div class="buttons">
                <button type="submit" class="btn-submit">Сохранить</button>
                <a href="{{ route('admin.prices.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
@endsection
