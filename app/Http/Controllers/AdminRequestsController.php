<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FreeLessonRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountCreated;

class AdminRequestsController extends Controller
{
    public function index()
    {

        // Загружаем заявки, которые ещё не обработаны
        $requests = FreeLessonRequest::where('status', '<>', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.admin.requests', compact('requests'));
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'requested_role' => 'required|in:student,teacher,admin',
        ]);

        $application = FreeLessonRequest::findOrFail($id);
        $application->requested_role = $validated['requested_role'];
        $application->save();

        return redirect()->back()->with('success', 'Роль успешно обновлена!');
    }

    public function createProfile(Request $request, $id)
    {
        $application = FreeLessonRequest::findOrFail($id);
        $randomPassword = Str::random(8);

        $user = User::create([
            'first_name' => $application->name,
            'email'      => $application->email,
            'phone'      => $application->phone,
            'role'       => $application->requested_role,
            'password'   => Hash::make($randomPassword),
        ]);

        $application->status = 'approved';
        $application->save();

        Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));

        return redirect()->back()->with('success', 'Личный кабинет успешно создан! Проверьте свою почту.');
    }

    public function createProfilesAll(Request $request)
    {
        $pendingRequests = FreeLessonRequest::where('status', '<>', 'approved')->get();

        foreach ($pendingRequests as $application) {
            $randomPassword = Str::random(8);

            $user = User::create([
                'first_name' => $application->name,
                'email'      => $application->email,
                'phone'      => $application->phone,
                'role'       => $application->requested_role,
                'password'   => Hash::make($randomPassword),
            ]);

            $application->status = 'approved';
            $application->save();

            Mail::to($application->email)->send(new AccountCreated($user, $randomPassword));
        }

        return redirect()->back()->with('success', 'Все заявки обработаны и личные кабинеты созданы.');
    }
}
