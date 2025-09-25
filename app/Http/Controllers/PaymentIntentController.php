<?php

// app/Http/Controllers/PaymentIntentController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentIntentController extends Controller
{
    public function create(Request $request, Package $package)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $package->price * 100, // in cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true], // allows Apple Pay, Google Pay, cards
        ]);

        return response()->json([
            'clientSecret' => $intent->client_secret,
        ]);
    }
}

