<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use Illuminate\Support\Facades\Log;
use App\Models\Ad;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Package;
use App\Models\UserPackage;
use OpenAI\Laravel\Facades\OpenAI;

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

        // Normalize phone number
        $phone_number = $request->country_code . preg_replace('/\D+/', '', $request->phone_number);
        $profileData = session('profile');

        // One-time OTP (for both new or existing users)
        $otp = rand(10000, 99999);

        // --- CASE 1: User came from profile creation flow ---
        if ($profileData) {
            try {
                $user = User::create([
                    'name'         => $profileData['display_name'] ?? '',
                    'phone_number' => $phone_number,
                    'email'        => null,
                ]);

                Profile::create([
                    'user_id'      => $user->id,
                    'display_name' => $profileData['display_name'] ?? '',
                    'occupation'   => strtolower($profileData['occupation'] ?? ''),
                    'age'          => $profileData['age'] ?? 0,
                    'status'       => strtolower($profileData['status'] ?? ''),
                    'gender'       => $profileData['gender'] ?? '',
                    'location'     => session('ads')['location'] ?? '',
                    'bio'          => '',
                ]);

                // Store OTP in session (⚠️ temporary — better to use phone_verifications table)
                session([
                    'otp' => [
                        'otp'     => $otp,
                        'user_id' => $user->id,
                    ],
                ]);

                $request->session()->forget('profile'); // prevent duplicate users

                return redirect()->route('auth.verify');
            } catch (\Exception $e) {
                Log::error("Failed to create user from profile session", ['error' => $e->getMessage()]);
                return back()->withErrors(['msg' => 'Unable to create account. Try again.']);
            }
        }

        // --- CASE 2: Normal login/register flow ---
        $user = User::firstOrCreate(
            ['phone_number' => $phone_number],
            ['name' => '', 'email' => null]
        );

        // Store OTP in session
        session([
            'otp' => [
                'otp'     => $otp,
                'user_id' => $user->id,
            ],
        ]);

        // If this was a brand new user → force profile creation
        // If this was a brand new user → force profile creation
        if ($user->wasRecentlyCreated) {
            session(['must_create_profile' => true]);

            // 🔹 FIX: log them in now
            Auth::login($user);

            return redirect()->route('profile.create');
        }

        // Existing user → verify OTP
        return redirect()->route('auth.verify');
    }


    public function verifyOtp(Request $request)
    {
        $otpData = session('otp');

        $inputOtp = collect(range(1,5))
            ->map(fn($i) => trim((string)$request->input("box_$i", '')))
            ->implode('');

        if (!$otpData || (string)$otpData['otp'] !== $inputOtp) {
            return redirect()->route('auth.verify')->with('error', 'Your OTP is not correct');
        }

        $user = User::find($otpData['user_id']);
        if (!$user) {
            return back()->withErrors(['Could not find user']);
        }

        // ✅ Case 1: User is creating an ad
        if ($ads = session('ads')) {
            $profile = Profile::where('user_id', $user->id)->first();
            if (!$profile) {
                return back()->withErrors(['Profile not found']);
            }

            $box  = rand(100000, 999999);

            $profile = Profile::where('user_id', $user->id)->first();
            $isFeatured = UserPackage::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();

            // --- Generate witty title ---
            $prompt = $this->generateAdPrompt($profile, $ads['description']);
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a witty personal ad writer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $title = trim($response['choices'][0]['message']['content'] ?? 'Seeking someone special');
            $title = preg_replace('/^["“]|["”]$/u', '', $title);
            $title = str_replace('!', '', $title);
            $title = trim($profile->display_name.' '.$ads['location'].' '.$title);

            // Ensure slug is unique
            $slug = Str::slug($title).'-'.$box;
            $originalSlug = $slug;
            $count = 1;
            while (Ad::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }

            Ad::create([
                'user_id'             => $user->id,
                'description'         => $ads['description'],
                'title'               => $title,
                'slug'                => $slug,
                'snapshot_name'       => $profile->display_name,
                'snapshot_occupation' => $profile->occupation,
                'snapshot_age'        => $profile->age,
                'snapshot_status'     => $profile->status,
                'snapshot_gender'     => $profile->gender,
                'location'            => $profile->location,
                'views'               => 0,
                'box_number'          => $box,
            ]);

            Auth::login($user);
            $request->session()->forget(['ads', 'otp']);
            return redirect()->route('ad.writing', ['box' => $box]);
        }

        // ✅ Case 2: User is replying to an ad
        if ($reply = session('reply')) {
            $ad = Ad::find($reply['ad_id']);
            if (!$ad) {
                return back()->withErrors(['Ad not found']);
            }

            $author = $ad->user;

            $conversation = Conversation::firstOrCreate(
                [
                    'ad_id'      => $ad->id,
                    'author_id'  => $ad->user_id,
                    'replier_id' => $user->id,
                ],
                [
                    'progress'       => '0%',
                    'unlocked_photo' => false,
                ]
            );

            // Create new message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'content'         => $reply['content'],
            ]);

            // Update conversation progress
            $messageCount = $conversation->messages()->count();
            $progress     = match (true) {
                $messageCount >= 8 => '100%',
                $messageCount >= 6 => '75%',
                $messageCount >= 4 => '50%',
                $messageCount >= 2 => '25%',
                default            => '0%',
            };
            $conversation->update([
                'progress'       => $progress,
                'unlocked_photo' => $messageCount >= 3,
            ]);

            // Send email notification
            if ($author->email) {
                Mail::send('mail.reply', [
                    'name'    => $author->name,
                    'content' => $reply['content'],
                ], function ($message) use ($author) {
                    $message->to($author->email)->subject('You just got a reply!');
                });
            }

            Auth::login($user);
            $request->session()->forget(['reply', 'otp']);
            return redirect()->route('reply_confirmation');
        }

        // ✅ Case 3: Plain login after OTP verification
        $user->update(['phone_verified_at' => now()]);
        Auth::login($user);

        $request->session()->forget('otp');
        return redirect()->route('home');
    }

    protected function generateAdPrompt(Profile $profile, string $description): string
    {
        return "
        You are writing humorous and quirky personal ad titles, like in the book 'They Call Me Naughty Lola'.
        Examples:
        - Tonight, female readers to 90, I am the hunter and you are my quarry.
        - If we share a bath together I have to insist on wearing verruca socks.
        - I’ll see you at the singles night.

        Rules:
        - Output only ONE short title (max 8 words).
        - Do not use quotation marks or exclamation marks.
        - No explanations.

        Profile:
        Name: {$profile->display_name}
        Age: {$profile->age}
        Gender: {$profile->gender}
        Location: {$profile->location}
        Status: {$profile->status}
        Occupation: {$profile->occupation}
        Description: {$description}
        ";
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
        Mail::send('mail.verify-code-2', ['code' => $code], function ($message) use ($email) {
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
            Mail::send('mail.verify-code-2', ['code' => $code], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your Email Verification Code');
            });

            return redirect()->route('profile.verify_email');

        } else {
            return redirect()->route('profile.email')->with('error', 'The email is already exists!');
        }
    }

    protected function send_phone_verification($phone_number)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $serviceSid = env('TWILIO_VERIFY_SERVICE_SID');

        $twilio = new Client($sid, $token);

        $twilio->verify->v2->services($serviceSid)
            ->verifications
            ->create($phone_number, "sms");
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'code' => 'required|string',
        ]);

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $serviceSid = env('TWILIO_VERIFY_SERVICE_SID');

        $twilio = new Client($sid, $token);

        $verification_check = $twilio->verify->v2->services($serviceSid)
            ->verificationChecks
            ->create([
                'to' => $request->phone_number,
                'code' => $request->code,
            ]);

        if ($verification_check->status === "approved") {
            // Phone verified
            return redirect()->route('home')->with('success', 'Phone verified!');
        } else {
            return back()->withErrors(['code' => 'Invalid verification code']);
        }
    }
}
