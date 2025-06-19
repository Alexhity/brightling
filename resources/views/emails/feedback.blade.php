@component('mail::message')
    # Обратная связь

    Получена новая заявка.

    **Имя:** {{ $feedbackData['name'] }}

    **Email:** {{ $feedbackData['email'] }}

    **Сообщение:**
    {{ $feedbackData['message'] }}

    С уважением,
    Языковая школа BrightLing
@endcomponent
