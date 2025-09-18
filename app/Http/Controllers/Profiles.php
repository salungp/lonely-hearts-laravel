<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Profiles extends Controller
{
    public function create(): View
    {
        return view('profile.create');
    }

    public function profile(): View
    {
        $id = Auth::id();
        $user = DB::table('table_profiles')->where('user_id', $id)->first();
        return view('profile.view', [
            'user' => $user
        ]);
    }

    public function profile_edit(): View
    {
        return view('profile.edit');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_name'=> 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'age'        => 'required|integer|min:1',
            'status'     => 'required|string|max:255',
            'gender'     => 'required|string|max:10',
        ]);

        // Check if the user come from create ad or login
        if (session()->has('current_url')) {
            // user come from login
            $user_id = session('otp')['user_id'];

            DB::table('users')->where('id', $user_id)->update(['name' => $validated['person_name']]);

            $user = DB::table('users')->where('id', $user_id)->first();

            DB::table('table_profiles')->insert([
                'user_id'      => session('otp')['user_id'],
                'display_name' => $validated['person_name'],
                'occupation'   => $validated['occupation'],
                'age'          => $validated['age'],
                'status'       => $validated['status'],
                'gender'       => $validated['gender'],
                'location'     => 'London',
                'bio'          => '',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $request->session()->forget('current_url');
            return redirect()->route('auth.verify');
        } else {
            // Save to session
            session([
                'profile' => [
                    'display_name' => $validated['person_name'],
                    'occupation'   => $validated['occupation'],
                    'age'          => $validated['age'],
                    'status'       => $validated['status'],
                    'gender'       => $validated['gender'],
                ],
            ]);

            // Retrieve intended URL or fallback
            $redirectTo = session()->pull('intended_url', route('home'));
        
            return redirect($redirectTo);
        }
    }
}
