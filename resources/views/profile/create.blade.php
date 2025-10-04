@extends('layouts.app')
@section('title', 'Lonely Hearts | Create Profile')
@section('back')
<a href="{{ url()->previous() }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">

    <!-- Form start line -->
    <form action="{{ route('profile.store') }}" method="POST">
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


        <div class="d-flex mb-3" style="flex-grow: 0;flex-shrink: 0;gap: 16px;">
            <span style="font-size: 20px; text-transform: uppercase;" >My Name is</span>
            <input style="text-transform: uppercase;" type="text" name="person_name" class="input-line" required />
        </div>

        @foreach($options as $opt)
            <div class="sentence d-inline-block text-uppercase mb-2">
                <span>{{ $opt->text }}</span>
            </div>

            @if($opt->input_type === 'dropdown')
                <div class="lh-dropdown-wrap" data-field="{{ $opt->title }}">
                    <button class="lh-dropdown-button" type="button">
                        {{ strtoupper($opt->value[0]) }}
                    </button>
                    <div class="lh-dropdown-menu">
                        <input type="hidden" name="{{ $opt->title }}" value="{{ $opt->value[0] }}">
                        @foreach($opt->value as $val)
                            <div class="lh-option">{{ strtoupper($val) }}</div>
                        @endforeach
                    </div>
                </div>
            @elseif($opt->input_type === 'text')
                <input type="text" name="{{ $opt->title }}" class="input-line" />
            @elseif($opt->input_type === 'textarea')
                <textarea name="{{ $opt->title }}"></textarea>
            @endif
        @endforeach

        <div class="mb-4"></div>

        <button class="lh-button" type="submit">Continue</button>
        
    </form>
    
</div>
@endsection
@section('script')
<script>
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
    const hiddenInput = wrap.querySelector("input[type='hidden']");

    // Set button text
    btn.textContent = this.textContent;

    // Set hidden input value
    hiddenInput.value = this.textContent;

    // Close dropdown
    wrap.classList.remove("open");
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
</script>
@endsection