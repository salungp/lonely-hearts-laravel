@extends('layouts.app')
@section('title', 'Home Page')
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
            <button class="lh-dropdown-button" type="button">Red</button>
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
            <button class="lh-dropdown-button" type="button">Traveling</button>
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


        <div class="mb-4"></div>

        <button class="lh-button" onclick="openConfirmPopup()">Continue</button>
    
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
<!-- Location pop up -->
<div class="lh-popup" id="confirmPopup">
    <div class="lh-popup-header">
        <button onclick="closeConfirmPopup()">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
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
<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div id="screenOne">
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
const selections = {
  height: "Average height",
  hair: "black hair",
  eyes: "brown eyes",
  behavior: "kind",
  seeking: "friends",
  hobby: "reading"
};
const confirmPopup = document.getElementById("confirmPopup");

function openConfirmPopup() {
    updateDescription();
    confirmPopup.classList.add("active");
}

function closeConfirmPopup() {
    confirmPopup.classList.remove("active");
}

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

        if (!text) {
            alert("Please write something first.");
            return;
        }
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

// Close dropdown when clicking outside
document.addEventListener("click", function (e) {
  if (!e.target.closest(".lh-dropdown-wrap")) {
    document.querySelectorAll(".lh-dropdown-wrap").forEach((el) => {
      el.classList.remove("open");
    });
  }
});

function updateDescription() {
  const textarea = document.getElementById("lh-textarea");

  const height = selections.height || "";
  const hair = selections.hair ? ` with ${selections.hair}` : "";
  const eyes = selections.eyes ? `, ${selections.eyes.toLowerCase()} eyes` : "";
  const behavior = selections.behavior ? `, who is ${selections.behavior.toLowerCase()}` : "";
  const seeking = selections.seeking ? `, seeking ${selections.seeking}` : "";
  const hobby = selections.hobby ? `, into ${selections.hobby}` : "";

  // Build sentence dynamically
  let sentence = "";
  if (height) sentence += `I'm ${height}`;
  sentence += hair;
  sentence += eyes;
  sentence += behavior;
  sentence += seeking;
  sentence += hobby;
  if (sentence) sentence += "."; // add period at the end

  textarea.value = sentence;
}
</script>
@endsection