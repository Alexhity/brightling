{{-- resources/views/auth/student/reviews.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .student-content-wrapper {
            margin-left: 200px;
            font-family: 'Montserrat Medium', sans-serif;
            width: calc(100% - 200px);
        }
        .student-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            font-size: 32px;
            color: #333333;
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
        }
        .review-form {
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
        .form-group select,
        .form-group textarea {
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
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #615f5f;
        }
        /* убрали .input-error и .error */
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
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        <h2>Оставить отзыв</h2>
        <div class="review-form">
            <form id="review-form" action="{{ route('student.reviews.store') }}" method="POST" novalidate>
                @csrf

                {{-- Заголовок --}}
                <div class="form-group">
                    <label for="title">Заголовок</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                    >
                </div>

                {{-- Оценка --}}
                <div class="form-group">
                    <label for="rating">Оценка (1–5)</label>
                    <select id="rating" name="rating">
                        <option value="" disabled {{ old('rating') ? '' : 'selected' }}>— выберите —</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Курс --}}
                <div class="form-group">
                    <label for="course_id">Курс (необязательно)</label>
                    <select id="course_id" name="course_id">
                        <option value="">— без привязки —</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Комментарий --}}
                <div class="form-group">
                    <label for="comment">Комментарий</label>
                    <textarea id="comment" name="comment" rows="4">{{ old('comment') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">Отправить отзыв</button>
            </form>
        </div>
    </div>

    {{-- Всплывающие уведомления и валидация --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Берём «сырые» ошибки из PHP
                let raw = @json($errors->all());

                // Заменяем ключ 'validation.uploaded' и фильтруем пустые
                let errs = raw.map(msg => {
                    return msg === 'validation.uploaded'
                        ? 'Ошибка при загрузке файла.'
                        : msg;
                }).filter(Boolean);

                // Если есть ошибки — показываем их в Swal
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
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Клиентская валидация
            const form = document.getElementById('review-form');
            form.addEventListener('submit', function(e) {
                const errsClient = [];
                if (!form.title.value.trim()) {
                    errsClient.push('Введите заголовок.');
                }
                if (!form.rating.value) {
                    errsClient.push('Выберите оценку.');
                }
                if (!form.comment.value.trim()) {
                    errsClient.push('Введите комментарий.');
                }

                if (errsClient.length) {
                    e.preventDefault();
                    const list = errsClient.map(m => `<li>${m}</li>`).join('');
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

            // Уведомление об успешном создании
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                customClass: { popup: 'swal2-toast' }
            });
            @php session()->forget('success'); @endphp
            @endif
        });
    </script>
@endsection

