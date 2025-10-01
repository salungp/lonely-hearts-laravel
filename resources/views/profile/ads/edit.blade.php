@extends('layouts.app')
@section('title', 'My Ads | Edit')
@section('back')
<a href="{{ route('profile.my_ads') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Edit ad</h1>

    @if (session('error'))
        <div class="lh-alert mb-3 lh-alert-error" id="alert">
        {{ session('error') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    @if (session('success'))
        <div class="lh-alert mb-3 lh-alert-success" id="alert">
        {{ session('success') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    <form action="{{ route('ads.update') }}" method="POST">
        @csrf
        <div class="position-relative mb-2">
            <input type="hidden" name="id" id="id" value="{{ $ad->id }}">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="description"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own ad"
            >{{ $ad->description }}</textarea>

            <div class="lh-textarea-info">
                <span id="lh-textarea-info">0</span>/300
            </div>

            <button
                class="d-flex lh-box-no position-absolute"
                style="bottom: 6px; left: 16px; z-index: 99"
                id="showPopup">
            Help me write
            </button>
        </div>

        @error('description')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

        <button class="lh-button" type="submit">Update</button>
    </form>
</div>
@endsection
@section('script')
<script>
    const alert = document.getElementById("alert");
    
    clickAction(".lh-alert-close", (e) => {
        alert.style.display = "none";
    });
</script>
@endsection