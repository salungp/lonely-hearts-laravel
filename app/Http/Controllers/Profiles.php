<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Ad;
use App\Models\User;
use App\Models\Profile;
use App\Models\Package;
use App\Models\UserPackage;

class Profiles extends Controller
{
    public function create(): View
    {
        return view('profile.create');
    }

    public function profile(): View
    {
        $id = Auth::id();
        $user = Profile::where('user_id', $id)->first();
        $isFeatured = UserPackage::where('user_id', $id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();

        return view('profile.view', [
            'user' => $user,
            'is_featured' => $isFeatured
        ]);
    }

    public function email(): View
    {
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        return view('profile.email', [
            'user' => $user
        ]);
    }

    public function my_ads(): View
    {
        $id = Auth::id();

        $ads = Ad::where('user_id', $id)->get();

        return view('profile.my_ads', ['ads' => $ads]);
    }

    public function profile_edit(): View
    {
        $id = Auth::id();
        $user = Profile::where('user_id', $id)->first();

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

        Profile::where('user_id', $id)->update([
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
        // Clear session if user came from create flow
        session()->forget(['profile', 'ads']);
        
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

        // --- CASE 1: User came from forced login (must create profile) ---
        if (session()->has('must_create_profile')) {
            $user = Auth::user(); // ✅ safe now
        
            $userId = Auth::id();
            if (!$userId) {
                return back()->withErrors(['User not found, please login again.']);
            }

            $user = User::findOrFail($userId);
            $user->update(['name' => $request->person_name]);
        
            Profile::create([
                'user_id'      => $user->id,
                'display_name' => $validated['person_name'],
                'occupation'   => $validated['occupation'],
                'age'          => $validated['age'],
                'status'       => $validated['status'],
                'gender'       => $validated['gender'],
                'location'     => session('ads')['location'] ?? 'Unknown',
                'bio'          => '',
            ]);
        
            $request->session()->forget('must_create_profile');
        
            return redirect()->route('auth.verify');
        }        

        // --- CASE 2: User hasn’t registered yet (profile saved in session before creating user) ---
        session([
            'profile' => [
                'display_name' => $validated['person_name'],
                'occupation'   => $validated['occupation'],
                'age'          => $validated['age'],
                'status'       => $validated['status'],
                'gender'       => $validated['gender'],
            ],
        ]);

        // Redirect back to intended flow (ad creation, reply, or home)
        $redirectTo = session()->pull('intended_url', route('home'));

        return redirect($redirectTo);
    }
}
