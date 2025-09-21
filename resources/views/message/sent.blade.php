@extends('layouts.app')
@section('title', 'Home Page')
@section('meta')
<style>
      .lh-mini-dropdown-wrapper {
        position: relative;
      }

      .lh-mini-dropdown {
        width: 190px;
        background: var(--fill);
        border: 2px solid var(--dark);
        position: absolute;
        top: 60px;
        right: 0;
        opacity: 0;
        transition: .2s ease-in-out;
      }

      .lh-mini-dropdown a {
        width: 100%;
        height: 48px;
        line-height: 48px;
        text-decoration: none;
        color: var(--dark);
        display: flex;
        padding: 0 20px;
        text-transform: uppercase;
        font-size: 16px;
        gap: 8px;
        align-items: center;
      }

      .lh-mini-dropdown a:nth-child(1) {
        border-bottom: 2px solid var(--dark);
      }

      .lh-mini-dropdown a img {
        width: 24px;
      }

      .lh-mini-dropdown.active {
        opacity: 1;
        top: 36px;
      }
</style>
@endsection
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-between">
        <h1 class="lh-title mb-3">Sent</h1>
        <a href="{{ route('message') }}" class="lh-link" style="padding: 0 !important; text-align: right;">Receive</a>
    </div>

    @foreach ($messages as $message)
        <!-- Feed list -->
        <div data-target="{{ $message->sender->id }}" id="messageItem3" class="lh-feed-card gap-2 d-flex flex-direction-row text-decoration-none text-dark">

            @if ($message->is_read == 0)
                <img
                    style="width: 58px;"
                    src="{{ asset('images/envelope-icon.png') }}"
                    alt="Envelope icon"
                />
            @else
                <img
                    style="width: 58px;"
                    src="{{ asset('images/envelope-open.png') }}"
                    alt="Envelope icon"
                />
            @endif

            <div class="text-content" style="flex-grow: 1">
              <h4 class="lh-sub-title">{{ $message->conversation->author->name }}</h4>
              <p class="lh-text-small">{{ substr($message->content, 0, 50) . '...' }}</p>
            </div>
            <img
              style="width: 24px"
              src="{{ asset('images/heart-fill-20.svg') }}"
              alt="Heart fill progress"
            />
          </div>
    @endforeach

</div>
<!-- Pop up -->
<div id="popup" class="lh-popup full-height">
    <!-- Lh popup header -->
    <div class="lh-popup-header">
        <button id="closePopup" class="close-popup">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>

    <!-- Lh popup body -->
    <div
        class="lh-popup-body"
        style="height: 100%; overflow-y: scroll; padding-bottom: 100px"
    >
    <div class="message-lists" id="messageLists">
        <div class="container-sm" id="popupBody">
            @foreach ($messages as $message)
            <!-- Message body -->
            <div class="lh-message-card">
                <div class="lh-message-header">
                    <div class="left">
                        <p class="lh-text-small">From</p>
                        <h4 class="lh-sub-title">{{ $message->sender->name }}</h4>
                    </div>
                    <div class="right">
                        <p class="lh-text-small">12, June 2025</p>
                        <img
                            style="width: 24px"
                            src="{{ asset('images/heart-fill-20.svg') }}"
                            alt="Heart fill progress"
                        />
                    </div>
                </div>
                <div class="lh-message-body">
                    <div class="d-flex">
                    <h4>Patricia, 46, F semi-retired gold digger</h4>

                    <div class="lh-mini-dropdown-wrapper">
                        <div role="button" class="lh-mini-dropdown-btn">
                        <img
                            src="{{ asset('icons/ellipsis.svg') }}"
                            class="lh-small-icon"
                            alt="Share icon"
                        />
                        </div>

                        <div class="lh-mini-dropdown">
                        <a href="#">
                            <span style="color: var(--red);">Report message</span>
                        </a>
                        <a href="#">
                            <span style="color: var(--red);">Block account</span>
                        </a>
                        </div>
                    </div>

                    </div>
                    <p class="lh-text-paragraph">
                    {{ $message->content }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="bottom">
            <div class="container-sm">
                <button id="ctaButton1" class="lh-button mb-2">
                <img
                    src="{{ asset('icons/envelope-border.svg') }}"
                    alt="Envelope border icon"
                />
                Write a reply
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- end pop up -->
@endsection
@section('script')
<script src="{{ asset('js/message.js') }}"></script>
<script>
    // const popup = document.getElementById("popup");

    // const togglePopup = (show) => popup.classList.toggle("active", show);

    // clickAction(".lh-feed-card", () => togglePopup(true));
    // clickAction(".close-popup", () => togglePopup(false));

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