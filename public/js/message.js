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
        const is_read = messages[0].is_read;
  
        // Fill popup
        const popupBody = document.getElementById("popupBody");
        popupBody.innerHTML = messages.map(msg => `
          <div class="lh-message-card">
            <div class="lh-message-header">
              <div class="left">
                <p class="lh-text-small">From</p>
                <h4 class="lh-sub-title">${msg.sender_name}</h4>
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
        document.getElementById("popup").classList.add("active");
  
        // Update envelope icon in list
        // const img = item.querySelector("img[alt='Envelope icon']");
        // if (img) img.src = "/images/envelope-open.png";
      } catch (err) {
        console.error("Failed to fetch message:", err);
      }
    });
  });

  clickAction(".close-popup", (e) => {
    document.getElementById("popup").classList.remove("active");
  });
  