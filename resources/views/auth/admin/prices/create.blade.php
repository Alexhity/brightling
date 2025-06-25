@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin: 30px 0;
            text-align: center;
        }
        .course-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto 40px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 4px; }
        .form-group input,
        .form-group select {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;
            font-family: 'Montserrat Medium', sans-serif; font-size: 14px;
            transition: border-color .2s;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            font-size: 14px; color: #999; opacity: 1;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none; border-color: #615f5f;
        }
        .buttons { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }
        .btn-submit { background: #beffe6; padding: 10px 20px; border: none; border-radius: 7px; cursor: pointer; }
        .btn-submit:hover { background: #93edca; }
        .btn-cancel { background: #f0f0f0; padding: 10px 20px; border-radius: 7px; text-decoration: none; }
        .required::after { content: " *"; color: #ff4c4c; }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')
    <div class="admin-content-wrapper">
        <h2>Создать тариф</h2>

        {{-- Validation errors (5s toast) --}}
        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const list = @json($errors->all()).map(e => `<li>${e}</li>`).join('');
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false, timer: 5000, timerProgressBar: true,
                    });
                });
            </script>
        @endif

        {{-- Success message (3s toast) --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false, timer: 3000, timerProgressBar: true,
                    });
                });
            </script>
        @endif

        <form action="{{ route('admin.prices.store') }}" method="POST" class="course-form" id="price-create-form" novalidate>
            @csrf

            <div class="form-group">
                <label for="name" class="required">Название тарифа</label>
                <input id="name" name="name" type="text" placeholder="Введите название"
                       value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="lesson_duration" class="required">Длительность (мин)</label>
                <input id="lesson_duration" name="lesson_duration" type="number" placeholder="Минуты"
                       value="{{ old('lesson_duration') }}" required>
            </div>

            <div class="form-group">
                <label for="unit_price" class="required">Цена за урок, BYN</label>
                <input id="unit_price" name="unit_price" type="number" step="0.01" placeholder="0.00"
                       value="{{ old('unit_price') }}" required>
            </div>

            <div class="form-group">
                <label for="format" class="required">Формат</label>
                <select id="format" name="format" required>
                    <option value="" disabled {{ old('format')?'':'selected' }}>Выберите формат</option>
                    <option value="individual" {{ old('format')=='individual'?'selected':'' }}>Индивидуальный</option>
                    <option value="group"      {{ old('format')=='group'     ?'selected':'' }}>Групповой</option>
                </select>
            </div>

            <div class="buttons">
                <button type="submit" class="btn-submit">Создать</button>
                <a href="{{ route('admin.prices.index') }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('price-create-form');

            form.addEventListener('submit', e => {
                const errs = [],
                    dur = form.lesson_duration.value,
                    price = form.unit_price.value,
                    fmt = form.format.value,
                    name = form.name.value.trim();
                if(!name) errs.push('Введите название тарифа.');
                if(!dur || dur <= 0) errs.push('Укажите корректную длительность.');
                if(!price || price <= 0) errs.push('Введите корректную цену.');
                if(!fmt) errs.push('Выберите формат.');
                if(errs.length){
                    e.preventDefault();
                    const list = errs.map(e=>`<li>${e}</li>`).join('');
                    Swal.fire({
                        toast:true,position:'top-end',icon:'error',
                        title:'Ошибка валидации',
                        html:`<ul style="text-align:left">${list}</ul>`,
                        showConfirmButton:false,timer:5000,timerProgressBar:true
                    });
                }
            });
        });
    </script>
@endsection
