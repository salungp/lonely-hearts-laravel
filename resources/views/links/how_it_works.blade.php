@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
    <div class="container-sm">
        <h1 class="lh-title mb-3">HOW DO I USE LONELY HEARTS TO FIND LOVE?</h1>
        <p style="text-align: justify; font-family: 'Merriweather'; line-height: 200%;">You create an ad in a way that reflects your personality and soul with words that are meaningful to you and that show your personality. You don’t have to upload a photo. If you do, it will slowly becoming clearer only as exchange love letters and it’s going in the right direction. We give you a Box Number.<br> <br>Anyone can reply to your ad. You exchange love letters, if you connect and you have a photo.</p>
    </div>
@endsection
