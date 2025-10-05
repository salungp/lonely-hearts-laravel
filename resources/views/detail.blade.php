@extends('layouts.app')
@section('title', $ad->title)
@section('meta')
<style>
    /* Full-page overlay for particles */
    #loveLayer {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 2147483647; /* above everything */
    }
    
    /* Each floating heart */
    .love-spark {
        position: absolute;
        width: 40px;  /* 18px × 4 */
        height: 40px; /* 18px × 4 */
        opacity: 0;
    }
    
    /* Keep SVG crisp */
    .love-spark svg { display: block; width: 100%; height: 100%; }
    
    /* Fast float-up animation (1.0–1.8s each) */
    @keyframes floatUp {
      0%   { transform: translate(0, 0) scale(0.8) rotate(0deg);     opacity: 1; }
      70%  { opacity: 0.85; }
      100% { transform: translate(var(--dx, 0px), var(--dy, -200px)) 
                          scale(1.1) rotate(var(--rot, 15deg));      opacity: 0; }
    }
    
    /* Respect reduced-motion */
    @media (prefers-reduced-motion: reduce) {
      .love-spark { animation: none !important; opacity: 0 !important; }
    }
</style>    
@endsection
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@php
    $userId = auth()->id();
    $liked = DB::table('likes')
               ->where('ad_id', $ad->id)
               ->where('user_id', $userId)
               ->exists();
