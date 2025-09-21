@extends('layouts.app')
@section('title', 'Home Page')
@section('meta')
<style>
      .box-confirmation-header-red {
        padding: 10px 16px;
        background: var(--red);
        text-align: center;
        text-transform: uppercase;
        font-size: 24px;
        border: 2px solid var(--red-dark);
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
</style>
@endsection
@section('back')
<a href="{{ url('/ad/create/') }}" class="lh-nav-button">
    <img src="{{ asset('icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Confirmation</h1>

    <div class="box-confirmation">
        <div class="box-confirmation-header-red">
            <span>Your ad is live!</span>
        </div>
        <div class="box-confirmation-icon">
            <img src="{{ asset('images/envelope-icon.png') }}" alt="Love mail icon to represent the icon">
        </div>
        <div class="box-number-title">
            Box number
        </div>
        <div class="box-number-digit">
            {{ $ad->box_number }}
        </div>
    </div>

    <div class="bottom">
        <div class="container-sm">
            <div class="d-flex" style="gap: 16px">
                <button class="lh-button-icon" id="like" style="background-color: #dd775a; border-color: #a34d41" >
                    <img src="{{ asset('icons/edit.svg') }}" class="lh-small-icon" alt="" />
                </button>

                <a href="{{ url($ad->slug.'.html') }}" class="lh-button d-flex justify-content-center align-items-center">
                    Check it
                </a>

                <button class="lh-button-icon" id="shareBtn" style="background-color: #88a5a0; border-color: #5c7377" >
                    <img src="{{ asset('icons/share.svg') }}" class="lh-small-icon" alt="" />
                </button>
            </div>
        </div>
    </div>
</div>
@endsection