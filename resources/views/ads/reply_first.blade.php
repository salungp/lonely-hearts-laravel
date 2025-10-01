@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/reply/'.$ad->box_number) }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-between">
        <h2 class="lh-title mb-3" style="text-align: left">Reply</h2>
        <span>{{ strtoupper(date('D jS M Y', strtotime('2025-06-06'))) }}</span>
    </div>

    <h3 style="font-family: 'Merriweather'; text-align: left">Dear {{ $ad->snapshot_name }}</h3>

    <form action="{{ route('ad.reply_store') }}" method="POST">
        @csrf
        <input type="hidden" name="ad_id" id="ad_id" value="{{ $ad->id }}">
        <div class="position-relative mb-2">
            <input type="hidden" name="location" id="location">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="content"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own reply"
            ></textarea>

            <div class="lh-textarea-info">
                <span id="lh-textarea-info">0</span>/300
            </div>

            <button data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                Help me write
            </button>
        </div>

        @error('content')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

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

<!-- Modal -->
<div class="modal lh-modal fade"
    id="staySafeModal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true" >
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

@include('package.offer', ['package' => $package, 'package_id' => $package_id])
@include('components.writing')

@endsection
@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const lhFeed = document.querySelectorAll(".lh-feed-card");
    const priceDisplay = document.querySelector(".lh-offer-price");
    const packageId = document.getElementById("package");

    document.addEventListener("DOMContentLoaded", function() {
        const stripe = Stripe("{{ config('services.stripe.key') }}"); // pk_test_xxx

        // Create a payment request for the Featured package ($20)
        const paymentRequest = stripe.paymentRequest({
            country: 'US',
            currency: 'usd',
            total: {
                label: 'Featured Package',
                amount: 2000, // $20
            },
            requestPayerName: true,
            requestPayerEmail: true,
        });

        const elements = stripe.elements();
        const prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest,
            style: {
                paymentRequestButton: {
                    type: 'default',
                    theme: 'dark',
                    height: '48px',
                },
            },
        });

        // Check if Apple Pay / Google Pay is available
        paymentRequest.canMakePayment().then(function(result) {
            if (result) {
                prButton.mount('#payment-request-button');
            } else {
                document.getElementById('payment-request-button').style.display = 'none';
            }
        });

        // Handle payment
        paymentRequest.on('paymentmethod', async function(ev) {
            // Create PaymentIntent on server
            const response = await fetch("{{ route('payment.intent.create', $package_id->id) }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            });
            const { clientSecret } = await response.json();

            const {error, paymentIntent} = await stripe.confirmCardPayment(
                clientSecret,
                { payment_method: ev.paymentMethod.id },
                { handleActions: false }
            );

            if (error) {
                ev.complete('fail');
                alert(error.message);
            } else {
                ev.complete('success');
                if (paymentIntent.status === "requires_action") {
                    await stripe.confirmCardPayment(clientSecret);
                }
                if (paymentIntent.status === "succeeded") {
                    window.location.href = "{{ route('checkout.success', $package_id->id) }}";
                }
            }
        });
    });

    lhFeed.forEach((card) => {
    card.addEventListener("click", () => {
        // remove active from all
        lhFeed.forEach((c) => c.classList.remove("lh-active-feed"));
            // add active to clicked one
            card.classList.add("lh-active-feed");

            // update price
            const price = card.getAttribute("data-price");
            priceDisplay.textContent = `$${price}`;
            packageId.value = card.getAttribute("data-package");
        });
    });
</script>
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
    const screenOne = getIdElement("screenOne");

    clickAction(".lh-tag", (el) => {
        el.classList.toggle("active");
    });

    $(document).ready(function () {
        const $writingPopup = $("#writingPopup");
        const $loadingText = $("#loading-text");

        let dotCount = 0;
        setInterval(() => {
            dotCount = (dotCount + 1) % 4;
            $loadingText.text("WRITING AD" + ".".repeat(dotCount));
        }, 500);

        // Handle form submit with AJAX
        $("form").on("submit", function (e) {
            e.preventDefault();
            $writingPopup.addClass("active");

            $.ajax({
                url: $(this).attr("action"),
                method: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $("#offerPopup").addClass("active");
                        $("#confirmPopup").removeClass("active");
                        $writingPopup.removeClass("active");
                        const writingUrl = "{{ route('reply_confirmation') }}";
                        $("#cancel").attr("href", writingUrl);
                    } else {
                        alert(response.message || "Something went wrong");
                        $writingPopup.removeClass("active");
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                    $writingPopup.removeClass("active");
                }
            });
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