<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function verify_code(): View
    {
        return view('auth.verify');
    }

    public function logout()
    {
        Auth::logout(); // Logs out the user
        request()->session()->invalidate(); // Invalidate session
        request()->session()->regenerateToken(); // Regenerate CSRF token

        return redirect()->route('home'); // Redirect anywhere you want (e.g., login page)
    }

    public function LoginOrRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $name = $request->name;
        $code = $request->country_code;
        $phone = $request->phone_number;
        $phone_number = $code.$phone;

        $user = User::where('phone_number', $phone_number)->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $name,
                'phone_number' => $phone_number,
                'email' => null, // leave email empty
            ]);

            // Define the profile data
            $profile = session('profile');
            $age = $profile['age'] ?? '';
            $occupation = strtolower($profile['occupation'] ?? '');
            $status = strtolower($profile['status'] ?? '');
            $gender = $profile['gender'];
            $location = session('ads')['location'] ?? '';
            $display_name = $profile['display_name'] ?? '';

            if ($profile) {
                // Insert profile
                DB::table('table_profiles')->insert([
                    'user_id'      => $user->id,
                    'display_name' => $display_name,
                    'occupation'   => $occupation,
                    'age'          => $age,
                    'status'       => $status,
                    'gender'       => $gender,
                    'location'     => $location,
                    'bio'          => '',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                // Log error if session missing
                logger()->warning('Profile session data not found for new user: ' . $user->id);
            }
        }

        $otp = rand(10000, 99999);

        session([
            'otp' => [
                'otp'       => $otp,
                'user_id'   => $user->id,
            ],
        ]);

        $request->session()->forget('profile');

        // Log in user
        Auth::login($user);

        return redirect()->route('auth.verify');
    }

    public function verifyOtp(Request $request)
    {
        $otp = session('otp');
        $otp_input_1 = $request->box_1;
        $otp_input_2 = $request->box_2;
        $otp_input_3 = $request->box_3;
        $otp_input_4 = $request->box_4;
        $otp_input_5 = $request->box_5;
        $otp_input = $otp_input_1.$otp_input_2.$otp_input_3.$otp_input_4.$otp_input_5;

        if ($otp['otp'] == $otp_input) {
            $ads = session('ads');

            // Check if this verify is part of create ad
            if ($ads) {
                $user = DB::table('table_profiles')->where('user_id', $otp['user_id'])->first();

                $box = rand(100000, 999999);
                $slug = Str::slug($user->location.' '.$user->display_name.' '.$user->occupation.' '.$user->status.' '.$box);
                
                // insert ads data
                DB::table('ads')->insert([
                    'user_id'               => $otp['user_id'],
                    'description'           => $ads['description'],
                    'slug'                  => $slug,
                    'snapshot_name'         => $user->display_name,
                    'snapshot_occupation'   => $user->occupation,
                    'snapshot_age'          => $user->age,
                    'snapshot_status'       => $user->status,
                    'snapshot_gender'       => $user->gender,
                    'location'              => $user->location,
                    'views'                 => 0,
                    'box_number'            => $box,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                $request->session()->forget('ads');
                $request->session()->forget('otp');

                return redirect()->route('ad.writing');
            } else {
                return redirect()->route('home');
            }

        } else {
            return redirect()->route('auth.verify')->with('error', 'Your otp is not correct');
        }
    }

    // protected function sendPhoneVerification($phone_number)
    // {
    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');
    //     $serviceSid = env('TWILIO_VERIFY_SERVICE_SID');

    //     $twilio = new Client($sid, $token);

    //     $twilio->verify->v2->services($serviceSid)
    //         ->verifications
    //         ->create($phone_number, "sms");
    // }

    // public function verify(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|string',
    //         'code' => 'required|string',
    //     ]);

    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');
    //     $serviceSid = env('TWILIO_VERIFY_SERVICE_SID');

    //     $twilio = new Client($sid, $token);

    //     $verification_check = $twilio->verify->v2->services($serviceSid)
    //         ->verificationChecks
    //         ->create([
    //             'to' => $request->phone_number,
    //             'code' => $request->code,
    //         ]);

    //     if ($verification_check->status === "approved") {
    //         // Phone verified
    //         return redirect()->route('home')->with('success', 'Phone verified!');
    //     } else {
    //         return back()->withErrors(['code' => 'Invalid verification code']);
    //     }
    // }
}
