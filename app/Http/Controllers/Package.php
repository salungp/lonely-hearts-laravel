<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class Package extends Controller
{
    public function offer(): View
    {
        return view('package.offer');
    }
}
