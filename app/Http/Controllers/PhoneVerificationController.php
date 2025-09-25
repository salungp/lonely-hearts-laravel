<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PhoneVerificationController extends Controller
{
    // Step 1: Request OTP
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:20',
        ]);

        $otp = rand(100000, 999999);

        PhoneVerification::updateOrCreate(
            ['phone' => $request->phone],
            [
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]
        );

        // TODO: integrate SMS gateway here
        // SmsService::send($request->phone, "Your code is $otp");

        return response()->json(['message' => 'OTP sent']);
    }

    // Step 2: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|numeric',
        ]);

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

        return response()->json(['message' => 'Phone verified successfully']);
    }
}
