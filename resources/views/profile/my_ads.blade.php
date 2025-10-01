@extends('layouts.app')
@section('title', 'My Ads')
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
      <h1 class="lh-title mb-3">My ads</h1>

      @if (session('success'))
          <div class="lh-alert mb-3 lh-alert-success" id="alert">
            {{ session('success') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
      @endif

      @if (session('error'))
          <div class="lh-alert mb-3 lh-alert-error" id="alert">
            {{ session('error') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
      @endif

      @foreach($ads as $ad)
      <!-- Feed list -->
      <div class="lh-feed-card text-decoration-none text-dark" >
        <div>
          <h2>
            <b>{{ $ad->snapshot_name }}, {{ $ad->snapshot_age }}, {{ $ad->snapshot_gender }}, {{ $ad->location }}, {{ $ad->snapshot_status }} .</b> {{ substr($ad->description, 0, 50) . '...' }}
          </h2>

          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
              <img
                src="{{ asset('images/logo.svg') }}"
                class="lh-small-icon me-2"
                alt="Love lonely heart symbol"
              />
              <span>{{ $ad->likes_count }}</span>
            </div>

            <div class="d-flex align-items-center">
              <img
                src="{{ asset('icons/eye-icon.svg') }}"
                class="lh-small-icon me-2"
                alt="View icon"
              />
              <span>{{ $ad->views }}</span>
            </div>

            <div class="lh-mini-dropdown-wrapper">
                <div role="button" class="lh-mini-dropdown-btn">
                <img
                    src="{{ asset('icons/ellipsis.svg') }}"
                    class="lh-small-icon"
                    alt="Share icon"
                />
                </div>

                <div class="lh-mini-dropdown">
                    <a href="{{ route('profile.ad_edit', ['id' => $ad->id]) }}">
                        <span style="color: var(--dark);">Edit</span>
                    </a>

                    <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ad?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="lh-btn-link">Delete</button>
                    </form>
                </div>
            </div>

          </div>
        </div>
      </div>
      @endforeach

</div>
@endsection
@section('script')
<script>
  clickAction(".lh-mini-dropdown-btn", (el) => {
        const wrapper = el.closest(".lh-mini-dropdown-wrapper");
        const dropdown = wrapper.querySelector(".lh-mini-dropdown");
        const isAlreadyActive = dropdown.classList.contains("active");

        // Close all dropdowns first
        document.querySelectorAll(".lh-mini-dropdown").forEach((d) => {
          d.classList.remove("active");
        });

        // If it was NOT already active, open it
        if (!isAlreadyActive) {
          dropdown.classList.add("active");
        }
  });
</script>
@endsection
