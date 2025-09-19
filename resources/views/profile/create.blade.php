@extends('layouts.app')
@section('title', 'Home Page')
@section('back')
<a href="{{ url('/ad/create') }}" class="lh-nav-button">
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


        <div class="d-flex mb-3">
            <span style="font-size: 20px; text-transform: uppercase; margin-right: 16px;" >My Name is</span
            >
            <input style="text-transform: uppercase;" type="text" name="person_name" class="input-line" required />
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>I'm in a</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="height">
            <button class="lh-dropdown-button" type="button">Work</button>
            <div class="lh-dropdown-menu">
                <input type="hidden" name="occupation" value="work">
                <div class="lh-option">Work</div>
                <div class="lh-option">School</div>
                <div class="lh-option">Freelance</div>
                <div class="lh-option">Unemployed</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>In</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="height">
            <button class="lh-dropdown-button" type="button">30</button>
            <div class="lh-dropdown-menu">
                <input type="hidden" name="age" value="30">
                <div class="lh-option">18</div>
                <div class="lh-option">20</div>
                <div class="lh-option">25</div>
                <div class="lh-option">30</div>
                <div class="lh-option">35</div>
                <div class="lh-option">38</div>
                <div class="lh-option">42</div>
                <div class="lh-option">46</div>
                <div class="lh-option">50</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Into</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="height">
            <button class="lh-dropdown-button" type="button">MALE</button>
            <div class="lh-dropdown-menu">
                <input type="hidden" name="gender" value="MALE">
                <div class="lh-option">MALE</div>
                <div class="lh-option">FEMALE</div>
                <div class="lh-option">ALL</div>
            </div>
        </div>

        <div class="sentence d-inline-block text-uppercase mb-2">
            <span>Status</span>
        </div>

        <div class="lh-dropdown-wrap" data-field="height">
            <button class="lh-dropdown-button" type="button">Single</button>
            <div class="lh-dropdown-menu">
                <input type="hidden" name="status" value="Single">
                <div class="lh-option">Single</div>
                <div class="lh-option">Taken</div>
                <div class="lh-option">Complicated</div>
                <div class="lh-option">Secret</div>
            </div>
        </div>

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