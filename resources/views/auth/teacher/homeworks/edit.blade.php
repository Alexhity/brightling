@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 20px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn-back {
            padding: 8px 14px;
            background-color: #e6e2f8;
            border-radius: 7px;
            text-decoration: none;
            color: #000;
            transition: background 0.3s;
        }
        .btn-back:hover { background-color: #c4b6f3; }

        .homework-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Montserrat SemiBold', sans-serif;
        }
        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Montserrat Medium', sans-serif;
        }
        .btn-submit {
            padding: 10px 18px;
            background-color: #8986FF;
            color: #fff;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover { background-color: #6f6cff; }

        .error {
            color: #c00;
            font-size: 14px;
            margin-top: 4px;
        }
        /* Кнопка "Перейти" */
        #btnVisitLink {
            background: #e6e2f8;
            color: #000;
        }
        #btnVisitLink:hover {
            background: #c4b6f3;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <div class="header-row">
            <h2>Редактировать задание</h2>
            <a href="{{ route('teacher.homeworks.index') }}" class="btn-back">← Назад</a>
        </div>

        <form action="{{ route('teacher.homeworks.update', $homework) }}"
              method="POST"
              class="homework-form">
            @csrf
            @method('PUT')

            {{-- Описание --}}
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea name="description"
                          id="description"
                          rows="4">{{ old('description', $homework->description) }}</textarea>
                @error('description')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Дедлайн --}}
            <div class="form-group">
                <label for="deadline">Дедлайн</label>
                <input type="datetime-local"
                       name="deadline"
                       id="deadline"
                       value="{{ old('deadline', $homework->deadline->format('Y-m-d\TH:i')) }}">
                @error('deadline')<div class="error">{{ $message }}</div>@enderror
            </div>

            {{-- Ссылка + кнопка "Перейти" --}}
            <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
                <div style="flex:1;">
                    <label for="link">Ссылка</label>
                    <input type="text"
                           name="link"
                           id="link"
                           value="{{ old('link', $homework->link) }}"
                           placeholder="https://example.com">
                    @error('link')<div class="error">{{ $message }}</div>@enderror
                </div>
                <button type="button"
                        id="btnVisitLink"
                        class="btn-submit"
                        style="margin-top: 24px; flex: 0 0 auto;">
                    Перейти
                </button>
            </div>

            <button type="submit" class="btn-submit">Сохранить</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Проверка URL
        function isValidUrl(u) {
            try { new URL(u); return true; }
            catch { return false; }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnVisitLink').addEventListener('click', () => {
                const url = document.getElementById('link').value.trim();
                if (!url) {
                    Swal.fire('Введите ссылку', '', 'warning');
                    return;
                }
                if (!isValidUrl(url)) {
                    Swal.fire('Неверный формат URL', '', 'error');
                    return;
                }
                window.open(url, '_blank');
            });

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
@endpush
