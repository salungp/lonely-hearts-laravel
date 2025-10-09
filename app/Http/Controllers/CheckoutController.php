<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Ad;
use App\Models\UserPackage;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        $packageId = $request->package;

        $package = Package::findOrFail($packageId);

        if (Auth::check()) {
            $existing = UserPackage::where('user_id', $user->id)
                ->where('package_id', $package->id)
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->first();

            if ($existing) {
                return redirect()->back()->with('info', 'You already have an active subscription.');
            }
        }

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

        $box = 123456;

        if (!Auth::check()) {
            session([
            'payment' => [
                'user_id' => null,
                'user_package_id' => $package->id,
                'amount' => $package->price,
                'status' => 'completed',
                'method' => 'stripe',
                'transaction_ref' => uniqid('txn_'),
            ],
            'user_package' => [
                'user_id' => null,
                'package_id' => $package->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($package->duration_days),
                'status' => 'active',
            ]]);

            return redirect()->route('ad.writing', ['box' => 123456]);
        }

        // activate package
        $user_package = UserPackage::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($package->duration_days),
            'status' => 'active',
        ]);

        // record payment
        $payment = Payment::create([
            'user_id' => $user->id,
            'user_package_id' => $user_package->id,
            'amount' => $package->price,
            'status' => 'completed',
            'method' => 'stripe',
            'transaction_ref' => uniqid('txn_'),
        ]);

        // Get the active user package with its related package data
        $userPackage = UserPackage::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        // Resolve the feature status (package title or default value)
        $isFeatured = $userPackage?->package?->title ?? null;

        if ($isFeatured === 'featured') {
            if (session()->has('box')) {
                Ad::where('box_number', session('box'))->update(['is_featured' => 1]);
                $box = session('box');
            }
        }

        $url = session('redirect_after_purchase', route('offer'));
        session()->forget('redirect_after_purchase');

        if (session('state') == 'reply') {
            return redirect()->route('reply_confirmation')->with('success', 'Subscription activated!');
        }

        return redirect()->route('ad.writing', ['box' => $box])->with('success', 'Subscription activated!');
    }
}
