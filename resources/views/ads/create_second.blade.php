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
@include('package.offer', ['package' => $package, 'package_id' => $package_id])
@include('components.writing')

@endsection
@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const lhFeed = document.querySelectorAll(".lh-feed-card");
    const priceDisplay = document.querySelector(".lh-offer-price");
    const packageId = document.getElementById("package");
    const cancel = document.getElementById("cancel");

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
$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $selectedLocation = $("#selectedLocation");
    const $loadingText = $("#loading-text");
    const $writingPopup = $("#writingPopup");

    // Animate "WRITING AD..."
    let dotCount = 0;
    setInterval(() => {
        dotCount = (dotCount + 1) % 4;
        $loadingText.text("WRITING AD" + ".".repeat(dotCount));
    }, 500);

    // Initial setup
    updateDescription("description");
    locations.shift();
    renderLocations(locations, "locationList", "location");

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
                    const writingUrl = "{{ route('ad.writing', ['box' => ':box']) }}";
                    $("#cancel").attr("href", writingUrl.replace(':box', response.data.box_number));
                    $writingPopup.removeClass("active");
                    $("#offerPopup").addClass("active");
                } else {
                    alert(response.message || "Something went wrong");
                    $writingPopup.removeClass("active");
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
                console.log(xhr)
                $writingPopup.removeClass("active");
            }
        });
    });

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