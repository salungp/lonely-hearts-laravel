<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home;
use App\Http\Controllers\Ads;
use App\Http\Controllers\Profiles;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\PaymentController;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Ad;
use App\Http\Controllers\PhoneVerificationController;

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')->setPriority(1.0));

    $ads = Ad::all();
    foreach ($ads as $ad) {
        $sitemap->add(
            Url::create("/ads/{$ad->id}")
                ->setLastModificationDate($ad->updated_at)
        );
    }
    return $sitemap->toResponse(request());
});

// Home
Route::get('/', [Home::class, 'show'])->name('home');
Route::get('/feed', [Home::class, 'feed'])->name('feed');
Route::post('/set-location', [Ads::class, 'filter_location'])->name('location.set');
Route::get('/my-location', [Home::class, 'show_location']);

// Check when create/reply ad
Route::middleware(['profile.or.create'])->group(function () {
    Route::get('/ad/create_first/', [Ads::class, 'create_first']);
    Route::get('/ad/create_second/', [Ads::class, 'create_second']);
    Route::get('/ad/reply_first/{box}', [Ads::class, 'reply_first'])->name('ad.reply_first');
    Route::get('/ad/reply_second/{box}', [Ads::class, 'reply_second'])->name('ad.reply_second');
});

// Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login_or_register', [AuthController::class, 'LoginOrRegister'])->name('login_or_register');
Route::get('/verify', [AuthController::class, 'verify_code'])->name('auth.verify');
Route::post('/verify_code', [AuthController::class, 'verifyOtp'])->name('auth.verify_code');
Route::post('/otp/request', [PhoneVerificationController::class, 'requestOtp'])->name('otp.request');
Route::post('/otp/verify', [PhoneVerificationController::class, 'verifyOtp'])->name('otp.verify');

Route::middleware(['web', 'ensure.auth'])->group(function () {
    Route::get('/ad/writing/{box}', [Ads::class, 'writing'])->name('ad.writing');
    Route::get('/ad/confirmation/{box}', [Ads::class, 'confirmation'])->name('ad.confirmation');
});

// Ad routes
Route::get('/ad/create/', [Ads::class, 'create_ad'])->name('create_ad');
Route::get('/ad/confirmation/', [Ads::class, 'reply_confirmation'])->name('reply_confirmation');
Route::post('/ad/store/', [Ads::class, 'store'])->name('create.store');
Route::post('/ad/reply_store/', [Ads::class, 'reply_store'])->name('ad.reply_store');
Route::post('/ad/check_box', [Home::class, 'check_box']);
Route::get('/ad/reply/{box}', [Ads::class, 'reply']);
Route::post('/ad/update/', [Ads::class, 'update'])->name('ads.update');
Route::delete('/ad/delete/{id}', [Ads::class, 'destroy'])->name('ads.destroy');
Route::post('/ad/{ad}/toggle-like', [Ads::class, 'toggleLike'])
     ->middleware('auth')
     ->name('ad.toggleLike');
Route::post('/ad/{ad}/toggle-like', [Ads::class, 'toggleLike'])
    ->name('ad.toggle-like');
Route::post('/ad/apply-style', [Ads::class, 'apply_style']);
Route::get('/ad/{box}', [Home::class, 'detail'])->name('ad.detail');
Route::post('/ad', [Home::class, 'check_box']);

// Test mail
// Route::get('/test-mail', function () {
//     Mail::send('mail.verify-code-2', ['code' => 34580], function ($message) {
//         $message->to('salung@22-22.co')
//                 ->subject('Test Email');
//     });

//     return 'Test email sent!';
// });

// Profile
Route::get('/account', [Profiles::class, 'profile'])->name('profile.view')->middleware('auth');
Route::get('/account/profile', [Profiles::class, 'profile_edit'])->name('profile.edit')->middleware('auth');
Route::get('/account/create', [Profiles::class, 'create'])->name('profile.create');
Route::get('/account/my_ads', [Profiles::class, 'my_ads'])->name('profile.my_ads')->middleware('auth');
Route::get('/account/ads/edit/{id}', [Ads::class, 'ad_edit'])->name('profile.ad_edit')->middleware('auth');
Route::post('/account/store', [Profiles::class, 'store'])->name('profile.store');
Route::post('/account/update', [Profiles::class, 'update'])->name('profile.update')->middleware('auth');

// Payment
Route::get('/account/payment', [PaymentController::class, 'show'])->name('profile.payment');

// Message
Route::get('/message', [MessageController::class, 'show'])->name('message')->middleware('auth');
Route::get('/message/sent', [MessageController::class, 'sent'])->name('message.sent')->middleware('auth');
Route::get('/message/{sender_Id}', [MessageController::class, 'show_by_sender']);
Route::middleware('auth')->get('/conversations/{conversationId}/messages', [MessageController::class, 'conversationMessages']);
Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);
Route::get('/conversations/{receiver}/sent-messages', [MessageController::class, 'sent_messages']);

// Site links
Route::get('/what-is-lonely-hearts', function() {
    return view('links.about');
})->name('about');
Route::get('/terms-of-service', function() {
    return view('links.terms_of_service');
})->name('toc');
Route::get('/privacy-policy', function() {
    return view('links.privacy_policy');
})->name('policy');
Route::get('/how-it-works', function() {
    return view('links.how_it_works');
})->name('how_it_works');
Route::get('/help', function() {
    return view('links.help');
})->name('help');

// Profile email
Route::get('/account/verify-email', [Profiles::class, 'verify_email'])->name('profile.verify_email')->middleware('auth');
Route::get('/account/email', [Profiles::class, 'email'])->name('profile.email')->middleware('auth');
Route::post('/account/verify-email-code', [Profiles::class, 'verify_email_code'])->name('profile.verify_email_code');
Route::post('/account/email_update', [AuthController::class, 'update_email'])->name('profile.email_update');

// Package
Route::get('/offer', [PackageController::class, 'offer'])->name('offer');
Route::post('/packages/{id}/buy', [PackageController::class, 'buy'])->name('packages.buy');

// Checkout
Route::post('/checkout/{id}', [CheckoutController::class, 'checkout'])->name('checkout.start');
Route::get('/checkout/success/{packageId}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/create-payment-intent/{package}', [PaymentIntentController::class, 'create'])
    ->name('payment.intent.create');

// Set up for 404 page
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/{slug}.html', [Home::class, 'detail_slug'])->name('ad.show');