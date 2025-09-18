@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
@php
$profile = session('profile')
@endphp
@if($profile)
<a href="{{ url('/ad/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/back.svg') }}" alt="Icon back button" />
</a>
@endif
@endsection
@section('content')
<div class="container-sm">
    <h1
        class="lh-title"
        style="width: 100%; text-align: left; margin-bottom: 20px"
        >
        before continue
    </h1>

    <form action="{{ route('login_or_register') }}" method="POST">
        @csrf
        <div class="lh-input-group mb-3">
            <label for="name">Name</label>
            <input
            type="text"
            class="lh-input"
            placeholder="Enter your name"
            name="name"
            />
            @error('name')
                <div class="text-uppercase text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="lh-input-group mb-3 position-relative">
            <label for="name">Phone number</label>
            <div class="position-relative">
                <div id="countrySelector" class="lh-country-display">
                    +62 🇮🇩
                </div>
                <input type="hidden" id="country_code" name="country_code" value="+1">
                <input
                    type="tel"
                    class="lh-input lh-input-tel"
                    placeholder="Enter your phone number"
                    name="phone_number"
                />
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
    <div class="popup-content">
    <input
        type="text"
        id="countrySearch"
        placeholder="Search country..."
    />
    <ul id="countryList"></ul>
    </div>
</div>
@endsection
@section('script')
<script>
    const countries = [
        { name: "United States", code: "+1", flag: "🇺🇸" },
        { name: "United Kingdom", code: "+44", flag: "🇬🇧" },
        { name: "Indonesia", code: "+62", flag: "🇮🇩" },
        { name: "India", code: "+91", flag: "🇮🇳" },
        { name: "Japan", code: "+81", flag: "🇯🇵" },
        // ... add more countries
      ];

      new CountrySelector({
        trigger: "#countrySelector", // the element to open popup
        popup: "#countryPopup", // popup container
        countries: countries, // data
        input: "country_code"
      });
</script>
@endsection