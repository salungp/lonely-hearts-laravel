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

        @foreach($options as $opt)
            <div class="sentence d-inline-block text-uppercase mb-2">
                <span>{{ $opt->text }}</span>
            </div>

            @if($opt->input_type === 'dropdown')
                <div class="lh-dropdown-wrap" data-field="{{ $opt->title }}">
                    <button class="lh-dropdown-button" type="button">
                        {{ strtoupper($opt->value[0]) }}
                    </button>
                    <div class="lh-dropdown-menu">
                        <input type="hidden" name="{{ $opt->title }}" value="{{ $opt->value[0] }}">
                        @foreach($opt->value as $val)
                            <div class="lh-option">{{ strtoupper($val) }}</div>
                        @endforeach
                    </div>
                </div>
            @elseif($opt->input_type === 'text')
                <input type="text" name="{{ $opt->title }}" class="input-line" />
            @elseif($opt->input_type === 'textarea')
                <textarea name="{{ $opt->title }}"></textarea>
            @endif
        @endforeach

        <button class="lh-dropdown-button" id="selectedLocation" type="button" data-target="locationPopup">
            LOCATION
        </button>

        <div class="mb-4"></div>

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

        <textarea style="display: none;" name="description" id="description" rows="3" class="w-full mt-3" readonly></textarea>

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
});
</script>
@endsection