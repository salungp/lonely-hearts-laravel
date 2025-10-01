@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Stand out</h1>

    <!-- Feed list -->
    <form action="{{ route('checkout.start', $package_id) }}" method="POST">
        @csrf
        <input type="hidden" name="package" id="package" value="">
        @foreach ($package as $item)
            <div
                class="lh-feed-card text-decoration-none text-dark"
                data-price="{{ $item->price }}"
                data-package="feature_reply"
                >
                <div class="d-flex" style="gap: 16px; align-items: flex-start">
                    <div class="lh-feed-icon">
                        <img src="{{ asset('images/envelope-icon.png') }}" alt="Envelope icon" />
                    </div>

                    <div class="lh-feed-content">
                        <h2>{{ $item->name }}</h2>

                        <p>{{ $item->description }}</p>
                    </div>
                </div>
            </div>
            <!-- end card -->
        @endforeach

        <h1 class="lh-offer-price mb-4">$50</h1>

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

    <a href="#" class="lh-link" data-close>No, thank you</a>
</div>
<div id="payment-request-button"></div>
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
            console.log(result);
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
@endsection