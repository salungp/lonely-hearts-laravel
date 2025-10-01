// 👇 Global variable
let globalMessages = [];

document.querySelectorAll(".lh-feed-card").forEach(item => {
  item.addEventListener("click", async () => {
    const senderId = item.dataset.target;

    try {
      const res = await fetch(`/conversations/${senderId}/messages/`, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        }
      });

      const messages = await res.json();

      // 👇 Save globally
      globalMessages = messages;

      const is_read = messages[0]?.is_read ?? false;

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
      console.error("Failed to fetch message:", err);
    }
  });
});

clickAction(".close-popup", () => {
  document.getElementById("popup").classList.remove("active");
});
