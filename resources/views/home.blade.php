@extends('layouts.app')
@section('title', 'Home Page')
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-center" style="margin-bottom: 20px">
        <img
          style="width: 100px"
          src="{{ asset('images/envelope-icon.png') }}"
          alt="Envelope icon symbol of lonely hearts"
        />
    </div>

      <h2 class="text-center mb-3">
        Enter Box Number
      </h2>
      <form action="{{ url('/ad/check_box') }}" method="POST">
        @csrf
        <!-- PIN Input Container -->
        <div class="pin-container d-flex mb-4">
          @for ($i = 1; $i < 7; $i++)
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

        <!-- Action Buttons -->
        <div class="d-grid gap-2 mb-3">
          <button class="lh-button" id="submitBtn" disabled>
            <span
              class="spinner-border spinner-border-sm me-2 d-none"
              id="loadingSpinner"
            ></span>
            View Ad
          </button>
        </div>
      </form>

    @foreach($ads as $ad)
    <!-- Feed list -->
    <a href="{{ url($ad->slug.'.html') }}" class="lh-feed-card text-decoration-none text-dark" >
      <div class="w-100">
        <h2>
          <b class="text-uppercase">{{ $ad->snapshot_name }}, {{ $ad->snapshot_age }}, {{ $ad->snapshot_gender }}, {{ $ad->location }}, {{ $ad->snapshot_status }} .</b> {{ substr($ad->description, 0, 50) . '...' }}
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

    <a href="{{ route('feed') }}" class="lh-link" style="margin-bottom: 20px">See all ads</a>

    <div class="lh-feed-card lh-cta-card" style="display: block !important">
      <img
        src="{{ asset('images/logo.svg') }}"
        alt="Heart icon logo symbol"
        style="width: 42px"
      />
      <div style="width: 100%">
        <h3>Looking for love?</h3>
        <p class="lh-text">
          Find love the old fashioned way. No swiping, just words from the
          hearth (and soul) Exchange love letters from the heart.
        </p>
        <a href="{{ route('create_ad') }}" class="lh-button-sm">Find love</a>
      </div>
    </div>

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
    const alert = document.getElementById("alert");
    const ctaLocation = document.getElementById("ctaLocation");

    const locations = [
      "All Locations",
      "London",
      "Birmingham",
      "Manchester",
      "Leeds",
      "Sheffield",
      "Liverpool",
      "Bristol",
      "Newcastle upon Tyne",
      "Sunderland",
      "Leicester",
      "Coventry",
      "Kingston upon Hull",
      "Bradford",
      "Stoke-on-Trent",
      "Wolverhampton",
      "Nottingham",
      "Derby",
      "Southampton",
      "Portsmouth",
      "Plymouth",
      "Brighton",
      "Reading",
      "Northampton",
      "Luton",
      "Swindon",
      "Milton Keynes",
      "Oxford",
      "Cambridge",
      "York",
      "Blackpool",
      "Middlesbrough",
      "Bolton",
      "Stockport",
      "Warrington",
      "Huddersfield",
      "Preston",
      "Norwich",
      "Peterborough",
      "Exeter",
      "Chelmsford",
      "Gloucester",
      "Bath",
      "Colchester",
      "Ipswich",
      "Chester",
      "Dundee",
      "Edinburgh",
      "Glasgow",
      "Aberdeen",
      "Belfast"
    ];

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

    clickAction(".lh-alert-close", (e) => {
        alert.style.display = "none";
    });

    // Initialize setelah DOM loaded
    document.addEventListener("DOMContentLoaded", () => {
        ctaLocation.style.textDecoration = "underline";
        new BootstrapPinInput();
    });
</script>
@endsection
