<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        // Получаем языки из курсов студента
        $languages = $user->courses
            ->pluck('language.name')
            ->unique()
            ->values();

        $certificates = $user->certificates;

        return view('auth.student.profile.edit', compact('user','languages','certificates'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'date_birthday' => 'nullable|date',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'description'   => 'nullable|string|max:2000',
            'avatar'        => 'nullable|image|max:2048',
            'languages'     => 'nullable|array',
            'languages.*.level' => 'nullable|in:beginner,A1,A2,B1,B2,C1,C2',
        ], [
            'avatar.image' => 'Файл должен быть изображением',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Генерируем уникальное имя
            $filename = time() . '_' . $file->getClientOriginalName();
            // Перемещаем в public/images/profile
            $file->move(public_path('images/profile'), $filename);
            // Сохраняем путь относительно public
            $data['file_path'] = 'images/profile/' . $filename;
        }

        $user->update($data);

        // Обновляем pivot: сохраняем уровни
        if (!empty($data['languages'])) {
            $syncData = [];
            foreach ($data['languages'] as $langId => $vals) {
                $syncData[$langId] = ['level' => $vals['level'] ?? null];
            }
            $user->languages()->sync($syncData);
        }

        return redirect()
            ->route('student.profile.edit')
            ->with('success','Профиль успешно обновлён');
    }

    public function showPasswordForm()
    {
        return view('auth.student.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:5|confirmed',
        ], [
            'current_password.required' => 'Введите текущий пароль',
            'password.required'         => 'Введите новый пароль',
            'password.min'              => 'Новый пароль должен содержать минимум 5 символов',
            'password.confirmed'        => 'Пароли не совпадают',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Текущий пароль неверен',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('student.profile.edit')
            ->with('success','Пароль успешно изменён');
    }
}
