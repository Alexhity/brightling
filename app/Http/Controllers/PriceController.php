<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $format = $request->input('format');
        $sort   = $request->input('sort') === 'price' ? 'price' : null;
        $dir    = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        // Базовый запрос
        $query = Price::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($format) {
            $query->where('format', $format);
        }

        if ($sort === 'price') {
            $query->orderBy('unit_price', $dir);
        }

        $prices = $query->paginate(12)->appends($request->all());

        return view('prices', compact('prices', 'search', 'format', 'sort', 'dir'));
    }
}
