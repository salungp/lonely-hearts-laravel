@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
      <h1 class="lh-title mb-3">Payments</h1>
</div>
@endsection
@section('script')
