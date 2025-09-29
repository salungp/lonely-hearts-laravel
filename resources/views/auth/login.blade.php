@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
@php
$profile = session('profile')
@endphp
@if($profile)
<a href="{{ url('/ad/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endif
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title w-100 mb-3">before continue</h1>
    <form action="{{ route('login_or_register') }}" method="POST">
        @csrf
        <div class="lh-input-group mb-3 position-relative">
            <label for="name">Phone number</label>
            <div class="d-flex lh-input lh-input-tel">
                <div id="countrySelector" class="lh-country-display">+62</div>
                <input type="hidden" id="country_code" name="country_code" value="+62">
                <input type="tel" placeholder="Enter your phone number" name="phone_number"/>
            </div>

            @error('phone_number')
                <div class="text-uppercase text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button class="lh-button" type="submit">Continue</button>
    </form>
</div>
<!-- Country Selector Popup -->
<div id="countryPopup" class="popup hidden">
    <div class="container-sm">
        <div class="popup-content">
            <input type="text" id="countrySearch" placeholder="Search country..."/>
            <ul id="countryList"></ul>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    const selector = new CountrySelector({
        trigger: "#countrySelector",
        popup: "#countryPopup",
        input: "country_code",
        apiUrl: "https://restcountries.com/v3.1/all?fields=name,cca2,idd,flags",
    });

    async function getLocation() {
    try {
        const res = await fetch("{{ url('/my-location') }}");
        const data = await res.json();
        return data;
    } catch (err) {
        console.error("Error:", err);
        return null;
    }
    }

    window.phoneCodes = @json(config('phonecodes'));

    document.addEventListener("DOMContentLoaded", async () => {
        const location = await getLocation(); // waits for fetch
        document.getElementById("ctaLocation").textContent =
            location.city || location.cityName || "Unknown";

        selector.setDefault(window.phoneCodes[location.country]);
    });
</script>
@endsection