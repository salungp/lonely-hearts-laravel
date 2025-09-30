<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|min:10|max:20',
        ]);

        $phone_number = $request->country_code . preg_replace('/\D+/', '', $request->phone_number);
        $user = User::where('phone_number', $phone_number)->first();
        $profile = session('profile');
        $otp = rand(10000, 99999);
        session(['otp_code' => $otp, 'otp_expire' => now()->addMinutes(5)]);

        session([
            'otp' => [
                'otp'     => $otp,
                'user_id' => $user->id,
                'phone'   => $phone_number
            ],
        ]);

        PhoneVerification::updateOrCreate(
            ['phone' => $phone_number],
            [
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]
        );

        // $sid    = env('TWILIO_SID');
        // $token  = env('TWILIO_TOKEN');
        // $service_id = "VA9ee664a3c3fff2a63cd16974bb4ecb6a";
        // $twilio = new Client($sid, $token);

        // $verification_check = $twilio->verify->v2->services($service_id)
        //                            ->verificationChecks
        //                            ->create([
        //                                         "to" => $phone_number,
        //                                         "code" => $otp
        //                                     ]
        //                             );

        return redirect()->route('auth.verify')->with('success', 'Your otp code is has been sent!');
    }

    // Step 2: Verify OTP
    public function verifyOtp(Request $request)
    {
        $otp = session('otp');
        $otp_input_1 = $request->box_1;
        $otp_input_2 = $request->box_2;
        $otp_input_3 = $request->box_3;
        $otp_input_4 = $request->box_4;
        $otp_input_5 = $request->box_5;
        $otp_input = $otp_input_1.$otp_input_2.$otp_input_3.$otp_input_4.$otp_input_5;

        $verification = PhoneVerification::where('phone', $request->phone)->first();

        if (!$verification) {
            return response()->json(['error' => 'No OTP found'], 404);
        }

        if ($verification->isExpired()) {
            return response()->json(['error' => 'OTP expired'], 400);
        }

        if (!$verification->hasAttemptsLeft()) {
            return response()->json(['error' => 'Too many attempts'], 429);
        }

        if (!Hash::check($request->otp, $verification->otp)) {
            $verification->increment('attempts');
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        // ✅ Success: mark phone as verified
        User::where('phone', $request->phone)->update([
            'phone_verified_at' => now(),
        ]);

        $verification->delete(); // cleanup
        $request->session()->forget('otp');

        return redirect()->route('home');
    }
}
