{{-- resources/views/auth/admin/certificates/create.blade.php --}}
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
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
        }
        .cert-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 40px auto;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
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
            background-color: #ffffff;
            transition: border-color 0.2s;
        }
        .form-group select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12'><path fill='%23666' d='M2 4l4 4 4-4z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #615f5f;
        }
        /* Убрали .input-error и .error */
        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
        <h2>Добавить сертификат</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
            @php session()->forget('success'); @endphp
        @endif

        <form id="cert-create-form"
              action="{{ route('admin.certificates.store') }}"
              method="POST"
              class="cert-form"
              enctype="multipart/form-data"
              novalidate>
            @csrf

            <div class="form-group">
                <label for="user_id">Пользователь</label>
                <select name="user_id" id="user_id">
                    <option value="">— выберите пользователя —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->first_name }} {{ $u->last_name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="title">Заголовок сертификата</label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required>
            </div>

            <div class="form-group">
                <label for="file">Файл сертификата (JPG/PNG)</label>
                <input type="file"
                       id="file"
                       name="file"
                       required>
            </div>

            <div class="buttons">
                <a href="{{ route('admin.certificates.index') }}" class="btn-cancel">Отмена</a>
                <button type="submit" class="btn-submit">Сохранить</button>
            </div>
        </form>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Получаем массив «сырых» ошибок
                let raw = @json($errors->all());

                // Заменяем/фильтруем ключ 'validation.uploaded'
                let errs = raw.map(msg => {
                    if (msg === 'validation.uploaded') {
                        // либо возвращаем какую-то нейтральную строку…
                        return 'Не удалось загрузить файл.';
                        // return null;
                    }
                    return msg;
                })
                    // Если вы возвращаете null для нежелательных, отфильтруем их:
                    .filter(Boolean);

                // Показываем оставшиеся
                if (errs.length) {
                    const list = errs.map(m => `<li>${m}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        customClass: { popup: 'swal2-toast' }
                    });
                }
            });
        </script>
    @endif

    {{-- Клиентская валидация --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('cert-create-form');
            form.addEventListener('submit', function(e) {
                const errs = [];
                // Проверяем, что пользователь выбран
                if (!form.user_id.value) {
                    errs.push('Выберите пользователя.');
                }
                // Заголовок не пустой
                const title = form.title.value.trim();
                if (!title) {
                    errs.push('Введите заголовок сертификата.');
                }
                // Файл выбран и имеет разрешённый тип
                const fileInput = form.file;
                if (!fileInput.files.length) {
                    errs.push('Загрузите файл сертификата.');
                } else {
                    const allowed = ['application/pdf', 'image/jpeg', 'image/png'];
                    const file = fileInput.files[0];
                    if (!allowed.includes(file.type)) {
                        errs.push('Файл должен быть JPG или PNG.');
                    }
                }

                if (errs.length > 0) {
                    e.preventDefault();
                    const list = errs.map(m => `<li>${m}</li>`).join('');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Ошибка валидации',
                        html: `<ul style="text-align:left; margin:0">${list}</ul>`,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        customClass: { popup: 'swal2-toast' }
                    });
                }
            });
        });
    </script>
@endsection
