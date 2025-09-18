@extends('layouts.app')
@section('title', 'Home Page')
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-center mb-4">
        <img style="width: 180px;" src="{{ asset('images/pixel-heart.png') }}" alt="Pixel heart on lost page">
    </div>

    <h2 class="lh-title text-center mb-3">You’re lost</h2>

    <p class="lh-text text-center mb-3">
        We got tried of dating apps, swiping instead of connecting on a deeper level. Words are powerful, so powerful in.
    </p>

    <div class="bottom">
        <div class="container-sm">
            <a href="{{ route('home') }}" class="lh-button d-flex justify-content-center align-items-center">
                Find love
            </a>
        </div>
    </div>
</div>
@endsection