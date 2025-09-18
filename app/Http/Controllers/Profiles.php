<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

    public function email(): View
    {
        $id = Auth::id();
        $user = DB::table('users')->where('id', $id)->first();
        return view('profile.email', [
            'user' => $user
        ]);
    }

    public function profile_edit(): View
    {
        $id = Auth::id();
        $user = DB::table('table_profiles')->where('user_id', $id)->first();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'person_name'=> 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'age'        => 'required|integer|min:1',
            'status'     => 'required|string|max:255',
            'gender'     => 'required|string|max:10',
            'email'      => 'email|max:255',
        ]);

        $id = Auth::id();

        DB::table('table_profiles')->where('user_id', $id)->update([
            'display_name' => $validated['person_name'],
            'occupation' => $validated['occupation'],
            'age' => $validated['age'],
            'status' => $validated['status'],
            'gender' => $validated['gender'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'The data has been updated!');
    }

    public function verify_email_code(Request $request)
    {
        $otp_input_1 = $request->box_1;
        $otp_input_2 = $request->box_2;
        $otp_input_3 = $request->box_3;
        $otp_input_4 = $request->box_4;
        $otp_input_5 = $request->box_5;
        $otp_input_6 = $request->box_6;
        $code = $otp_input_1.$otp_input_2.$otp_input_3.$otp_input_4.$otp_input_5.$otp_input_6;
        $id = Auth::id();

        $record = DB::table('email_verifications')
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($record) {
            // Mark user as verified (update users table manually)
            DB::table('users')->where('id', $id)->update([
                'email_verified_at' => Carbon::now()
            ]);

            // Optionally delete verification record
            DB::table('email_verifications')->where('code', $code)->delete();

            return redirect()->route('profile.email')->with('succes', 'The profile has been updated!');
        }

        return redirect()->route('profile.email')->with('error', 'Something went wrong!');
    }


    public function verify_email(): View
    {
        return view('auth.verify_email');
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
