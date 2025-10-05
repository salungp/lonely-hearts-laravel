// 👇 Global variable
let globalMessages = [];

document.querySelectorAll(".lh-feed-card").forEach(item => {
  item.addEventListener("click", async () => {
    const senderId = item.dataset.target;
    if (!senderId) return;

    try {
      const res = await fetch(`/conversations/${senderId}/messages/`, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        }
      });

      if (!res.ok) throw new Error(`Failed to fetch: ${res.status}`);
      const messages = await res.json();
      if (!Array.isArray(messages)) throw new Error("Invalid messages format");

      globalMessages = messages;

      const popupBody = document.getElementById("popupBody");
      popupBody.innerHTML = messages.map(msg => renderMessageCard(msg)).join("");

      // Open popup
      document.getElementById("popup").classList.add("active");

      // Attach handlers for envelopes
      attachEnvelopeHandlers();

    } catch (err) {
      console.error("❌ Error fetching messages:", err);
    }
  });
});

// ✅ Render message card
function renderMessageCard(msg) {
  const displayName = msg.sender_id === loggedInUserId ? "You" : msg.sender_name;
  const senderClass = msg.sender_id === loggedInUserId ? "lh-message-red" : "";
  const isOwn = msg.sender_id === loggedInUserId;

  if (msg.is_read < 1 && !isOwn) {
    return `
      <div class="lh-message-card" style="${isOwn ? "margin-left: 40px" : "margin-right: 40px"}" data-message-id="${msg.id}">
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
          <div class="envelope d-flex justify-content-center align-items-center" data-id="${msg.id}">
            <img style="width: 200px;" src="images/envelope-icon.png" alt="Envelope icon">
          </div>
        </div>
      </div>`;
  } else {
    return `
      <div class="lh-message-card" style="${isOwn ? "margin-left: 40px" : "margin-right: 40px"}" data-message-id="${msg.id}">
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
      </div>`;
  }
}

// ✅ Envelope click handlers (with CSRF)
function attachEnvelopeHandlers() {
  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  if (!tokenMeta) {
    console.error("❌ CSRF meta tag not found!");
    return;
  }

  const csrfToken = tokenMeta.getAttribute("content");

  document.querySelectorAll(".envelope").forEach(env => {
    env.addEventListener("click", async () => {
      const msgId = env.dataset.id;
      if (!msgId) return;

      try {
        const res = await fetch(`/messages/${msgId}/read`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            "Accept": "application/json"
          },
          body: JSON.stringify({ is_read: 1 })
        });

        if (!res.ok) throw new Error(`Request failed with status ${res.status}`);

        const data = await res.json();
        if (!data.success) throw new Error("Server did not return success");

        // Update local message
        const msgIndex = globalMessages.findIndex(m => m.id == msgId);
        if (msgIndex > -1) {
          globalMessages[msgIndex].is_read = 1;
        }

        // Re-render popup
        const popupBody = document.getElementById("popupBody");
        popupBody.innerHTML = globalMessages.map(msg => renderMessageCard(msg)).join("");

        // Rebind remaining envelopes
        attachEnvelopeHandlers();

      } catch (err) {
        console.error("❌ Error marking message as read:", err);
      }
    });
  });
}

clickAction(".close-popup", () => {
  document.getElementById("popup").classList.remove("active");
});
