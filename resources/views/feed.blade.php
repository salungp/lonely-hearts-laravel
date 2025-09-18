@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/back.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    @foreach($ads as $ad)
    <!-- Feed list -->
    <a href="{{ url('/ad/'.$ad->box_number) }}" class="lh-feed-card lh-feed-featured text-decoration-none text-dark" >
      <div>
        <h2>
          <b>{{ $ad->snapshot_name }}, {{ $ad->snapshot_age }}, {{ $ad->snapshot_gender }}, {{ $ad->location }}, {{ $ad->snapshot_status }} .</b> {{ substr($ad->description, 0, 50) . '...' }}
        </h2>

        <div class="d-flex justify-content-between align-items-center mt-2">
          <div class="d-flex align-items-center">
            <img
              src="{{ asset('images/logo.svg') }}"
              class="lh-small-icon me-2"
              alt="Love lonely heart symbol"
            />
            <span>{{ $ad->likes_count }}</span>
          </div>

          <div class="d-flex align-items-center">
            <img
              src="{{ asset('icons/eye-icon.svg') }}"
              class="lh-small-icon me-2"
              alt="View icon"
            />
            <span>{{ $ad->views }}</span>
          </div>

          <!-- Use a <button> or <div> styled to look like a share icon -->
          <div onclick="sharePost()" role="button">
            <img
              src="{{ asset('icons/share.svg') }}"
              class="lh-small-icon"
              alt="Share icon"
            />
          </div>
        </div>
      </div>
    </a>
    @endforeach

    <a href="{{ route('create_ad') }}" class="lh-feed-card lh-cta-card" style="background-color: #A34D41; border-color: #384B49; display: block !important;">
        <img src="{{ asset('images/red-bg-heart.svg') }}" alt="Heart icon logo symbol" style="width: 42px;">
        <div style="width: 100%;">
            <h3 style="color: #fff;">Looking for love?</h3>
            <p style="color: #FFF5DF; background-color: rgba(255, 255, 255, 0);">Find love the old fashioned way. No swiping, just words from the hearth (and soul) Exchange love letters from the heart.</p>
        </div>
    </a>

</div>
@endsection
