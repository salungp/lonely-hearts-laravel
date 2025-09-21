@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/reply/'.$ad->box_number) }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">I'll write it</h1>

    <form action="{{ route('ad.reply_store') }}" method="POST">
        @csrf
        <input type="hidden" name="ad_id" id="ad_id" value="{{ $ad->id }}">
        <div class="position-relative mb-2">
            <input type="hidden" name="location" id="location">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="content"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own reply"
            ></textarea>

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

        @error('content')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

        <button
            class="lh-button-secondary mb-3"
            data-bs-toggle="modal"
            data-bs-target="#staySafeModal"
            type="button"
        >
                Stay Safe
        </button>

        <button class="lh-button" type="submit">Continue</button>
    </form>
</div>

<!-- Modal -->
<div
    class="modal lh-modal fade"
    id="staySafeModal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog lh-modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header lh-modal-header">
        <h1
            class="modal-title lh-modal-title fs-5"
            id="exampleModalLabel"
        >
            Stay Safe
        </h1>
        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
        ></button>
        </div>
        <div class="modal-body">
        <div class="d-flex justify-content-center mb-4">
            <img
            style="width: 88px"
            src="{{ asset('images/stay-safe.png') }}"
            alt="Stay safe warning icon"
            />
        </div>

        <h3 style="text-transform: uppercase; text-align: center">
            Stay safe while connecting with the new people. not to share
            personal data, don't send money to strangers.
        </h3>
        </div>
        <div class="modal-footer">
        <button class="lh-button" data-bs-dismiss="modal">
            got it
        </button>
        </div>
    </div>
    </div>
</div>
@endsection