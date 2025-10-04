@extends('layouts.app')
@section('title', 'Messages | Sent')
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <div class="d-flex justify-content-between">
        <h1 class="lh-title mb-3">Sent</h1>
        <a href="{{ route('message') }}" class="lh-link" style="padding: 0 !important; text-align: right;">Inbox</a>
    </div>

    @foreach ($messages as $message)
        <!-- Feed list -->
        <div data-target="{{ $message->conversation_id }}" id="messageItem3" class="lh-feed-card gap-2 d-flex flex-direction-row text-decoration-none text-dark">

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
              <h4 class="lh-sub-title">{{ $message->conversation->author->name }}</h4>
              <em style="color: #888;line-height: 120%" class="mb-2 mt-2"><small>{{ $message->conversation->ad->title }}</small></em>
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
            <img src="{{ asset('icons/close.svg') }}" data-close alt="Close button" />
        </button>
    </div>

    <!-- Lh popup body -->
    <div class="lh-popup-body" style="height: 100%; overflow-y: scroll; padding-bottom: 150px">
        <div class="container-sm">
            <div id="popupBody"></div>
            <div class="bottom">
                <div class="container-sm">
                    <button id="ctaButton1" class="lh-button mb-2">
                    <img src="{{ asset('icons/envelope-border.svg') }}" alt="Envelope border icon"/>
                    Write a reply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
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
                    <button onclick="helpWrite()" data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
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
<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh" style="z-index: 999999999999999999999999 !important;">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div class="container-sm" id="screenOne">
            <h2 class="lh-title mb-3" style="text-align: left">Reword it</h2>
            <div id="tags-container" class="tags-container">
                @foreach ($prompts as $style)
                    <button class="lh-tag" data-style="{{ $style }}">{{ $style }}</button>
                @endforeach
            </div>

            <button id="applyStyle" class="lh-button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span class="btn-text">Apply Style</span>
            </button>
        </div>
    <!-- End scenario -->
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

            <a href="" class="lh-button" id="ctaMessagePopup">OK</a>
        </div>
    </div>
</div>
<!-- end pop up -->
@endsection
@section('script')
<script>
    const loggedInUserId = @json(auth()->id());
</script>
<script>
    let globalSentMessages = [];
    const popup = document.getElementById("popup");
    const popup3 = document.getElementById("popup3");
    const dearName = document.getElementById("dearName");
    const dearDate = document.getElementById("dearDate");
    const replyForm = document.getElementById("replyForm");
    const replyContent = document.getElementById("lh-textarea");
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.content : '';

    function helpWrite() {
        const popup = document.getElementById("helpMePopup");

        popup.classList.add("active");
        popup3.classList.remove("active");
    }

    document.addEventListener("DOMContentLoaded", () => {
        let selectedStyle = null;

        // Handle style button clicks
        document.querySelectorAll("#tags-container button").forEach(btn => {
            btn.addEventListener("click", () => {
                selectedStyle = btn.dataset.style;

                // highlight active button
                document.querySelectorAll("#tags-container button").forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");
            });
        });

        // Handle Apply Style
        document.getElementById("applyStyle").addEventListener("click", () => {
            const btn = document.getElementById("applyStyle");
            const spinner = btn.querySelector(".spinner-border");
            const btnText = btn.querySelector(".btn-text");
            const textarea = document.getElementById("lh-textarea");
            const text = textarea.value;

            if (!selectedStyle) {
                alert("Please select a style first.");
                return;
            }

            // Show loading spinner
            btn.disabled = true;
            spinner.classList.remove("d-none");
            btnText.textContent = "Generating...";

            fetch("/ad/apply-style", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ text: text, style: selectedStyle })
            })
                .then(res => res.json())
                .then(data => {
                    textarea.value = data.styled_text;
                    updateLHtextarea();
                    popup3.classList.add("active");
                    document.getElementById("helpMePopup").classList.remove("active");
                })
                .catch(err => {
                    console.error(err);
                    alert("Something went wrong!");
                })
                .finally(() => {
                    // Reset button
                    btn.disabled = false;
                    spinner.classList.add("d-none");
                    btnText.textContent = "Apply Style";
                });
        });
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

    clickAction("#ctaButton1", (e) => {
        popup3.classList.add("active");
        popup.classList.remove("active");
        dearName.innerHTML = globalSentMessages[0].sender_name;
        dearDate.innerHTML = globalSentMessages[0].created_at;
    });

    clickAction("#closePopup3", (e) => {
        popup3.classList.remove("active");
    });

    clickAction("#closePopup", (e) => {
        popup.classList.remove("active");
    });

    // Send a reply
    replyForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const content = replyContent.value;
        if (!content.trim()) return;

        const replyBtn = document.getElementById("ctaButton3");
        const originalBtnText = replyBtn.innerHTML;

        try {
            // 🔹 Set loading state
            replyBtn.disabled = true;
            replyBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Sending...
            `;

            const conversationId = globalSentMessages[0]?.conversation_id;
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

            // Add new message to globalSentMessages and update UI
            globalSentMessages.push(newMessage);
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
            showMessagePopup("error", "Failed to send message!");
        } finally {
            // 🔹 Reset button state
            replyBtn.disabled = false;
            replyBtn.innerHTML = originalBtnText;
        }
    });

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
                popupBody.innerHTML = messages.map(msg => {
                    // If sender is logged in user, override name
                    const displayName = msg.sender_id === loggedInUserId ? "You" : msg.sender_name;
                    const senderClass = msg.sender_id === loggedInUserId ? "lh-message-red" : "";
                    const check = msg.sender_id === loggedInUserId ? true : false;

                    return `
                    <div class="lh-message-card" style="${check ? "margin-left: 40px" : "margin-right: 40px"}">
                        <div class="lh-message-header ${senderClass}">
                        <div class="left">
                            <p class="lh-text-small">From</p>
                            <h4 class="lh-sub-title">${displayName}</h4>
                        </div>
                        <div class="right">
                            <p class="lh-text-small">${new Date(msg.created_at).toLocaleString()}</p>
                        </div>
                        </div>
                        <div class="lh-message-body">
                        <p class="lh-text-paragraph">${msg.content}</p>
                        </div>
                    </div>
                    `;
                }).join("");

                // Open popup
                document.getElementById("popup").classList.add("active");

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