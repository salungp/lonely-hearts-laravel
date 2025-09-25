@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('login') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1
        class="lh-title text-start w-100 mb-3"
        >
        Verification code
    </h1>

    <p class="mb-3">Your verification code <strong>{{ session('otp')['otp'] }}</strong></p>

    <form action="{{ route('auth.verify_code') }}" method="POST">
        @csrf
        <!-- PIN Input Container -->
        <div class="pin-container d-flex mb-4">
          @for ($i = 1; $i < 6; $i++)
            <input
              type="text"
              class="text-center lh-input pin-input-field"
              maxlength="1"
              data-index="{{ $i }}"
              name="{{ 'box_'.$i }}"
              inputmode="numeric"
            />
          @endfor
        </div>

        @if (session('error'))
          <div class="lh-alert mb-3 lh-alert-error" id="alert">
            {{ session('error') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
        @endif

        <button class="lh-button" id="submitBtn" type="submit">Continue</button>

    </form>
</div>
@endsection
@section('script')
<script>
    const alert = document.getElementById("alert");
    // Initialize setelah DOM loaded
    document.addEventListener("DOMContentLoaded", () => {
        new BootstrapPinInput();
    });

    clickAction(".lh-alert-close", (e) => {
        alert.style.display = "none";
    });
</script>
@endsection