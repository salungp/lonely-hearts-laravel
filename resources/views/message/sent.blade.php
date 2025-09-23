@extends('layouts.app')
@section('title', 'Home Page')
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
        <div data-target="{{ $message->message_id }}" id="messageItem3" class="lh-feed-card gap-2 d-flex flex-direction-row text-decoration-none text-dark">

            @if ($message->is_read == 0)
                <div style="
                        width: 48px;
                        height: 48px;
                        background: url('{{ asset('images/envelope.png') }}');
                        background-size: contain;
                        background-position: center;
                        background-repeat: no-repeat;
                        flex-basis: 48px;
                        flex-grow: 0;
                        flex-shrink: 0;">
                </div>
            @else
                <div style="
                        width: 48px;
                        height: 48px;
                        background: url('{{ asset('images/mail-open.png') }}');
                        background-size: contain;
                        background-position: center;
                        background-repeat: no-repeat;
                        flex-basis: 48px;
                        flex-grow: 0;
                        flex-shrink: 0;">
                </div>
            @endif

            <div class="text-content" style="flex-grow: 1">
              <h4 class="lh-sub-title">{{ $message->ad_owner_name }}</h4>
              <p class="lh-text-small">{{ substr($message->content, 0, 50) . '...' }}</p>
            </div>
            @if ($message->progress == '0%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-0.svg') }}" alt="Heart fill progress" />
            @elseif ($message->progress == '25%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-25.svg') }}" alt="Heart fill progress" />
            @elseif ($message->progress == '50%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-50.svg') }}" alt="Heart fill progress" />
            @elseif ($message->progress == '75%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-75.svg') }}" alt="Heart fill progress" />
            @elseif ($message->progress == '100%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-100.svg') }}" alt="Heart fill progress" />
            @endif
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
                        <h4 class="lh-sub-title">{{ $message->ad_owner_name }}</h4>
                    </div>
                    <div class="right">
                        <p class="lh-text-small">12, June 2025</p>
                        @if ($message->progress == '0%')
                            <img style="width: 24px" src="{{ asset('images/heart-fill-0.svg') }}" alt="Heart fill progress" />
                        @elseif ($message->progress == '25%')
                            <img style="width: 24px" src="{{ asset('images/heart-fill-25.svg') }}" alt="Heart fill progress" />
                        @elseif ($message->progress == '50%')
                            <img style="width: 24px" src="{{ asset('images/heart-fill-50.svg') }}" alt="Heart fill progress" />
                        @elseif ($message->progress == '75%')
                            <img style="width: 24px" src="{{ asset('images/heart-fill-75.svg') }}" alt="Heart fill progress" />
                        @elseif ($message->progress == '100%')
                            <img style="width: 24px" src="{{ asset('images/heart-fill-100.svg') }}" alt="Heart fill progress" />
                        @endif
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
<script>
    let globalSentMessages = [];

    document.querySelectorAll(".lh-feed-card").forEach(item => {
        item.addEventListener("click", async () => {
            const receiverId = item.dataset.target; // 👈 assuming dataset has the receiver user id

            try {
            const res = await fetch(`/conversations/${receiverId}/sent-messages/`, {
                headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
                }
            });

            const messages = await res.json();

            // 👇 Save globally
            globalSentMessages = messages;

            // Fill popup
            const popupBody = document.getElementById("popupBody");
            popupBody.innerHTML = messages.map(msg => `
                <div class="lh-message-card">
                <div class="lh-message-header">
                    <div class="left">
                    <p class="lh-text-small">To</p>
                    <h4 class="lh-sub-title">${msg.receiver_name}</h4>
                    </div>
                    <div class="right">
                    <p class="lh-text-small">${new Date(msg.created_at).toLocaleString()}</p>
                    </div>
                </div>
                <div class="lh-message-body">
                    <p class="lh-text-paragraph">${msg.content}</p>
                </div>
                </div>
            `).join("");

            // Open popup
            // document.getElementById("popup").classList.add("active");

            } catch (err) {
                console.error("Failed to fetch sent messages:", err);
            }
        });
    });

    // clickAction(".close-popup", () => {
    //     document.getElementById("popup").classList.remove("active");
    // });

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