@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            padding: 20px;
            max-width: 700px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 28px;
            margin-bottom: 20px;
        }
        /* Стили табов */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tabs button {
            flex: 1;
            padding: 10px 0;
            background: #f0f0f0;
            border: none;
            border-radius: 5px 5px 0 0;
            font-family: 'Montserrat Medium', sans-serif;
            cursor: pointer;
            transition: background .2s;
        }
        .tabs button.active {
            background: #e6e2f8;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .tab-content {
            border: 1px solid #e0e0e0;
            border-top: none;
            padding: 20px;
            background: #fff;
            border-radius: 0 5px 5px 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .form-group select,
        .form-group input[type="file"] {
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

        .alert-success {
            background: #e6f7e6;
            color: #2e7d32;
            padding: 12px 20px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
            border-radius: 7px;
            font-family: 'Montserrat Medium', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <h2>Выдача сертификатов</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="tabs">
            <button type="button" class="tab-btn active" data-tab="individual">Индивидуально</button>
            <button type="button" class="tab-btn" data-tab="bulk">По уровню</button>
        </div>

        {{-- ИНДИВИДУАЛЬНАЯ выдача --}}
        <div id="individual" class="tab-content">
            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="mode" value="individual">

                <div class="form-group">
                    <label for="user_id">Пользователь</label>
                    <select name="user_id" id="user_id" required>
                        <option value="">— выберите студента —</option>
                        @foreach($students as $stu)
                            <option value="{{ $stu->id }}">{{ $stu->first_name }} {{ $stu->last_name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="language_id_ind">Язык</label>
                    <select name="language_id" id="language_id_ind" required>
                        <option value="">— выберите язык —</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                    @error('language_id')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="level_ind">Уровень</label>
                    <select name="level" id="level_ind" required>
                        <option value="">— выберите уровень —</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                    @error('level')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="file_ind">Файл сертификата</label>
                    <input type="file" name="file" id="file_ind" accept=".pdf,.jpg,.png" required>
                    @error('file')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-submit">Выдать сертификат</button>
            </form>
        </div>

        {{-- МАССОВАЯ выдача --}}
        <div id="bulk" class="tab-content" style="display:none;">
            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="mode" value="bulk">

                <div class="form-group">
                    <label for="language_id_bulk">Язык</label>
                    <select name="language_id" id="language_id_bulk" required>
                        <option value="">— выберите язык —</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                    @error('language_id')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="level_bulk">Уровень</label>
                    <select name="level" id="level_bulk" required>
                        <option value="">— выберите уровень —</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                    @error('level')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="file_bulk">Файл сертификата</label>
                    <input type="file" name="file" id="file_bulk" accept=".pdf,.jpg,.png" required>
                    @error('file')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-submit">Выдать всем</button>
            </form>
        </div>
    </div><script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs   = document.querySelectorAll('.tab-btn');
            const panes  = document.querySelectorAll('.tab-content');

            tabs.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Активный таб
                    tabs.forEach(t => t.classList.remove('active'));
                    btn.classList.add('active');

                    // Показать нужную панель
                    panes.forEach(p => p.style.display = 'none');
                    document.getElementById(btn.dataset.tab).style.display = 'block';
                });
            });
        });
    </script>
@endsection

