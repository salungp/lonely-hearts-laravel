@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
  <a href="{{ route('create_ad') }}" class="lh-link mb-2">Looking for ♥️</a>
    @foreach($ads as $ad)
    <!-- Feed list -->
    <a href="{{ url($ad->slug.'.html') }}" class="lh-feed-card text-decoration-none text-dark" >
      <div>
        <h2>
          <b class="text-uppercase">{{ $ad->title }} .</b> {{ substr($ad->description, 0, 50) . '...' }}
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
<!-- Location pop up -->
<div class="lh-popup" id="locationPopup">
  <div class="lh-popup-header">
    <button id="closePopupLocation">
      <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
    </button>
  </div>
  <div class="lh-popup-body">
    <h2 class="lh-title mb-3" style="text-align: left">Change location</h2>
    <div class="location-field">
      <input type="text" id="searchInput" placeholder="Search city..." class="input-none" />
      <button class="current-location-btn">
        <img src="{{ asset('icons/search.svg') }}" alt="Search svg icon" />
      </button>
    </div>
    <ul id="locationList"></ul>
  </div>
</div>
@endsection
@section('script')
<script>
    const locationList = document.getElementById("locationList");
    const ctaLocation = document.getElementById("ctaLocation");

    function renderLocations(list) {
        locationList.innerHTML = "";
        list.forEach((loc) => {
            const li = document.createElement("li");
            li.textContent = loc;
            li.addEventListener("click", () => {
                // Special case: reset location if "All Locations" is chosen
                const payload = loc === "All Locations" ? { location: null } : { location: loc };

                fetch("{{ route('location.set') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify(payload),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        locationPopup.classList.remove("active");
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Error setting location:", err));
            });
            locationList.appendChild(li);
        });
    }

    clickAction(".lh-location", (el) => {
        locationPopup.classList.add("active");
        renderLocations(locations);
    });

    clickAction("#closePopupLocation", (el) => {
        locationPopup.classList.remove("active");
    });

    // Initialize setelah DOM loaded
    document.addEventListener("DOMContentLoaded", () => {
        ctaLocation.style.textDecoration = "underline";
    });
</script>
@endsection
