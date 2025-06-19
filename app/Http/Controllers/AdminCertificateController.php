<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCertificateController extends Controller
{
    public function create()
    {
        $languages = Language::orderBy('name')->get();
        $levels    = ['A1','A2','B1','B2','C1','C2'];
        $students  = User::where('role','student')->orderBy('first_name')->get();

        return view('auth.admin.certificates.create', compact(
            'languages','levels','students'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mode'        => 'required|in:individual,bulk',
            'language_id' => 'required|exists:languages,id',
            'level'       => 'required|in:A1,A2,B1,B2,C1,C2',
            'user_id'     => 'required_if:mode,individual|exists:users,id',
            'file'        => 'required|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $path = $request->file('file')->store('certificates','public');

        if ($data['mode'] === 'bulk') {
            $students = User::where('role','student')
                ->whereHas('languages', function($q) use($data) {
                    $q->where('language_id',$data['language_id'])
                        ->wherePivot('level',$data['level']);
                })
                ->get();
        } else {
            $students = User::where('id',$data['user_id'])->get();
        }

        $created = 0;
        foreach ($students as $stu) {
            $exists = Certificate::where([
                ['user_id',$stu->id],
                ['language_id',$data['language_id']],
                ['level',$data['level']],
            ])->exists();

            if (!$exists) {
                Certificate::create([
                    'user_id'     => $stu->id,
                    'language_id' => $data['language_id'],
                    'level'       => $data['level'],
                    'title'       => Language::find($data['language_id'])->name . " {$data['level']}",
                    'file_path'   => $path,
                    'issued_at'   => now(),
                ]);
                $created++;
            }
        }

        return redirect()
            ->route('admin.certificates.create')
            ->with('success', "Сертификатов выдано: {$created}");
    }
}
