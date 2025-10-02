@extends('layouts.app')
@section('title', 'Create ad | Help me write it')
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
                @foreach ($options["height"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
            </div>
        </div>

        <div class="lh-dropdown-wrap" data-field="hair">
            <button class="lh-dropdown-button" type="button">Blue hair</button>
            <div class="lh-dropdown-menu">
                @foreach ($options["hair"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>And</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="eyes">
            <button class="lh-dropdown-button" type="button">Who</button>
            <div class="lh-dropdown-menu">
                @foreach ($options["eyes"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Eyes, Who</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="behavior">
            <button class="lh-dropdown-button" type="button">Who</button>
            <div class="lh-dropdown-menu">
                @foreach ($options["behavior"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Seeking</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="seeking">
            <button class="lh-dropdown-button" type="button">Seeking</button>
            <div class="lh-dropdown-menu">
                @foreach ($options["seeking"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Into</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="hobby">
            <button class="lh-dropdown-button" type="button">INTO</button>
            <div class="lh-dropdown-menu">
                @foreach ($options["hobby"] as $item)
                    <div class="lh-option">{{ $item }}</div>
                @endforeach
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

@include('components.location')

@endsection
@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $selectedLocation = $("#selectedLocation");

    // Initial setup
    updateDescription("description");
    locations.shift();
    renderLocations(locations, "locationList", "location");

    // Search filter
    $searchInput.on("input", function () {
        const query = $(this).val().toLowerCase();
        const filtered = locations.filter(loc => loc.toLowerCase().includes(query));
        renderLocations(filtered, "locationList", "location");
    });

    // Handle current location button
    $(".current-location-btn").on("click", function (e) {
        e.stopPropagation();

        if (!navigator.geolocation) {
            return alert("Geolocation not supported by your browser.");
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
                    );
                    const data = await response.json();

                    if (data && data.address) {
                        const city =
                            data.address.city ||
                            data.address.town ||
                            data.address.village ||
                            data.address.county;

                        if (city) {
                            $selectedLocation.text(city);
                            $("#location").val(city);
                        } else {
                            $selectedLocation.text("Unknown location");
                        }
                    } else {
                        $selectedLocation.text(`Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`);
                    }
                } catch (error) {
                    alert("Could not get address from coordinates.");
                    $selectedLocation.text(`Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`);
                }

                $("#locationPopup").removeClass("active");
            },
            () => alert("Permission denied or unavailable")
        );
    });

    // Toggle dropdowns
    $(".lh-dropdown-button").on("click", function () {
        const $wrap = $(this).parent();
        $(".lh-dropdown-wrap").not($wrap).removeClass("open");
        $wrap.toggleClass("open");
    });

    // Select option
    $(".lh-option").on("click", function () {
        const $wrap = $(this).closest(".lh-dropdown-wrap");
        const $btn = $wrap.find(".lh-dropdown-button");
        const field = $wrap.data("field");

        selections[field] = $(this).text(); // save selection
        $btn.text($(this).text()); // update button
        $wrap.removeClass("open");

        updateDescription("description");
    });

    // Close dropdowns when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".lh-dropdown-wrap").length) {
            $(".lh-dropdown-wrap").removeClass("open");
        }
    });
});
</script>
@endsection