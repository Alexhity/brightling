<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class AdminPriceController extends Controller
{
    /**
    * Показывает список тарифов.
    */
    public function index(Request $request)
    {
        // Разрешённые поля для сортировки
        $allowed = ['name','lesson_duration','unit_price'];

        // Получаем из строки запроса, или задаём по умолчанию
        $sort = in_array($request->get('sort'), $allowed)
            ? $request->get('sort')
            : 'name';
        $dir  = $request->get('dir') === 'desc' ? 'desc' : 'asc';

//        $prices = Price::orderBy('unit_price')->get();
        $prices = Price::orderBy($sort, $dir)->get();
        return view('auth.admin.prices.index', compact('prices', 'sort', 'dir'));
    }

    /**
     * Показывает форму создания нового тарифа.
     */
    public function create()
    {
        return view('auth.admin.prices.create');
    }

    /**
     * Сохраняет новый тариф в базе.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|unique:prices,name',
            'lesson_duration' => 'required|integer|min:1',
            'unit_price'      => 'required|numeric|min:0',
            'format'          => 'required|in:individual,group',
        ]);

        Price::create($data);

        return redirect()
            ->route('admin.prices.index')
            ->with('success', 'Тариф «'.$data['name'].'» успешно создан.');
    }

    /**
     * Показывает форму редактирования существующего тарифа.
     */
    public function edit(Price $price)
    {
        return view('auth.admin.prices.edit', compact('price'));
    }

    /**
     * Обновляет данные тарифа.
     */
    public function update(Request $request, Price $price)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'lesson_duration' => 'required|integer|min:1',
            'unit_price'      => 'required|numeric|min:0',
            'format'          => 'required|in:individual,group',
        ]);

        $price->update($data);

        return redirect()
            ->route('admin.prices.index')
            ->with('success', 'Тариф «'.$data['name'].'» успешно обновлён.');
    }

    /**
     * Удаляет тариф.
     */
    public function destroy(Price $price)
    {
        $price->delete();

        return redirect()
            ->route('admin.prices.index')
            ->with('success', 'Тариф «'.$price->name.'» удалён.');
    }
}
