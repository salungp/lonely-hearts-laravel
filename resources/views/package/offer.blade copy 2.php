<!-- offerPopup pop up -->
<div class="lh-popup" id="offerPopup" data-modal>
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <div class="container-sm">
            <h1 class="lh-title mb-3 text-start">Stand out</h1>
            <!-- Feed list -->
            <form action="{{ route('checkout.start', $package_id) }}" method="POST">
                @csrf
                <input type="hidden" name="package" id="package" value="">
                {{-- Fetch the package item --}}
                @foreach ($package as $item)
                    <div class="lh-feed-card text-decoration-none text-dark" data-price="{{ $item->price }}" data-package="feature_reply">
                        <div class="d-flex flex-row flex-start" style="gap: 16px;">
                            <div class="lh-feed-icon d-flex align-items-center justify-content-center">
                                <img src="{{ asset('images/envelope-icon.png') }}" alt="Envelope icon" />
                            </div>
                            {{-- Item content --}}
                            <div class="lh-feed-content d-block">
                                <div class="d-flex justify-content-between">
                                    <h2 class="text-start mb-1">{{ $item->name }}</h2>
                                    <span>${{ $item->price }}</span>
                                </div>

                                <p class="text-start">{{ $item->description }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                @endforeach

                <h3 class="lh-offer-price mb-4 d-none">$20.00</h3>

                <div class="form-check d-flex justify-content-center mb-4 mt-4" style="gap: 10px" >
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

            <a id="cancel" href="{{ route('ad.writing', ['box'=>234022]) }}" class="lh-link">No, thank you</a>

            <div id="payment-request-button"></div>
        </div>
    </div>
</div>