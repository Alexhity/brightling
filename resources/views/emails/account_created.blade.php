@component('mail::message')
    # Здравствуйте, {{ $user->first_name }}!

    Ваш личный кабинет был успешно создан.

    **Ваш логин:** {{ $user->email }}
    **Ваш пароль:** {{ $password }}

    Для входа в личный кабинет нажмите на кнопку ниже.

    @component('mail::button', ['url' => route('login')])
        Войти в личный кабинет
    @endcomponent

    Спасибо,
    {{ config('app.name') }}
@endcomponent
