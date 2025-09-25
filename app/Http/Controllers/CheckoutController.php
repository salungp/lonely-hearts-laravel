<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Payment;
use App\Models\UserPackage;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function checkout(Request $request, $packageId)
    {
        $package = Package::findOrFail($packageId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'], // don't include apple_pay/google_pay directly
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $package->name,
                        'description' => $package->description,
                    ],
                    'unit_amount' => $package->price * 100, // cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['packageId' => $package->id]),
            'cancel_url' => route('offer'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, $packageId)
    {
        $user = $request->user();
        $package = Package::findOrFail($packageId);

        // record payment
        $payment = Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'transaction_id' => uniqid('txn_'),
        ]);

        // activate package
        UserPackage::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($package->duration_days),
            'status' => 'active',
        ]);

        return redirect()->route('offer')->with('success', 'Package purchased successfully via Stripe!');
    }
}
