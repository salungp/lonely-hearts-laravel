<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Package extends Controller
{
    public function offer(): View
    {
        $link = 'ad.writing';
        $ad = DB::table('ads')->where('id', session('reply')['ad_id'])->first();
        if (session()->has('reply')) {
            $link = '/ad/confirmation/'.$ad->box_number;
        }
        return view('package.offer', [
            'link' => $link,
            ''
        ]);
    }
}
