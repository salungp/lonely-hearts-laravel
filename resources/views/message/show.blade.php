@extends('layouts.app')
@section('title', 'Home Page')
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-between">
        <h1 class="lh-title mb-3">Messages</h1>
        <a href="{{ route('message.sent') }}" class="lh-link" style="padding: 0 !important; text-align: right;">Sent</a>
    </div>

    @if (session('error'))
        <div class="lh-alert mb-3 lh-alert-error" id="alert">
        {{ session('error') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    @if (session('success'))
        <div class="lh-alert mb-3 lh-alert-success" id="alert">
        {{ session('success') }}
        <button class="lh-alert-close" type="button">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
        </button>
        </div>
    @endif

    @foreach ($messages as $message)
        <!-- Feed list -->
        <div data-target="{{ $message->sender->id }}" id="messageItem3" class="lh-feed-card gap-2 d-flex flex-direction-row text-decoration-none text-dark">

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
                <h4 class="lh-sub-title">{{ $message->sender->name }}</h4>
                <p class="lh-text-small">{{ substr($message->content, 0, 50) . '...' }}</p>
            </div>

            @if ($message->conversation->progress == '0%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-0.svg') }}" alt="Heart fill progress" />
            @elseif ($message->conversation->progress == '25%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-25.svg') }}" alt="Heart fill progress" />
            @elseif ($message->conversation->progress == '50%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-50.svg') }}" alt="Heart fill progress" />
            @elseif ($message->conversation->progress == '75%')
                <img style="width: 24px" src="{{ asset('images/heart-fill-75.svg') }}" alt="Heart fill progress" />
            @elseif ($message->conversation->progress == '100%')
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
                        <h4 class="lh-sub-title">{{ $message->sender->name }}</h4>
                    </div>
                    <div class="right">
                        <p class="lh-text-small">12, June 2025</p>
                        <img
                            style="width: 24px"
                            src="{{ asset('images/heart-fill-25.svg') }}"
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

<!-- Reply container -->
<div id="popup3" class="lh-popup" style="z-index: 999999999999999;">
    <div class="lh-popup-header">
        <button id="closePopup3">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>

    <div id="popupBody"></div>

    <div class="lh-popup-body">
        <div class="container-sm">
            <div class="d-flex justify-content-between">
                <h2 class="lh-title mb-3" style="text-align: left">Reply</h2>
                <span id="dearDate"></span>
            </div>

            <h3 style="font-family: 'Merriweather'; text-align: left">Dear <span id="dearName"></span></h3>

            <form id="replyForm">
                @csrf
                <div style="position: relative; margin-bottom: 20px;">
                    <textarea
                        class="lh-textarea"
                        oninput="updateLHtextarea()"
                        name="content"
                        id="lh-textarea"
                        maxlength="300"
                        placeholder="Write your own ad"
                        ></textarea>
                    <div class="lh-textarea-info">
                        <span id="lh-textarea-info">0</span>/300
                    </div>
                    <button
                        class="d-flex lh-box-no"
                        style="
                            position: absolute;
                            bottom: 6px;
                            left: 16px;
                            z-index: 99;
                        "
                        id="showPopup"
                    >
                    Help me write
                    </button>
                </div>

                <button id="ctaButton3" class="lh-button mb-2">
                    <img
                    src="{{ asset('icons/envelope-border.svg') }}"
                    alt="Envelope border icon"
                    type="submit"
                    />
                    Send reply
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Message pop up component -->
<div id="messagePopup" class="lh-popup" style="z-index: 999999999999999;">
    <div class="lh-popup-header">
        <button id="closeMessagePopup">
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>

    <div class="lh-popup-body">
        <div class="container-sm">
            <div class="d-flex justify-content-center" style="margin-bottom: 20px">
                <img
                style="width: 100px;margin-top: 30px;"
                src="{{ asset('images/envelope-icon.png') }}"
                alt="Envelope icon symbol of lonely hearts"
                />
            </div>

            <h2 id="messagePopupTitle" style="margin-bottom: 30px;"></h2>

            <button class="lh-button" id="ctaMessagePopup">OK</button>
        </div>
    </div>

</div>
@endsection
@section('script')
<script src="{{ asset('js/message.js') }}"></script>
<script>
    const popup = document.getElementById("popup");
    const popup3 = document.getElementById("popup3");
    const dearName = document.getElementById("dearName");
    const dearDate = document.getElementById("dearDate");
    const replyForm = document.getElementById("replyForm");
    const replyContent = document.getElementById("lh-textarea");
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.content : '';

    clickAction("#ctaButton1", (e) => {
        popup3.classList.add("active");
        popup.classList.remove("active");
        dearName.innerHTML = globalMessages[0].sender_name;
        dearDate.innerHTML = globalMessages[0].created_at;
    });

    clickAction("#closePopup3", (e) => {
        popup3.classList.remove("active");
    });

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

    function showMessagePopup(status, message) {
        const messagePopup = document.getElementById("messagePopup");
        const messagePopupTitle = document.getElementById("messagePopupTitle");

        messagePopup.classList.add("active");
        messagePopupTitle.innerHTML = message;

        clickAction("#closeMessagePopup", (e) => {
            messagePopup.classList.remove("active");
        });

        clickAction("#ctaMessagePopup", (e) => {
            messagePopup.classList.remove("active");
        });
    }

    // Send a reply
    replyForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const content = replyContent.value;
        if (!content.trim()) return;

        try {
            const conversationId = globalMessages[0]?.conversation_id;
            if (!conversationId) {
                alert("No conversation selected!");
                return;
            }

            const res = await fetch(`/conversations/${conversationId}/messages`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ content })
            });

            const newMessage = await res.json();

            // Add new message to globalMessages and update UI
            globalMessages.push(newMessage);
            const popupBody = document.getElementById("popupBody");
            popupBody.innerHTML += `
            <div class="lh-message-card">
                <div class="lh-message-header">
                <div class="left">
                    <p class="lh-text-small">From</p>
                    <h4 class="lh-sub-title">${newMessage.sender_name}</h4>
                </div>
                <div class="right">
                    <p class="lh-text-small">${new Date(newMessage.created_at).toLocaleString()}</p>
                </div>
                </div>
                <div class="lh-message-body">
                <p class="lh-text-paragraph">${newMessage.content}</p>
                </div>
            </div>
            `;

            popup3.classList.remove("active");

            showMessagePopup("success", "The message has been sent!");

            // Clear input
            replyContent.value = "";

        } catch (err) {
            console.error("Failed to send reply:", err);
        }
    });

</script>
@endsection