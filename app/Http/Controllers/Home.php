<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Ad;

class Home extends Controller
{
    public function show_location(Request $request)
    {
        $ip = $request->ip();

        // Localhost fallback
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '1.1.1.1'; // Cloudflare public IP
        }

        $response = Http::get("https://ipinfo.io/{$ip}/json");

        $data = $response->json();

        return $data;
    }

    
    public function show(): View
    {
        $query = Ad::withCount('likes'); // ✅ uses relationship instead of manual join

        // Filter by selected location (if exists)
        if (session()->has('selected_location')) {
            $query->where('location', session('selected_location'));
        }

        // Clear session if user came from create flow
        session()->forget(['profile', 'ads']);

        $ads = $query->orderByDesc('is_featured') // ✅ featured first
             ->orderByDesc('created_at')  // ✅ newest next
             ->limit(6)
             ->get();

        // Cache ads feed (optionally include location in cache key)
        $cacheKey = 'ads_feed_' . (session('selected_location') ?? 'all');
        Cache::put($cacheKey, $ads, now()->addSeconds(10));

        return view('home', [
            'ads' => $ads,
            'selectedLocation' => session('selected_location'), // for view
        ]);
    }

    public function feed(): View
    {
        $query = Ad::withCount('likes'); // ✅ uses relationship instead of manual join

        // Filter by selected location (if exists)
        if (session()->has('selected_location')) {
            $query->where('location', session('selected_location'));
        }

        // Clear session if user came from create flow
        session()->forget(['profile', 'ads']);

        $ads = $query->latest() // shorthand for orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    
        Cache::put('ads_feed', $ads, now()->addSeconds(10));

        return view('feed', [
            'ads' => $ads,
            'selectedLocation' => session('selected_location'), // for view
        ]);
    }

    public function detail_slug($slug): View
    {
        $ad = Ad::where('slug', $slug)->first();

        if (!$ad) {
            abort(404);
        }

        $sessionKey = 'viewed_ad_' . $ad->id;
        $viewedAt = session($sessionKey);

        if (!$viewedAt || now()->diffInHours($viewedAt) > 1) {
            DB::table('ads')->where('slug', $slug)->increment('views');
            session([$sessionKey => now()]);
            // re-fetch to show updated views count
            $ad = DB::table('ads')->where('slug', $slug)->first();
        }

        return view('detail', [
            'conversation' => 2,
            'ad' => $ad,
        ]);
    }

    public function detail($box): View
    {
        $ad = Ad::where('box_number', $box)->first();

        if (!$ad) {
            abort(404);
        }
        
        $sessionKey = 'viewed_ad_' . $ad->id;

        if (!session()->has($sessionKey)) {
            DB::table('ads')
                ->where('box_number', $box)
                ->increment('views');
    
            session([$sessionKey => now()]);
        }

        return view('detail', [
            'conversation' => 2,
            'ad' => $ad
        ]);
    }

    public function detail_ad(Request $request): View
    {
        $box_1 = $request->input('box_1');
        $box_2 = $request->input('box_2');
        $box_3 = $request->input('box_3');
        $box_4 = $request->input('box_4');
        $box_5 = $request->input('box_5');
        $box_6 = $request->input('box_6');

        if ($request) {
            $box = $box_1.$box_2.$box_3.$box_4.$box_5.$box_6;
        }

        $ad = Ad::where('box_number', $box)->first();

        if (!$ad) {
            abort(404);
        }
        
        $sessionKey = 'viewed_ad_' . $ad->id;

        if (!session()->has($sessionKey)) {
            DB::table('ads')
                ->where('box_number', $box)
                ->increment('views');
    
            session([$sessionKey => now()]);
        }

        return view('detail', [
            'conversation' => 2,
            'ad' => $ad
        ]);
    }

    public function check_box(Request $request)
    {
        $box_1 = $request->input('box_1');
        $box_2 = $request->input('box_2');
        $box_3 = $request->input('box_3');
        $box_4 = $request->input('box_4');
        $box_5 = $request->input('box_5');
        $box_6 = $request->input('box_6');
        $box = $box_1.$box_2.$box_3.$box_4.$box_5.$box_6;

        $ad = Ad::where('box_number', $box)->first();

        if ($ad) {
            return view('detail', [
                'ad' => $ad,
                'conversation' => 2,
            ]);
        } else {
            return redirect()->route('home')
                             ->with('error', 'Box is not found!');
        }
    }
}
