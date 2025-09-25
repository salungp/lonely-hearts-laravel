<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Package;
use App\Models\UserPackage;
use Carbon\Carbon;

class PackageController extends Controller
{
    public function offer(): View
    {
        $link = 'ad.writing';
        $package = Package::get();
        $package_id = Package::where('id', 1)->first();
        if (session()->has('reply')) {
            $ad = DB::table('ads')->where('id', session('reply')['ad_id'])->first();
            $link = '/ad/confirmation/'.$ad->box_number;
        }
        return view('package.offer', [
            'link' => $link,
            'package' => $package,
            'package_id' => $package_id
        ]);
    }

    public function buy($id)
    {
        $user = Auth::user();
        $package = Package::findOrFail($id);

        // create payment (manual for now)
        $payment = Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'completed', // for now, auto-complete
            'payment_method' => 'manual',
            'transaction_id' => uniqid('txn_'),
        ]);

        // assign package to user
        UserPackage::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($package->duration_days),
            'status' => 'active',
        ]);

        return redirect()->route('packages.index')->with('success', 'Package purchased successfully!');
    }
}
