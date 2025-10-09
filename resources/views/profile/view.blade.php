@extends('layouts.app')
@section('title', 'Account')
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Account</h1>

    <div class="d-flex align-items-center mb-3" style="gap: 20px;">
        <img src="{{ asset('images/profile-empty.png') }}" alt="Profile image" style="width: 70px; border-radius: 8px">
        <div class="text-content">
            <h3 style="font-size: 24px;text-transform:uppercase;">{{ $user->display_name ?? '' }}, {{ $user->age ?? '' }}
                @if ($is_featured == 'featured')
                    <span style="margin-left: 4px;background: var(--red); border-radius: 6px;border: 2px solid var(--red-dark)" class="badge badge-success">Featured</span>
                @endif
                @if ($is_featured == 'flower')
                    <img style="width: 30px;" src="{{ asset('images/rose.png') }}" alt="Flower icon">
                @endif
            </h3>
            <a href="{{ route('profile.edit') }}" class="lh-link" style="padding: 0 !important; text-align: left;">Edit profile</a>
        </div>
    </div>

    <a href="{{ route('profile.my_ads') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/frame.svg') }}" alt="Chain icon" />
        </span>
        <span>My Ads</span>
    </a>

    <a href="#" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/link-icon.svg') }}" alt="Chain icon" />
        </span>
        <span>Preferences</span>
    </a>

    <a href="{{ route('profile.email') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
        <img src="{{ asset('icons/mail-white.svg') }}" alt="Chain icon" />
        </span>
        <span>Email address</span>
    </a>

    <a href="{{ route('profile.payment') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/dollar.svg') }}" alt="Chain icon" />
        </span>
        <span>Payments</span>
    </a>

    <a href="{{ route('help') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/help.svg') }}" alt="Chain icon" />
        </span>
        <span>Help</span>
    </a>

    <a href="{{ route('toc') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/doc.svg') }}" alt="Chain icon" />
        </span>
        <span>Terms of services</span>
    </a>

    <a href="{{ route('policy') }}" class="d-flex lh-link-list mb-3">
        <span class="lh-link-icon">
            <img src="{{ asset('icons/doc.svg') }}" alt="Chain icon" />
        </span>
        <span>Privacy policy</span>
    </a>
</div>
<div class="container-sm">
    <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
        @csrf
        <button class="lh-link" type="submit">Logout</button>
    </form>
</div>
@endsection