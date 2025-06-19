<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Language;

class AdminLanguagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображает страницу управления языками.
     */
    public function index(Request $request)
    {
        // Разрешённые поля для сортировки
        $allowed = ['name'];

        $sort = in_array($request->get('sort'), $allowed)
            ? $request->get('sort')
            : 'name';

        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        // Получаем языки с учётом сортировки
        $languages = Language::orderBy($sort, $dir)->get();

        return view('auth.admin.languages', compact('languages', 'sort', 'dir'));
    }



    /**
     * Обрабатывает добавление нового языка.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:languages,name',
        ]);

        Language::create($validated);

        return redirect()->route('admin.languages.index')->with('success', 'Новый язык успешно добавлен!');
    }

    /**
     * Отображает форму редактирования конкретного языка.
     */
    public function edit(Language $language)
    {
        // Передаём в view модель $language
        return view('auth.admin.language_edit', compact('language'));
    }

    /**
     * Обрабатывает сохранение изменений языка.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            // Уникальность: игнорируем текущий ID при проверке
            'name' => 'required|string|max:255|unique:languages,name,' . $language->id,
        ]);

        $language->update($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Язык успешно обновлён!');
    }

    /**
     * Удаляет выбранный язык.
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return redirect()->route('admin.languages.index')->with('success', 'Язык успешно удалён!');
    }
}
