@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ route('home') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">Find love</h1>

    <div>
        <div class="lh-radio mb-3">
            <label for="one" class="d-flex align-items-center">
                <input class="lh-radio-input" type="radio" name="replyOption" id="one" value="{{ url('/ad/create_first/') }}" />
            <span class="lh-radio-circle"></span>
            I'll write it
            </label>
        </div>

        <div class="lh-radio mb-3">
            <label for="two" class="d-flex align-items-center">
                <input
                class="lh-radio-input"
                type="radio"
                name="replyOption"
                id="two"
                value="{{ url('/ad/create_second/') }}"
            />
            <span class="lh-radio-circle"></span>
            Help me write it
            </label>
        </div>

        <button class="lh-button" id="ctaButton">Continue</button>

    </div>
</div>
@endsection
@section('script')
<script>
    const selectInput = document.querySelectorAll("lh-radio-input");
    const ctaButton = document.getElementById("ctaButton");

    ctaButton.addEventListener("click", function () {
        // get selected radio
        const selected = document.querySelector(
            "input[name='replyOption']:checked"
        );

        if (selected) {
            // redirect to page based on value
            window.location.href = `{{ '${selected.value}' }}`;
        } else {
            alert("Please select an option first!");
        }
    });
</script>
@endsection