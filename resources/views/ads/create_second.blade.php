@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/create') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">

    <!-- Form start line -->
    <form action="{{ route('create.store') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="lh-alert lh-alert-error d-block mb-3">
                <strong>Whoops!</strong> Please fix the following:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <input type="hidden" name="location" id="location">

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>I'm</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="height">
            <button class="lh-dropdown-button" type="button">Tall</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Tall</div>
                <div class="lh-option">Kinda Tall</div>
                <div class="lh-option">Perfectly average</div>
                <div class="lh-option">Not too tall</div>
                <div class="lh-option">Petite</div>
            </div>
        </div>

        <div class="lh-dropdown-wrap" data-field="hair">
            <button class="lh-dropdown-button" type="button">Blue hair</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Blue hair</div>
                <div class="lh-option">Highlights</div>
                <div class="lh-option">Two-Tone</div>
                <div class="lh-option">Rainbow Hair</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>And</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="eyes">
            <button class="lh-dropdown-button" type="button">Who</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Red</div>
                <div class="lh-option">Blue</div>
                <div class="lh-option">Brown</div>
                <div class="lh-option">Black</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Eyes, Who</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="behavior">
            <button class="lh-dropdown-button" type="button">Who</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Bubbly</div>
                <div class="lh-option">Calm</div>
                <div class="lh-option">Adventurous</div>
                <div class="lh-option">Playful</div>
                <div class="lh-option">Serious</div>
                <div class="lh-option">Confident</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Seeking</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="seeking">
            <button class="lh-dropdown-button" type="button">Seeking</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Sugar Daddy</div>
                <div class="lh-option">Sugar Baby</div>
                <div class="lh-option">Sugar Mommy</div>
                <div class="lh-option">Mentor</div>
                <div class="lh-option">Sponsor</div>
                <div class="lh-option">Companion</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Into</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="hobby">
            <button class="lh-dropdown-button" type="button">INTO</button>
            <div class="lh-dropdown-menu">
                <div class="lh-option">Reading</div>
                <div class="lh-option">Traveling</div>
                <div class="lh-option">Cooking</div>
                <div class="lh-option">Gaming</div>
                <div class="lh-option">Music</div>
                <div class="lh-option">Sports</div>
                <div class="lh-option">Drawing</div>
                <div class="lh-option">Art</div>
            </div>
        </div>

        <!-- One textarea to store the final description -->
        <textarea style="display: none;" name="description" id="description" rows="3" class="w-full mt-3" readonly></textarea>

        <div class="mb-4"></div>

        <div class="location-field" id="locationField" data-target="locationPopup" style="margin-bottom: 16px;">
            <div class="d-flex align-items-center" style="gap: 12px;">
                <span class="icon">
                    <img src="{{ asset('icons/pin.svg') }}" alt="Pin svg icon">
                </span>
                <span id="selectedLocation">SELECT LOCATION</span>
            </div>
            <button class="current-location-btn" type="button">
                <img src="{{ asset('icons/location.svg') }}" alt="Pin svg icon">
            </button>
        </div>

        <div class="location-field file-field mb-4">
            <div class="d-flex align-items-center" style="gap: 12px;">
            <span class="icon">
                <img src="{{ asset('icons/file.svg') }}" alt="Pin svg icon">
            </span>
            <span>SELECT PHOTO</span>
            </div>
            <button class="file-info" type="button">
                <img src="{{ asset('icons/info.svg') }}" alt="Pin svg icon">
            </button>
        </div>

        <button class="lh-button" type="submit">Continue</button>
        
    </form>
    
</div>
<!-- Location pop up -->
<div class="lh-popup" id="locationPopup" data-modal>
    <div class="lh-popup-header">
        <button id="closePopupLocation" data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <h2 class="lh-title mb-3" style="text-align: left">Address</h2>
        <div class="location-field">
            <input
            type="text"
            id="searchInput"
            placeholder="Search location..."
            class="input-none"
            />
            <button class="current-location-btn">
            <img src="{{ asset('icons/location.svg') }}" alt="Pin svg icon">
            </button>
        </div>
        <ul id="locationList"></ul>
    </div>
</div>
@endsection
@section('script')
<script>
const locationField = document.getElementById("locationField");
const searchInput = document.getElementById("searchInput");
const selectedLocation = document.getElementById("selectedLocation");

document.addEventListener("DOMContentLoaded", () => {
    updateDescription();
    locations.shift();
    renderLocations(locations, "locationList", "location");
});

// Search filter
searchInput.addEventListener("input", () => {
    const query = searchInput.value.toLowerCase();
    const filtered = locations.filter((loc) =>
        loc.toLowerCase().includes(query)
    );
    renderLocations(filtered, "locationList", "location");
});

// Attach event to ALL current-location buttons
document.querySelectorAll(".current-location-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            try {
                // Call OpenStreetMap Nominatim API
                const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
                );
                const data = await response.json();

                if (data && data.address) {
                // Try to extract the most relevant city name
                const city =
                    data.address.city ||
                    data.address.town ||
                    data.address.village ||
                    data.address.county; // fallback if city is not available

                if (city) {
                    selectedLocation.textContent = city;
                    locationId.value = city;
                } else {
                    selectedLocation.textContent = "Unknown location";
                }
                } else {
                    selectedLocation.textContent = `Lat: ${lat.toFixed(
                    3
                )}, Lon: ${lon.toFixed(3)}`;
                }
            } catch (error) {
                alert("Could not get address from coordinates.");
                selectedLocation.textContent = `Lat: ${lat.toFixed(
                3
                )}, Lon: ${lon.toFixed(3)}`;
            }

            locationPopup.classList.remove("active");
            },
            (error) => alert("Permission denied or unavailable")
        );
        } else {
            alert("Geolocation not supported by your browser.");
        }
    });
});

// Toggle dropdown
document.querySelectorAll(".lh-dropdown-button").forEach((btn) => {
  btn.addEventListener("click", function () {
    const wrap = this.parentElement;
    document.querySelectorAll(".lh-dropdown-wrap").forEach((el) => {
      if (el !== wrap) el.classList.remove("open");
    });
    wrap.classList.toggle("open");
  });
});

// Select option
document.querySelectorAll(".lh-option").forEach((option) => {
  option.addEventListener("click", function () {
    const wrap = this.closest(".lh-dropdown-wrap");
    const btn = wrap.querySelector(".lh-dropdown-button");
    const field = wrap.dataset.field;

    // Save selection
    selections[field] = this.textContent;

    // Update button text
    btn.textContent = this.textContent;

    // Close dropdown
    wrap.classList.remove("open");

    // Update textarea with readable sentence
    updateDescription();
  });
});

// Close dropdown when clicking outside
document.addEventListener("click", function (e) {
  if (!e.target.closest(".lh-dropdown-wrap")) {
    document.querySelectorAll(".lh-dropdown-wrap").forEach((el) => {
      el.classList.remove("open");
    });
  }
});
</script>
@endsection