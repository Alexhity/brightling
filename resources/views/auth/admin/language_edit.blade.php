@extends('layouts.app')

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="lang-container">
        <h2>Редактировать язык: {{ $language->name }}</h2>

        {{-- Если остались ошибки валидации, выведем их --}}
        @if($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="lang-form">
            <form action="{{ route('admin.languages.update', $language->id) }}" method="POST">
                @csrf
                @method('PUT')

                <label for="name">Название языка:</label><br>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $language->name) }}"
                    placeholder="Введите новое название языка"
                    required
                >
                <button type="submit">Сохранить</button>
                <a href="{{ route('admin.languages') }}" class="cancel-btn">Отмена</a>
            </form>
        </div>
    </div>
@endsection
