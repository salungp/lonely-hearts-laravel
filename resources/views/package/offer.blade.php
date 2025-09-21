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
    <form action="" method="POST">
        @csrf
        <input type="hidden" name="package" id="package" value="">
        <div
            class="lh-feed-card text-decoration-none text-dark"
            data-price="50"
            data-package="feature_reply"
            >
            <div class="d-flex" style="gap: 16px; align-items: flex-start">
                <div class="lh-feed-icon">
                    <img src="{{ asset('images/envelope-icon.png') }}" alt="Envelope icon" />
                </div>

                <div class="lh-feed-content">
                    <h2>FEATURE REPLY</h2>

                    <p>
                    Blue eyes, long legs, shorter patience for broke men. Three
                    ex-husbands.
                    </p>
                </div>
            </div>
        </div>
        <!-- end card -->

        <!-- Feed list -->
        <div
            class="lh-feed-card text-decoration-none text-dark"
            data-price="80"
            data-package="real_letter"
        >
            <div class="d-flex" style="gap: 16px; align-items: flex-start">
            <div class="lh-feed-icon">
                <img src="{{ asset('images/envelope-icon.png') }}" alt="Envelope icon" />
            </div>

            <div class="lh-feed-content">
                <h2>SEND AS REAL LETTER</h2>

                <p>
                Blue eyes, long legs, shorter patience for broke men. Three
                ex-husbands.
                </p>
            </div>
            </div>
        </div>
        <!-- end card -->
    
    </form>

    <h1 class="lh-offer-price mb-4">$50</h1>

    <div
            class="form-check d-flex justify-content-center mb-4"
            style="gap: 10px"
        >
            <input
            class="form-check-input"
            type="checkbox"
            value=""
            id="checkDefault"
            />
            <label
            class="form-check-label"
            for="checkDefault"
            style="text-transform: uppercase"
            >
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

        <a href="{{ url($link) }}" class="lh-link">No, thank you</a>
</div>
@endsection
@section('script')
<script>
    const lhFeed = document.querySelectorAll(".lh-feed-card");
    const priceDisplay = document.querySelector(".lh-offer-price");
    const packageId = document.getElementById("package");

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