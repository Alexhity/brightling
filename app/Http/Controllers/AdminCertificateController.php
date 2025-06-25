<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;

class AdminCertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('user')
            ->latest()      // => orderBy('created_at', 'desc')
            ->paginate(10);  // или любое ваше значение

        return view('auth.admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        // Только не-админы
        $users = User::where('role', '<>', 'admin')->get();
        return view('auth.admin.certificates.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'title'     => 'required|string|max:255',
            'file'      => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // Обработка файла
        $file      = $request->file('file');
        $filename  = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/certificates'), $filename);

        Certificate::create([
            'user_id'   => $data['user_id'],
            'title'     => $data['title'],
            'file_path' => $filename,
        ]);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Сертификат успешно добавлен.');
    }

    public function edit($id)
    {
        $certificate = Certificate::findOrFail($id);
        $users       = User::where('role', '<>', 'admin')->get();
        return view('auth.admin.certificates.edit', compact('certificate', 'users'));
    }

    public function update(Request $request, $id)
    {
        $cert = Certificate::findOrFail($id);
        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'title'     => 'required|string|max:255',
            'file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('file')) {
            // удалить старый
            File::delete(public_path('images/certificates/'.$cert->file_path));
            // загрузить новый
            $file     = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/certificates'), $filename);
            $cert->file_path = $filename;
        }

        $cert->user_id = $data['user_id'];
        $cert->title   = $data['title'];
        $cert->save();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Сертификат обновлён.');
    }

    public function destroy($id)
    {
        $cert = Certificate::findOrFail($id);

        // Удаляем файл из public/images/certificates
        $path = public_path('images/certificates/' . $cert->file_path);
        if (file_exists($path)) {
            unlink($path); // или File::delete($path)
        }

        $cert->delete();

        return redirect()->back()->with('success', 'Сертификат удалён.');
    }
}
