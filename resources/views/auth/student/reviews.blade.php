{{-- resources/views/auth/student/reviews --}}
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
            margin-bottom: 4px;
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
            transition: border-color 0.2s;
            background-color: #ffffff;
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
        .input-error {
            border-color: #ff4c4c !important;
        }
        .error {
            position: absolute;
            bottom: -18px;
            left: 0;
            font-size: 13px;
            color: #ff4c4c;
            font-family: 'Montserrat Medium', sans-serif;
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
            display: inline-block;
        }
        .btn-submit:hover {
            background-color: #93edca;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_student')

    <div class="student-content-wrapper">
        <h2>Оставить отзыв</h2>
        <div class="review-form">
            <form action="{{ route('student.reviews.store') }}" method="POST" novalidate>
                @csrf

                {{-- Заголовок --}}
                <div class="form-group">
                    <label for="title">Заголовок</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        class="{{ $errors->has('title') ? 'input-error' : '' }}"
                    >
                    @if($errors->has('title'))
                        <div class="error">{{ $errors->first('title') }}</div>
                    @endif
                </div>

                {{-- Оценка --}}
                <div class="form-group">
                    <label for="rating">Оценка (1–5)</label>
                    <select
                        id="rating"
                        name="rating"
                        required
                        class="{{ $errors->has('rating') ? 'input-error' : '' }}"
                    >
                        <option value="" disabled {{ old('rating') ? '' : 'selected' }}>— выберите —</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @if($errors->has('rating'))
                        <div class="error">{{ $errors->first('rating') }}</div>
                    @endif
                </div>

                {{-- Курс (необязательно) --}}
                <div class="form-group">
                    <label for="course_id">Курс (необязательно)</label>
                    <select
                        id="course_id"
                        name="course_id"
                        class="{{ $errors->has('course_id') ? 'input-error' : '' }}"
                    >
                        <option value="">— без привязки —</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('course_id'))
                        <div class="error">{{ $errors->first('course_id') }}</div>
                    @endif
                </div>

                {{-- Комментарий --}}
                <div class="form-group">
                    <label for="comment">Комментарий</label>
                    <textarea
                        id="comment"
                        name="comment"
                        rows="4"
                        class="{{ $errors->has('comment') ? 'input-error' : '' }}"
                    >{{ old('comment') }}</textarea>
                    @if($errors->has('comment'))
                        <div class="error">{{ $errors->first('comment') }}</div>
                    @endif
                </div>

                <button type="submit" class="btn-submit">Отправить отзыв</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
@endsection
