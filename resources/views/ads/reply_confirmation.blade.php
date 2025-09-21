@extends('layouts.app')
@section('title', 'Home Page')
@section('meta')
<style>
      .box-confirmation-header-green {
        padding: 10px 16px;
        background: var(--green);
        text-align: center;
        text-transform: uppercase;
        font-size: 24px;
        border: 2px solid var(--green-dark);
        color: #fff;
      }

      .box-confirmation-icon {
        border-top: none !important;
        border-bottom: none !important;
      }

      .box-confirmation-footer-light {
        padding: 16px;
        text-transform: uppercase;
        border: 2px solid var(--dark);
        border-top: none;
        text-align: center;
        font-size: 20px !important;
      }

      .box-number-title {
        background: var(--green-dark);
        color: #fff;
        text-align: center;
        height: 40px;
        line-height: 40px;
        font-size: 24px;
        text-transform: uppercase;
      }

      .box-number-digit {
        text-align: center;
        color: #fff;
        font-size: 46px;
        padding: 10px;
        background: var(--green);
        border: 2px solid var(--green-dark);
        letter-spacing: 6px;
      }

      .box-number-footer {
        text-align: center;
        font-size: 20px;
        text-transform: uppercase;
        padding: 20px;
        border: 2px solid var(--dark);
      }
</style>
@endsection
@section('back')
<a href="{{ url()->previous() }}" class="lh-nav-button">
    <img src="{{ asset('icons/arrow-left-bold.svg') }}" alt="Icon back button" />
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
        <div class="box-number-footer">
            We HOPE YOUR HEARTS & SOULS ALIGN
        </div>
    </div>

    <div class="bottom">
        <div class="container-sm">
            <div class="d-flex" style="gap: 16px">
                
                <a href="{{ route('profile.email') }}" class="lh-button d-flex justify-content-center align-items-center">
                    Notify me
                </a>

            </div>
        </div>
    </div>
</div>
@endsection