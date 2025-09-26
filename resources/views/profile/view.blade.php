@extends('layouts.app')
@section('title', 'Home Page')
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Account</h1>

    <div class="d-flex align-items-center mb-3" style="gap: 20px;">
        <img src="{{ asset('images/profile-empty.png') }}" alt="Profile image" style="width: 70px; border-radius: 8px">
        <div class="text-content">
            <h3 class="ad-title">{{ $user->display_name ?? '' }}, {{ $user->age ?? '' }}</h3>
            <a href="{{ route('profile.edit') }}" class="lh-link" style="padding: 0 !important; text-align: left;">Edit profile</a>
        </div>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/frame.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('profile.my_ads') }}">My Ads</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/link-icon.svg') }}" alt="Chain icon" />
        </span>
        <a href="#">Preferences</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/mail-white.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('profile.email') }}">Email address</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/dollar.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('profile.payment') }}">Payments</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/help.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('help') }}">Help</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/doc.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('toc') }}">Terms of services</a>
    </div>

    <div class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/doc.svg') }}" alt="Chain icon" />
        </span>
        <a href="{{ route('policy') }}">Privacy policy</a>
    </div>
</div>
<div class="container-sm">
    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
        @csrf
        <button class="lh-link" type="submit">Logout</button>
    </form>
</div>
@endsection