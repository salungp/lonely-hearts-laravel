@extends('layouts.app')
@section('title', 'Lonely Hearts | Offer')
@section('back')
<a href="{{ url('/ad/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Stand out</h1>

    @if (session('success'))
        <div class="lh-alert mb-3 lh-alert-success" id="alert">
        {{ session('success') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    @if (session('info'))
        <div class="lh-alert mb-3 lh-alert-success" id="alert">
        {{ session('info') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    <!-- Feed list -->
    <form action="{{ route('checkout.start') }}" method="POST">
        @csrf
        <input type="hidden" name="package" id="package" value="">
        @foreach ($package as $item)
            <div
                class="lh-feed-card text-decoration-none text-dark"
                data-price="{{ $item->price }}"
                data-package="{{ $item->id }}"
                >
                <div class="d-flex" style="gap: 16px; align-items: flex-start;flex-direction: row;">
                    <div class="lh-feed-icon d-flex justify-content-center">
                        <img src="{{ asset('images/'.$item->icon) }}" alt="Envelope icon" />
                    </div>

                    <div class="lh-feed-content d-block">
                        <h2>{{ $item->name }}</h2>

                        <p>{{ $item->description }}</p>
                    </div>
                </div>
            </div>
            <!-- end card -->
        @endforeach

        <h1 class="lh-offer-price mb-4">$20.00</h1>

        <div class="form-check d-flex justify-content-center mb-4" style="gap: 10px" >
            <input class="form-check-input" type="checkbox" value="" id="checkDefault" />
            <label class="form-check-label" for="checkDefault" style="text-transform: uppercase" >
                Agree with terms & condition
            </label>
        </div>
        <button class="lh-button mb-2" id="payWithApple">
            <img
            class="lh-button-icon-img"
            src="{{ asset('images/apple-pay.png') }}"
            alt="Pay with apple pay"
            />
            with apple pay
        </button>
        <button class="lh-button mb-4" id="payWithGoogle">
            <img
            class="lh-button-icon-img"
            src="{{ asset('images/google-pay.png') }}"
            alt="Pay with apple pay"
            />
            with apple google
        </button>
    </form>

    <div id="payment-request-button"></div>

    @if ($link == 'create')
        <a href="{{ route('ad.writing', ['box' => $box]) }}" class="lh-link">No, thank you</a>
    @elseif ($link == 'reply')
        <a href="{{ route('reply_confirmation') }}" class="lh-link">No, thank you</a>
    @endif
</div>
@endsection
@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const lhFeed = document.querySelectorAll(".lh-feed-card");
    const priceDisplay = document.querySelector(".lh-offer-price");
    const packageId = document.getElementById("package");
    const checkDefault = document.getElementById("checkDefault");
    const payWithApple = document.getElementById("payWithApple");
    const payWithGoogle = document.getElementById("payWithGoogle");

    // ✅ Make the first package active by default
    if (lhFeed.length > 0) {
        const firstCard = lhFeed[0];
        firstCard.classList.add("lh-active-feed");
        priceDisplay.textContent = `$${firstCard.dataset.price}`;
        packageId.value = firstCard.dataset.package;
    }

    // ✅ Prevent payment if terms not agreed
    function checkAgreement(e) {
        if (!checkDefault.checked) {
            e.preventDefault();
            alert("Please agree with the Terms & Conditions to continue.");
            return false;
        }
        return true;
    }

    // Apply validation to buttons
    payWithApple.addEventListener("click", checkAgreement);
    payWithGoogle.addEventListener("click", checkAgreement);

    // ✅ Handle package card click selection
    lhFeed.forEach((card) => {
        card.addEventListener("click", () => {
            lhFeed.forEach((c) => c.classList.remove("lh-active-feed"));
            card.classList.add("lh-active-feed");
            priceDisplay.textContent = `$${card.dataset.price}`;
            packageId.value = card.dataset.package;
        });
    });

    // ✅ Stripe logic
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const paymentRequest = stripe.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: { label: 'Featured Package', amount: 2000 },
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

    paymentRequest.canMakePayment().then(function(result) {
        if (result) {
            prButton.mount('#payment-request-button');
        } else {
            document.getElementById('payment-request-button').style.display = 'none';
        }
    });

    paymentRequest.on('paymentmethod', async function(ev) {
        // stop if user not agreed
        if (!checkDefault.checked) {
            ev.complete('fail');
            alert("Please agree with the Terms & Conditions to continue.");
            return;
        }

        // Continue to payment
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
</script>
@endsection

