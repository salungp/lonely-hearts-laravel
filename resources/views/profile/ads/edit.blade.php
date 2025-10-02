@extends('layouts.app')
@section('title', 'My Ads | Edit')
@section('back')
<a href="{{ route('profile.my_ads') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Edit ad</h1>

    @if (session('error'))
        <div class="lh-alert mb-3 lh-alert-error" id="alert">
        {{ session('error') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    @if (session('success'))
        <div class="lh-alert mb-3 lh-alert-success" id="alert">
        {{ session('success') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    <form action="{{ route('ads.update') }}" method="POST">
        @csrf
        <div class="position-relative mb-2">
            <input type="hidden" name="id" id="id" value="{{ $ad->id }}">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="description"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own ad"
            >{{ $ad->description }}</textarea>

            <div class="lh-textarea-info">
                <span id="lh-textarea-info">0</span>/300
            </div>

            <button onclick="helpWrite()" data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                Help me write
            </button>
        </div>

        @error('description')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

        <button class="lh-button" type="submit">Update</button>
    </form>
</div>
<!-- end pop up -->
<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh" style="z-index: 999999999999999999999999 !important;">
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
@endsection
@section('script')
<script>
    const alert = document.getElementById("alert");

    function helpWrite() {
        const popup = document.getElementById("helpMePopup");

        popup.classList.add("active");
    }

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
    
    clickAction(".lh-alert-close", (e) => {
        alert.style.display = "none";
    });
</script>
@endsection