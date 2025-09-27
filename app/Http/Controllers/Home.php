<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Home extends Controller
{
    public function show_location(Request $request)
    {
        $ip = $request->ip();

        // Localhost fallback
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '1.1.1.1'; // Cloudflare public IP
        }

        $response = Http::get("https://ipapi.co/{$ip}/json/");

        if ($response->failed()) {
            return [
                'ip'      => $ip,
                'country' => 'United Kingdom',
                'city'    => 'London',
                'lat'     => 51.5072,
                'lon'     => -0.1276,
            ];
        }

        $data = $response->json();

        // If city/country missing, fallback
        if (empty($data['city']) || empty($data['country_name'])) {
            return [
                'ip'      => $ip,
                'country' => 'United Kingdom',
                'city'    => 'London',
                'lat'     => 51.5072,
                'lon'     => -0.1276,
            ];
        }

        return [
            'ip'      => $ip,
            'country' => $data['country_name'],
            'city'    => $data['city'],
            'lat'     => $data['latitude'] ?? null,
            'lon'     => $data['longitude'] ?? null,
        ];
    }

    
    public function show(): View
    {
        $query = DB::table('ads')
            ->leftJoin('like', 'ads.id', '=', 'like.ad_id') // ✅ make sure table is correct
            ->select('ads.*', DB::raw('COUNT(like.id) as likes_count'))
            ->groupBy('ads.id');

        // Filter by selected location (if exists)
        if (session()->has('selected_location')) {
            $query->where('ads.location', session('selected_location'));
        }

        // Clear session if user came from create flow
        session()->forget(['profile', 'ads']);

        $ads = $query->limit(6)->orderBy('ads.created_at', 'desc')->get();

        // Cache ads feed (optionally include location in cache key)
        $cacheKey = 'ads_feed_' . (session('selected_location') ?? 'all');
        Cache::put($cacheKey, $ads, now()->addSeconds(10));

        return view('home', [
            'ads' => $ads,
            'selectedLocation' => session('selected_location'), // so you can show it in view
        ]);
    }

    public function feed(): View
    {
        $ads = DB::table('ads')
                    ->leftJoin('like', 'ads.id', '=', 'like.ad_id')
                    ->select('ads.*', DB::raw('COUNT(like.id) as likes_count'))
                    ->orderBy('ads.created_at', 'desc')
                    ->groupBy('ads.id')
                    ->get();
    
        Cache::put('ads_feed', $ads, now()->addSeconds(10));

        return view('feed', [
            'ads' => $ads
        ]);
    }

    public function detail_slug($slug): View
    {
        $ad = DB::table('ads')->where('slug', $slug)->first();

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
        $ad = DB::table('ads')->where('box_number', $box)->first();

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

        $ad = DB::table('ads')->where('box_number', $box)->first();

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
