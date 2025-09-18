<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home;
use App\Http\Controllers\Ads;
use App\Http\Controllers\Profiles;
use App\Http\Controllers\Package;
use App\Http\Controllers\AuthController;

// Home
Route::get('/', [Home::class, 'show'])->name('home');
Route::get('/feed', [Home::class, 'feed'])->name('feed');
Route::post('/set-location', [Ads::class, 'filter_location'])->name('location.set');

Route::middleware(['profile.or.create'])->group(function () {
    Route::get('/ad/create_first/', [Ads::class, 'create_first']);
    Route::get('/ad/create_second/', [Ads::class, 'create_second']);
    Route::get('/ad/reply_first/{box}', [Ads::class, 'reply_first']);
});

// Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login_or_register', [AuthController::class, 'LoginOrRegister'])->name('login_or_register');
Route::get('/verify', [AuthController::class, 'verify_code'])->name('auth.verify');
Route::post('/verify_code', [AuthController::class, 'verifyOtp'])->name('auth.verify_code');

Route::middleware(['web', 'ensure.auth'])->group(function () {
    Route::get('/ad/writing/', [Ads::class, 'writing'])->name('ad.writing');
});

// Ad routes
Route::get('/ad/create/', [Ads::class, 'create_ad'])->name('create_ad');
Route::post('/ad/store/', [Ads::class, 'store'])->name('create.store');
Route::post('/ad/check_box', [Home::class, 'check_box']);
Route::get('/ad/reply/{box}', [Ads::class, 'reply']);
Route::post('/ad/{ad}/toggle-like', [Ads::class, 'toggleLike'])
     ->middleware('auth')
     ->name('ad.toggleLike');
Route::get('/ad/{box}', [Home::class, 'detail']);

// Profile
Route::get('/account', [Profiles::class, 'profile'])->name('profile.view')->middleware('auth');
Route::get('/account/profile', [Profiles::class, 'profile_edit'])->name('profile.edit')->middleware('auth');
Route::get('/account/create', [Profiles::class, 'create'])->name('profile.create');
Route::post('/account/store', [Profiles::class, 'store'])->name('profile.store');


// Package
Route::get('/offer', [Package::class, 'offer'])->name('offer');

// Set up for 404 page
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/{slug}.html', [Home::class, 'detail_slug'])->name('ad.show');