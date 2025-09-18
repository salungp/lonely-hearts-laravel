@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/create/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/back.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">I'll write it</h1>

    
</div>
@endsection