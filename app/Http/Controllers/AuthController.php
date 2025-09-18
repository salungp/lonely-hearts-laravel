<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(): View
    {
        $user = '';
        if (session()->has('profile')) {
            $user = session('profile')['display_name'];
        } 

        return view('auth.login', [
            'user' => $user
        ]);
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
            'country_code' => 'required|string|max:5',
            'phone_number' => 'required|string|max:20',
        ]);

        $phone_number = $request->country_code . preg_replace('/\D+/', '', $request->phone_number);
        $profile = session('profile');
        $user = User::where('phone_number', $phone_number)->first();

        // One-time OTP (for both new or existing users)
        $otp = rand(10000, 99999);

        if ($profile) {
            // Always create new user if profile session exists
            $user = User::create([
                'name'         => $profile['display_name'] ?? '',
                'phone_number' => $phone_number,
                'email'        => null,
            ]);

            if ($user) {
                // Insert profile
                DB::table('table_profiles')->insert([
                    'user_id'      => $user->id,
                    'display_name' => $profile['display_name'] ?? '',
                    'occupation'   => strtolower($profile['occupation'] ?? ''),
                    'age'          => $profile['age'] ?? '',
                    'status'       => strtolower($profile['status'] ?? ''),
                    'gender'       => $profile['gender'] ?? '',
                    'location'     => session('ads')['location'] ?? '',
                    'bio'          => '',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Store OTP session
                session([
                    'otp' => [
                        'otp'     => $otp,
                        'user_id' => $user->id,
                    ],
                ]);

                // Clear profile session to avoid duplicates
                $request->session()->forget('profile');

                Auth::login($user);
                return redirect()->route('auth.verify');
            }

            logger()->error("Failed to create user from profile session");
            return back()->withErrors(['msg' => 'Unable to create account. Try again.']);
        }

        // No profile session = normal login/register
        if (!$user) {
            $user = User::create([
                'name'         => '',
                'phone_number' => $phone_number,
                'email'        => null,
            ]);

            // Existing user -> login
            Auth::login($user);

            session([
                'otp' => [
                    'otp'     => $otp,
                    'user_id' => $user->id,
                ],
                'current_url' => $phone_number, // mark that we must create profile
            ]);

            return redirect()->route('profile.create');
        }

        // Existing user -> login
        Auth::login($user);

        session([
            'otp' => [
                'otp'     => $otp,
                'user_id' => $user->id,
            ],
        ]);

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

                return redirect()->route('ad.writing', ['box' => $box]);
            } else {
                return redirect()->route('home');
            }

        } else {
            return redirect()->route('auth.verify')->with('error', 'Your otp is not correct');
        }
    }

    public function sendVerification(Request $request)
    {
        $email = $request->input('email');
        $code = random_int(100000, 999999); // 6-digit code

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

        // Send email using Blade template
        Mail::send('emails.verify-code', ['code' => $code], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Your Email Verification Code');
        });

        return response()->json(['status' => 'verification_sent']);
    }

    public function update_email(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $validated['email'];
        $code = random_int(100000, 999999);
        $check_email = DB::table('users')->where('email', $email)->first();
        $id = Auth::id();

        if (!$check_email) {
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

            DB::table('users')->where('id', $id)->update([
                'email' => $email
            ]);

            // Send email using Blade template
            Mail::send('mail.verify_code', ['code' => $code], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your Email Verification Code');
            });

            return redirect()->route('profile.verify_email');

        } else {
            return redirect()->route('profile.email')->with('error', 'The email is already exists!');
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
