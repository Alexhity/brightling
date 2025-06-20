<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $levels = ['beginner'=>'Beginner','A1'=>'A1','A2'=>'A2','B1'=>'B1','B2'=>'B2','C1'=>'C1','C2'=>'C2'];
        $languages = Language::all();
        $certificates = $user->certificates;
        return view('auth.teacher.profile.edit', compact('user','levels','languages','certificates'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'first_name'=>'required|string|max:255',
            'last_name'=>'nullable|string|max:255',
            'date_birthday'=>'nullable|date',
            'phone'=>'required|string|max:20',
            'email'=>'required|email|max:255|unique:users,email,'.$user->id,
            'description'=>'nullable|string|max:2000',
            'level'=>'nullable|in:beginner,A1,A2,B1,B2,C1,C2',
            'languages'=>'nullable|array',
            'languages.*'=>'exists:languages,id',
            'avatar'=>'nullable|image|max:2048',
            'languages_levels'    => 'nullable|array',
            'languages_levels.*'  => 'nullable|in:beginner,A1,A2,B1,B2,C1,C2',

        ],[
            'avatar.image'=>'Файл должен быть изображением',
        ]);
//        if ($request->hasFile('avatar')) {
//            // сохраняем в storage/app/public/avatars
//            $path = $request->file('avatar')->store('avatars', 'public');
//            // сохраняем относительный путь в БД, например 'avatars/имя_файла.jpg'
//            $data['file_path'] = $path;
//        }
        // Если пришёл файл
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Генерируем уникальное имя
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            // Куда сохраняем: public/images/profile
            $destination = public_path('images/profile');
            // Создаём папку, если её нет
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            // Перемещаем файл
            $file->move($destination, $filename);

            // Сохраняем относительный путь в БД
            $data['file_path'] = 'images/profile/' . $filename;
        }

        $user->update($data);
        if (!empty($data['languages'])) {
            $user->languages()->sync($data['languages']);
        }
        $sync = [];
        foreach ($request->input('languages', []) as $langId) {
            $sync[$langId] = [
                'level' => $request->input("languages_levels.$langId") ?? null
            ];
        }
        $user->languages()->sync($sync);

        return redirect()->route('teacher.profile.edit')->with('success','Профиль успешно обновлён');
    }

    public function showPasswordForm()
    {
        return view('auth.teacher.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Валидация с минимумом 5 символов и кастомными сообщениями
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|string|min:5|confirmed',
        ], [
            'current_password.required' => 'Введите текущий пароль',
            'password.required'         => 'Введите новый пароль',
            'password.min'              => 'Новый пароль должен содержать минимум 5 символов',
            'password.confirmed'        => 'Новый пароль и его подтверждение не совпадают',
        ]);

        // Проверяем, что текущий пароль верный
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors([
                'current_password' => 'Текущий пароль неверен',
            ]);
        }

        // Сохраняем новый пароль
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()
            ->route('teacher.profile.edit')
            ->with('success', 'Пароль успешно изменён');
    }
}
