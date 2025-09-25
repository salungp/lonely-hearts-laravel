<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class PaymentController extends Controller
{
    public function show(): View
    {
        return view('payment.show');
    }
}
