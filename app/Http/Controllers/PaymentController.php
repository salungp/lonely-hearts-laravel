<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use App\Models\Payment;
use App\Models\UserPackage;

class PaymentController extends Controller
{
    public function show(): View
    {
        if (!Auth::check()) {
            abort(404);
        }

        $payment = Payment::with('userPackage.package')->where('user_id', Auth::id())->get();
        return view('payment.show', ['payment' => $payment]);
    }

    public function cancel($id)
    {
        $package = UserPackage::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $package->update(['status' => 'cancelled']);

        return back()->with('success', 'Your package has been cancelled.');
    }

}
