@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/create/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">I'll write it</h1>

    <form action="{{ route('create.store') }}" method="POST">
        @csrf
        <div class="position-relative mb-2">
            <input type="hidden" name="location" id="location">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="description"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own ad"
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

        @error('description')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

        <div class="location-field" id="locationField" style="margin-bottom: 16px;">
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

        <div class="location-field file-field">
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

<!-- Location pop up -->
<div class="lh-popup" id="locationPopup">
    <div class="lh-popup-header">
        <button id="closePopupLocation">
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
@section('script')
<script>
    const locationField = document.getElementById("locationField");
    const locationPopup = document.getElementById("locationPopup");
    const closePopup = document.getElementById("closePopupLocation");
    const searchInput = document.getElementById("searchInput");
    const locationList = document.getElementById("locationList");
    const selectedLocation = document.getElementById("selectedLocation");
    const locationId = document.getElementById("location");

    // Show popup
    locationField.addEventListener("click", () => {
        locationPopup.classList.add("active");
        renderLocations(locations);
    });

    // Close popup
    closePopup.addEventListener("click", () => {
        locationPopup.classList.remove("active");
    });

    const locations = [
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

    // Render list dynamically
    function renderLocations(list) {
        locationList.innerHTML = "";
        list.forEach((loc) => {
            const li = document.createElement("li");
            li.textContent = loc;
            li.addEventListener("click", () => {
                selectedLocation.textContent = loc;
                locationId.value = loc;
                locationPopup.classList.remove("active");
            });
            locationList.appendChild(li);
        });
    }

    // Search filter
    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        const filtered = locations.filter((loc) =>
            loc.toLowerCase().includes(query)
        );
        renderLocations(filtered);
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

</script>
@endsection