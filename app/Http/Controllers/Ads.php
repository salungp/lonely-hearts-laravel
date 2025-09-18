<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Ads extends Controller
{
    public function reply($box): View
    {
        return view('ads.reply', [
            'box' => $box
        ]);
    }

    public function filter_location(Request $request)
    {
        if (is_null($request->location)) {
            session()->forget('selected_location');
        } else {
            $request->validate([
                'location' => 'required|string|max:100',
            ]);
            session(['selected_location' => $request->location]);
        }
    
        return response()->json(['success' => true]);
    }

    public function reply_first($box): View
    {
        return view('ads.reply_first', [
            'box' => $box
        ]);
    }

    public function reply_second($box): View
    {
        return view('ads.reply_second', [
            'box' => $box
        ]);
    }

    public function create_ad(): View
    {
        return view('ads.create');
    }

    public function create_first(): View
    {
        return view('ads.create_first');
    }

    public function create_second(): View
    {
        return view('ads.create_second');
    }

    public function writing(): View
    {
        return view('ads.writing', [
            'redirectUrl' => route('home'), // or any route you want
            'delay' => 5000, // delay in milliseconds
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'location'    => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            // Save to session
            session([
                'ads' => [
                    'description'  => $validated['description'],
                    'location'     => $request->location
                ],
            ]);

            return redirect()->route('offer');
        } else {
            $user_id = Auth::id();
            $profile = DB::table('table_profiles')->where('user_id', $user_id)->first();
            $box = rand(100000, 999999);
            $slug = Str::slug($profile->location.' '.$profile->display_name.' '.$profile->occupation.' '.$profile->status.' '.$box);

            if ($profile) {
                DB::table('ads')->insert([
                    'user_id'               => $user_id,
                    'description'           => $validated['description'],
                    'slug'                  => $slug,
                    'snapshot_name'         => $profile->display_name,
                    'snapshot_occupation'   => $profile->occupation,
                    'snapshot_age'          => $profile->age,
                    'snapshot_status'       => $profile->status,
                    'snapshot_gender'       => $profile->gender,
                    'location'              => $profile->location,
                    'views'                 => 0,
                    'box_number'            => $box,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                return redirect()->route('ad.writing');
            }
        }
    }

    public function toggleLike($adId)
    {
        $userId = Auth::id();

        $liked = DB::table('like')
                   ->where('ad_id', $adId)
                   ->where('user_id', $userId)
                   ->exists();

        if ($liked) {
            DB::table('like')
              ->where('ad_id', $adId)
              ->where('user_id', $userId)
              ->delete();
        } else {
            DB::table('like')->insert([
                'ad_id' => $adId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'liked' => !$liked,
        ]);
    }
}
