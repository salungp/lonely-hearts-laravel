@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
    <div class="container-sm">
        <h1 class="lh-title mb-3">Terms of condition</h1>
        <p style="text-align: justify; font-family: 'Merriweather'; line-height: 200%;">We got tried of dating apps, swiping instead of connecting on a deeper level. Words are powerful, so powerful in fact, that from 1967 to xxxx it was the way to find love and a great connection with soul. <br> <br> In an always on connected world we wanted to bring back the humour and nostalgic feeling of connecting on a different level. We hope you find your lonely heart so it’s not so lonely anymore.</p>
    </div>
@endsection
