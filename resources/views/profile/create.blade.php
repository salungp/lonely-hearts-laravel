@extends('layouts.app')
@section('title', 'Lonely Hearts | Create Profile')
@section('back')
<a href="{{ url()->previous() }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">

    <!-- Form start line -->
    <form action="{{ route('profile.store') }}" method="POST" id="dynamicInputs">
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

        <div class="d-flex mb-3" style="flex-grow: 0;flex-shrink: 0;gap: 16px;">
            <span style="font-size: 20px; text-transform: uppercase;" >My Name is</span>
            <input style="text-transform: uppercase;" type="text" name="person_name" class="input-line" required />
        </div>

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
                        <input type="hidden" id="{{ $opt->title }}" name="{{ $opt->title }}" value="{{ $opt->value[0] }}">
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

        @if ($reply_mode === 'textarea' && $state === 'reply')
            <div class="sentence d-inline-block text-uppercase mb-2">
                <span>Location</span>
            </div>
            <button class="lh-dropdown-button" id="selectedLocation" type="button" data-target="locationPopup">
                LOCATION
            </button>
            <input type="hidden" name="location" id="location">
            <div class="position-relative mb-2 mt-3">
                <input type="hidden" name="ad_id" value="{{ $ad->id ?? '' }}">
                    <textarea
                        class="lh-textarea"
                        oninput="updateLHtextarea()"
                        name="content"
                        id="lh-textarea"
                        maxlength="300"
                        placeholder="Write reply"
                        style="font-family: 'Merriweather'"
                    ></textarea>

                    <div class="lh-textarea-info">
                        <span id="lh-textarea-info">0</span>/300
                    </div>

                    <button data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                        Help me write
                    </button>
            </div>
            <button class="lh-button" type="submit">Continue</button>
        @endif

        @if ($reply_mode === 'options' && $state === 'reply')
            @foreach($options_ad as $opt)
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
            
            <div class="sentence d-inline-block text-uppercase mb-2">
                <span>Location</span>
            </div>
            <button class="lh-dropdown-button" id="selectedLocation" type="button" data-target="locationPopup">
                LOCATION
            </button>

            <button class="lh-button mt-3" id="continueButton">Send reply</button>
        @endif

        <div class="mb-4"></div>

        @if ($state === 'create')
        <button class="lh-button" type="submit">Continue</button>
        @endif
    </form>
</div>
<!-- Location pop up -->
@if ($state === 'reply' && $reply_mode === 'options')
<div class="lh-popup" id="confirmPopup">
    <div class="lh-popup-header">
        <button onclick="closeConfirmPopup()">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <div class="container-sm">
            <div class="d-flex justify-content-between">
                <h2 class="lh-title mb-3" style="text-align: left">Reply</h2>
                <span>{{ strtoupper(date('D jS M Y', strtotime('2025-06-06'))) }}</span>
            </div>

            <h3 style="font-family: 'Merriweather'; text-align: left">Dear {{ $ad->snapshot_name }}</h3>
            <form action="{{ route('profile.store') }}" method="POST" id="profileForm">
                @csrf
                <!-- One textarea to store the final description -->
                <div class="position-relative mb-2">
                    <input type="hidden" name="ad_id" id="ad_id" value="{{ $ad->id }}">
                    <input type="hidden" name="location" id="location">
                    <textarea
                        class="lh-textarea"
                        oninput="updateLHtextarea()"
                        name="content"
                        id="lh-textarea"
                        maxlength="300"
                        placeholder="Write reply"
                        style="font-family: 'Merriweather'"
                    ></textarea>

                    <div class="lh-textarea-info">
                        <span id="lh-textarea-info">0</span>/300
                    </div>

                    <button data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                        Help me write
                    </button>
                </div>
                <button class="lh-button" type="submit">Continue</button>
            </form>
        </div>
    </div>
</div>
@endif
@if ($state === 'reply')
<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div class="container-sm" id="screenOne">
            <h2 class="lh-title mb-3" style="text-align: left">Reword it</h2>
            <div id="tags-container" class="tags-container">
                @foreach ($prompts as $style)
                    <button class="lh-tag" data-style="{{ $style }}">{{ $style }}</button>
                @endforeach
            </div>

            <button id="applyStyle" class="lh-button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span class="btn-text">Apply Style</span>
            </button>
        </div>
    <!-- End scenario -->
    </div>
</div>
@endif
@include('components.location')
@endsection
@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
const confirmPopup = document.getElementById("confirmPopup");

@if ($state === 'reply' && $reply_mode === 'options')
const continueBtn = document.getElementById("continueButton");
const form = document.getElementById("profileForm");
document.addEventListener("DOMContentLoaded", function () {
    continueBtn.addEventListener("click", function (e) {
        e.preventDefault(); // prevent auto-submit first

        // remove old hidden inputs
        form.querySelectorAll(".generated-hidden").forEach(el => el.remove());

        // collect all inputs & textareas from the dynamic section
        const allInputs = document.querySelectorAll(
            '#dynamicInputs input, #dynamicInputs textarea, #dynamicInputs select'
        );

        let hasEmpty = false;

        allInputs.forEach(input => {
            const value = input.value?.trim();
            const name = input.name;

            // check if required or visually necessary field is empty
            const isRequired = input.hasAttribute("required") || input.classList.contains("input-line");

            if (isRequired && !value) {
                hasEmpty = true;
                input.classList.add("error-border"); // visual cue
            } else {
                input.classList.remove("error-border");
            }

            // still append value (even if optional)
            if (name) {
                const hidden = document.createElement("input");
                hidden.type = "hidden";
                hidden.name = name;
                hidden.value = value || "";
                hidden.classList.add("generated-hidden");
                form.appendChild(hidden);
            }
        });

        if (hasEmpty) {
            alert("Please fill out all required fields before continuing.");
            return; // stop form submission
        }

        updateDescription("lh-textarea");
        updateLHtextarea();
        confirmPopup.classList.add("active");
    });
});
@endif

@if ($state === 'reply')
document.addEventListener("DOMContentLoaded", () => {
    let selectedStyle = null;

    // Handle style button clicks
    document.querySelectorAll("#tags-container button").forEach(btn => {
        btn.addEventListener("click", () => {
            selectedStyle = btn.dataset.style;

            // highlight active button
            document.querySelectorAll("#tags-container button").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
        });
    });

    // Handle Apply Style
    document.getElementById("applyStyle").addEventListener("click", () => {
        const btn = document.getElementById("applyStyle");
        const spinner = btn.querySelector(".spinner-border");
        const btnText = btn.querySelector(".btn-text");
        const textarea = document.getElementById("lh-textarea");
        const text = textarea.value;

        if (!selectedStyle) {
            alert("Please select a style first.");
            return;
        }

        // Show loading spinner
        btn.disabled = true;
        spinner.classList.remove("d-none");
        btnText.textContent = "Generating...";

        fetch("/ad/apply-style", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ text: text, style: selectedStyle })
        })
            .then(res => res.json())
            .then(data => {
                textarea.value = data.styled_text;
                updateLHtextarea();
                document.getElementById("helpMePopup").classList.remove("active");
            })
            .catch(err => {
                console.error(err);
                alert("Something went wrong!");
            })
            .finally(() => {
                // Reset button
                btn.disabled = false;
                spinner.classList.add("d-none");
                btnText.textContent = "Apply Style";
            });
    });
});

function closeConfirmPopup() {
    confirmPopup.classList.remove("active");
}

$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $selectedLocation = $("#selectedLocation");

    // Initial setup
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
@endif
</script>
@endsection