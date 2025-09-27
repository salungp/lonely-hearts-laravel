@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@php
    $userId = auth()->id();
    $liked = DB::table('like')
               ->where('ad_id', $ad->id)
               ->where('user_id', $userId)
               ->exists();
@endphp
@section('content')
<div class="container-sm">
    <div class="ad-content" style="margin-bottom: 150px">
        <h2 class="ad-title text-uppercase mb-2">
            {{ $ad->snapshot_name }} {{ $ad->location }} {{ $ad->title }}
        </h2>

        @if ($conversation >= 3)
        <div class="d-flex justify-content-center">
            <!-- Carousel -->
            <div class="carousel">
                <div class="carousel-track">
                    <div class="carousel-slide">
                        <img src="assets/ad-image.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-2.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-3.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-4.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/ad-image.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-2.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-3.png" alt="" />
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/Images/ad-detail-4.png" alt="" />
                    </div>
                </div>
                <button class="carousel-button prev">&#8249;</button>
                <button class="carousel-button next">&#8250;</button>
            </div>
            <!-- End carousel -->
        </div>
        @endif

        <p style="text-align: justify">
            {{ $ad->description }}
        </p>

        <!-- Box no -->
        <div class="d-flex justify-content-center">
            <div class="d-inline-flex lh-box-no">
                <img src="{{ asset('icons/box-no.png') }}" alt="Box icon" />
                <span>BOX No. {{ $ad->box_number }}.</span>
            </div>
        </div>

        <!-- Link ad -->
        <div class="d-flex lh-link-list">
            <span class="lh-link-icon">
                <img src="{{ asset('icons/link.svg') }}" alt="Chain icon" />
            </span>
            <a href="#" id="copyAdUrl" data-url="{{ url($ad->slug.'.html') }}">lonelyhearts.me/box/{{ $ad->box_number }}</a>
        </div>
    </div>

    <div class="bottom">
        <div class="container-sm">
            <div class="d-flex" style="gap: 16px">
                <button class="lh-button-icon" data-id="{{ $ad->id }}" id="like" style="background-color: #dd775a; border-color: #a34d41" >
                    <div id="likeIcon" class="lh-custom-icon"></div>
                </button>

                <a href="{{ url('ad/reply/'.$ad->box_number) }}" class="lh-button d-flex justify-content-center align-items-center">
                    Reply ad
                </a>

                <button class="lh-button-icon" id="shareBtn" style="background-color: #88a5a0; border-color: #5c7377" >
                    <img src="{{ asset('icons/share.svg') }}" class="lh-small-icon" alt="" />
                </button>
            </div>
        </div>
    </div>
</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        ✅ Ad URL copied to clipboard!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endsection
@section('script')
<script>
const likeIcon = document.getElementById("likeIcon");
const like = document.getElementById("like");

document.getElementById('shareBtn').addEventListener('click', async () => {
    const shareData = {
        title: "{{ $ad->location }}",
        text: "{{ Str::limit($ad->description, 100) }}",
        url: "{{ route('ad.show', ['slug' => $ad->slug]) }}"
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
            console.log('✅ Post shared successfully');
        } catch (err) {
            console.warn('❌ Share cancelled or failed:', err);
        }
    } else {
        // Fallback: Copy link if native share not supported
        navigator.clipboard.writeText(shareData.url).then(() => {
            alert("Link copied to clipboard!");
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // initialize
    likeIcon.style.backgroundSize = "cover";
    const copyBtn = document.getElementById("copyAdUrl");
    const toastEl = document.getElementById("copyToast");
    const toast = new bootstrap.Toast(toastEl);

    let tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    copyBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const url = this.getAttribute("data-url");

        navigator.clipboard.writeText(url).then(() => {
            toast.show();
        }).catch(err => {
            console.error("Failed to copy: ", err);
        });
    });
});

// Get initial state from DB (true = not liked, false = liked)
let likeState = @json(!DB::table('like')
    ->where('ad_id', $ad->id)
    ->where('user_id', auth()->id())
    ->exists());

function setLike(state) {
    if (state) {
        likeIcon.style.background = "url({{ asset('icons/heart.svg') }})";
    } else {
        likeIcon.style.background = "url({{ asset('icons/heart-fill.svg') }})";
    }
    likeIcon.style.backgroundSize = "cover";
}

// Show initial state
setLike(likeState);

like.addEventListener("click", () => {
    // Optimistic UI update (instant feedback)
    likeState = !likeState;
    setLike(likeState);

    // Send to server
    fetch(`/ad/${like.dataset.id}/toggle-like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        // Server returns the real state (liked = true/false)
        // Just in case our optimistic update was wrong
        likeState = !data.liked;
        setLike(likeState);
    })
    .catch(() => {
        // If request failed, revert state
        likeState = !likeState;
        setLike(likeState);
        alert("Failed to update like status.");
    });
});
</script>
@endsection
