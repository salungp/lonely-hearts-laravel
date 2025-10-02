@extends('layouts.app')
@section('title', 'Create ad | Ill write it')
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

            <button data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                Help me write
            </button>
        </div>

        @error('description')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

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

        <button class="lh-button-secondary mb-2 mt-2" data-bs-toggle="modal" data-bs-target="#staySafeModal" type="button">
            Stay Safe
        </button>

        <button class="lh-button" type="submit">Continue</button>
        <div id="result" class="mt-3" style="white-space: pre-wrap;"></div>
    </form>
</div>

<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div id="screenOne" class="container-sm">
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

<!-- Modal -->
<div class="modal lh-modal fade"
    id="staySafeModal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog lh-modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header lh-modal-header">
                <h1 class="modal-title lh-modal-title fs-5" id="exampleModalLabel">
                    Stay Safe
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

@include('components.location')

@endsection
@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
    const searchInput = document.getElementById("searchInput");
    const selectedLocation = document.getElementById("selectedLocation");

    clickAction(".lh-tag", (el) => {
        el.classList.toggle("active");
    });

    // Search filter
    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        const filtered = locations.filter((loc) =>
            loc.toLowerCase().includes(query)
        );
        renderLocations(filtered, "locationList", "location");
    });

    document.addEventListener("DOMContentLoaded", () => {
        locations.shift();
        renderLocations(locations, "locationList", "location");
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
</script>
@endsection