<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackMail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MainController extends Controller
{
    public function newAdmin()
    {
        User::create([
           'email' => 'admin@admin.com',
           'password' => Hash::make('123'),
           'role' => 'admin',
           'first_name' => 'Admin',
            'phone' => '+375333837946',
        ]);
    }

//    Метод для получения языков в форму бесплатной заявки
    public function index()
    {
        $languages = Language::all();
        return view('main', compact('languages'));
    }

    public function sendContact(Request $request)
    {
        // Валидация полей
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Собираем текст письма
        $body = "Новое сообщение из формы «Связаться с нами»: \n\n"
            . "Имя: " . $validated['name'] . "\n"
            . "Email: " . $validated['email'] . "\n\n"
            . "Сообщение: \n" . $validated['message'];

        // Отправляем письмо на адрес школы
        Mail::raw($body, function ($message) use ($validated) {
            $message->to('borodichalexandra@gmail.com')
                ->subject('Новое сообщение с сайта BrightLing');
            // Можно дополнительно указать from:
            // ->from($validated['email'], $validated['name']);
        });

        // Возвращаемся назад с флеш-сообщением
        return redirect()
            ->back()
            ->with('success', 'Спасибо! Ваше сообщение отправлено.')
            ->withFragment('contact-anchor');
    }
}
