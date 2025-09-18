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

    public function profile_edit(): View
    {
        $id = Auth::id();
        $user = DB::table('table_profiles')->where('user_id', $id)->first();
        $get_email = DB::table('users')->where('id', $id)->first();
        $email = $get_email->email;

        return view('profile.edit', [
            'user' => $user,
            'email' => $email
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

        $email = $request->input('email');
        $code = random_int(100000, 999999); // 6-digit code
        $id = Auth::id();
        $check_email = DB::table('users')->where('email', $email)->first();
        
        if ($check_email) {
            return redirect()->route('profile.edit')->with('error', 'The email is already exists!');
        } else {
            // Store in DB
            DB::table('email_verifications')->updateOrInsert(
                ['email' => $email],
                [
                    'code' => $code,
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]
            );

            DB::table('table_profiles')->where('user_id', $id)->update([
                'display_name' => $validated['person_name'],
                'occupation' => $validated['occupation'],
                'age' => $validated['age'],
                'status' => $validated['status'],
                'gender' => $validated['gender'],
            ]);

            DB::table('users')->where('id', $id)->update([
                'email' => $email
            ]);

            // Send email using Blade template
            Mail::send('mail.verify_code', ['code' => $code], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your Email Verification Code');
            });

            return redirect()->route('profile.verify_email');
        }
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

            return redirect()->route('profile.edit')->with('succes', 'The profile has been updated!');
        }

        return redirect()->route('profile.edit')->with('error', 'Something went wrong!');
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
