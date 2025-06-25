<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AdminUsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('auth.admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('auth.admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone'     => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string',
            'level' => 'nullable|string',
            'date_birthday' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь создан успешно');
    }

    public function edit(User $user)
    {
        return view('auth.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string',
            'level' => 'nullable|string',
            'date_birthday' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь обновлён успешно');
    }

    public function destroy(User $user)
    {
        // Проверяем связи с основными слотами
        $hasMainSlots = Timetable::where('user_id', $user->id)->exists();

        // Проверяем связи со слотами-заменами
        $hasOverrideSlots = Timetable::where('override_user_id', $user->id)->exists();

        if ($hasMainSlots || $hasOverrideSlots) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Невозможно удалить: пользователь назначен преподавателем в расписании.');
        }

        // Если связей нет — удаляем
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно удалён.');
    }
}