@endphp
@section('content')
<div class="love-animation-container"></div>
<div class="container-sm">
    <div class="ad-content" style="margin-bottom: 150px">
        @if ($ad->is_featured == 1)
            <div class="d-flex justify-content-center">
                <span style="margin-bottom: 12px;background: var(--red); border-radius: 6px;border: 2px solid var(--red-dark)" class="badge badge-success">Featured</span>
            </div>
        @endif
        <h2 style="font-size: 24px;text-align:center;font-family: 'Merriweather';text-transform:uppercase;font-weight: bold;">
            {{ $ad->title }}
        </h2>

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
                <button class="lh-button-icon like-btn" 
                        data-id="{{ $ad->id }}" 
                        style="background-color: #dd775a; border-color: #a34d41">
                    <div class="like-icon lh-custom-icon"></div>
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
    function ensureLoveLayer() {
        let layer = document.getElementById('loveLayer');
        if (!layer) {
            layer = document.createElement('div');
            layer.id = 'loveLayer';
            document.body.appendChild(layer);
        }
        return layer;
    }

    // Spawn a single pixel heart
    function spawnHeart(x, y, opts = {}) {
        const layer = ensureLoveLayer();
        const el = document.createElement('div');
        el.className = 'love-spark';

        // Position at screen coordinates
        el.style.left = x + 'px';
        el.style.top  = y + 'px';

        // Randomized drift and duration
        const dx = (Math.random() * 200 - 100);     // sideways drift
        const dy = - (160 + Math.random() * 200);   // upward distance
        const rot = (Math.random() * 40 - 20) + 'deg';
        const dur = (1000 + Math.random() * 800) + 'ms';
        const delay = (opts.delay || 0) + 'ms';

        el.style.setProperty('--dx', dx + 'px');
        el.style.setProperty('--dy', dy + 'px');
        el.style.setProperty('--rot', rot);
        el.style.animation = `floatUp ${dur} ease-out forwards`;
        el.style.animationDelay = delay;

        // pixel heart styling inline (same as your .heart)
        el.style.background = "#dd775a";
        // make the same heart ~30% of original
        el.style.width = "3px";
        el.style.height = "3px";
        el.style.boxShadow = `
            3px -3px 0 black, 6px -3px 0 black, 0px -3px 0 black,
            18px -3px 0 black, 21px -3px 0 black, 24px -3px 0 black,
            3px 0px 0 #dd775a, 6px 0px 0 #dd775a, 9px 0px 0 black, -3px 0px 0 black,
            15px 0px 0 black, 21px 0px 0 #dd775a, 24px 0px 0 #c45a41, 18px 0px 0 #dd775a, 27px 0px 0 black,
            12px 3px 0 black, 18px 3px 0 #dd775a, 21px 3px 0 #dd775a, 24px 3px 0 #dd775a, 27px 3px 0 #c45a41, 30px 3px 0 black,
            15px 3px 0 #dd775a, -6px 3px 0 black, -3px 3px 0 #dd775a, 0px 3px 0 #dd775a, 3px 3px 0 white, 6px 3px 0 #dd775a, 9px 3px 0 #dd775a,
            -6px 6px 0 black, -3px 6px 0 #dd775a, 0px 6px 0 white, 3px 6px 0 #dd775a, 6px 6px 0 #dd775a, 9px 6px 0 #dd775a,
            12px 6px 0 #dd775a, 15px 6px 0 #dd775a, 18px 6px 0 #dd775a, 21px 6px 0 #dd775a, 24px 6px 0 #dd775a, 27px 6px 0 #c45a41, 30px 6px 0 black,
            -6px 9px 0 black, -3px 9px 0 #dd775a, 0px 9px 0 #dd775a, 3px 9px 0 #dd775a, 6px 9px 0 #dd775a, 9px 9px 0 #dd775a,
            12px 9px 0 #dd775a, 15px 9px 0 #dd775a, 18px 9px 0 #dd775a, 21px 9px 0 #dd775a, 24px 9px 0 #dd775a, 27px 9px 0 #c45a41, 30px 9px 0 black,
            -6px 12px 0 black, -3px 12px 0 #dd775a, 0px 12px 0 #dd775a, 3px 12px 0 #dd775a, 6px 12px 0 #dd775a, 9px 12px 0 #dd775a,
            12px 12px 0 #dd775a, 15px 12px 0 #dd775a, 18px 12px 0 #dd775a, 21px 12px 0 #dd775a, 24px 12px 0 #dd775a, 27px 12px 0 #c45a41, 30px 12px 0 black,
            -3px 15px 0 black, 0px 15px 0 #dd775a, 3px 15px 0 #dd775a, 6px 15px 0 #dd775a, 9px 15px 0 #dd775a,
            12px 15px 0 #dd775a, 15px 15px 0 #dd775a, 18px 15px 0 #dd775a, 21px 15px 0 #dd775a, 24px 15px 0 #c45a41, 27px 15px 0 black,
            0px 18px 0 black, 3px 18px 0 #dd775a, 6px 18px 0 #dd775a, 9px 18px 0 #dd775a,
            12px 18px 0 #dd775a, 15px 18px 0 #dd775a, 18px 18px 0 #dd775a, 21px 18px 0 #c45a41, 24px 18px 0 black,
            3px 21px 0 black, 6px 21px 0 #dd775a, 9px 21px 0 #dd775a,
            12px 21px 0 #dd775a, 15px 21px 0 #dd775a, 18px 21px 0 #c45a41, 21px 21px 0 black,
            6px 24px 0 black, 9px 24px 0 #dd775a, 12px 24px 0 #dd775a, 15px 24px 0 #c45a41, 18px 24px 0 black,
            9px 27px 0 black, 12px 27px 0 #c45a41, 15px 27px 0 black
        `;

        // (optional) if your keyframes set transform, use only translate here
        // so animation doesn't override your size.
        el.style.transform = "translate(-50%, -50%)";
        el.style.transformOrigin = "center";

        el.addEventListener('animationend', () => el.remove());
        layer.appendChild(el);
    }

    // Burst several hearts from a button position
    function burstHeartsFromElement(el, totalMs = 5000, count = 28) {
        const rect = el.getBoundingClientRect();
        const x = rect.left + rect.width / 2;
        const y = rect.top  + rect.height / 2;

        const interval = totalMs / count;
        for (let i = 0; i < count; i++) {
            setTimeout(() => {
                spawnHeart(x, y, { delay: Math.random() * 80 });
            }, i * interval);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Share button
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

        document.querySelectorAll(".like-btn").forEach(button => {
            const adId = button.dataset.id;
            const icon = button.querySelector(".like-icon");

            let likeState = @json(
                DB::table('likes')
                    ->where('ad_id', $ad->id)
                    ->where('user_id', auth()->id())
                    ->exists()
            );

            function setLike(state) {
                icon.style.background = state
                ? "url({{ asset('icons/heart-fill.svg') }})"
                : "url({{ asset('icons/heart.svg') }})";
                icon.style.backgroundSize = "cover";
            }

            setLike(likeState);

            button.addEventListener("click", () => {
                likeState = !likeState;
                setLike(likeState);

                fetch(`/ad/${adId}/toggle-like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({})
                })
                .then(r => r.json())
                .then(data => {
                if (data.error) {
                    alert("You need to login or create account first!");
                    likeState = !likeState; // revert
                    setLike(likeState);
                    return;
                }

                likeState = data.liked;
                setLike(likeState);

                // ❤️ Only show animation when the action ends up 'liked'
                if (likeState) {
                    burstHeartsFromElement(button, 3000, 28); // ~10s show, fast particles
                }
                })
                .catch(err => {
                console.error("❌ Fetch error:", err);
                likeState = !likeState; // revert
                setLike(likeState);
                });
            });
            });
    });
</script>    
@endsection
