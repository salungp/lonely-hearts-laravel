@extends('layouts.app')
@section('title', 'Home Page')
@section('meta')
<style>
      .box-confirmation-header-green {
        padding: 16px;
        background: var(--green);
        text-align: center;
        text-transform: uppercase;
        font-size: 24px;
        border: 2px solid var(--green-dark);
        border-bottom: none;
      }

      .box-confirmation-footer-light {
        padding: 16px;
        text-transform: uppercase;
        border: 2px solid var(--dark);
        border-top: none;
        text-align: center;
        font-size: 20px !important;
      }
</style>
@endsection
@section('back')
<a href="{{ url('/ad/create/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/back.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Confirmation</h1>

    <div class="box-confirmation">
        <div class="box-confirmation-header-green">
            <span>Your reply has been sent!</span>
        </div>
        <div class="box-confirmation-icon">
            <img src="{{ asset('images/envelope-icon.png') }}" alt="Love mail icon to represent the icon">
        </div>
        <div class="box-confirmation-footer-light">
            <h2>We HOPE YOUR HEARTS & SOULS ALIGN</h2>
        </div>
    </div>
</div>
@endsection