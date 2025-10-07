@extends('layouts.app')
@section('title', 'Reply ad | Help me write it')
@section('back')
<a href="{{ url('/ad/reply/'.$ad->box_number) }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">

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


        <div class="mb-4"></div>

        <button class="lh-button" onclick="openConfirmPopup()">Continue</button>
    
</div>
<!-- Location pop up -->
@include('components.location')
<!-- Location pop up -->
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
            <form action="{{ route('ad.reply_store') }}" method="POST">
                @csrf
                <!-- One textarea to store the final description -->
                <div class="position-relative mb-2">
                    <input type="hidden" name="ad_id" id="ad_id" value="{{ $ad->id }}">
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
                <button class="lh-button" type="submit">Send reply</button>
            </form>
        </div>
    </div>
</div>
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

@endsection
@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
const confirmPopup = document.getElementById("confirmPopup");

function openConfirmPopup() {
    updateDescription("lh-textarea");
    confirmPopup.classList.add("active");
}

function closeConfirmPopup() {
    confirmPopup.classList.remove("active");
}

// Select option
document.querySelectorAll(".lh-option").forEach((option) => {
  option.addEventListener("click", function () {
    const wrap = this.closest(".lh-dropdown-wrap");
    const btn = wrap.querySelector(".lh-dropdown-button");
    const hiddenInput = wrap.querySelector("input[type='hidden']");
    const field = wrap.dataset.field;

    // Original text (from DB / option)
    const selectedText = this.textContent.trim();

    // Lowercase version
    const lowerValue = selectedText.toLowerCase();

    // Update button (show original text, not forced lowercase)
    btn.textContent = selectedText;

    // Save lowercase value into hidden input
    if (hiddenInput) {
      hiddenInput.value = lowerValue;
    }

    // Also save into JS selections if you still use it
    selections[field] = lowerValue;

    // Close dropdown
    wrap.classList.remove("open");

    // Update textarea
    updateDescription("lh-textarea");
  });
});

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
</script>
@endsection