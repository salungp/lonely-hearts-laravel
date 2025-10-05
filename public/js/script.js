let locations = [
  "All Locations",
  "London",
  "Birmingham",
  "Manchester",
  "Leeds",
  "Sheffield",
  "Liverpool",
  "Bristol",
  "Newcastle upon Tyne",
  "Sunderland",
  "Leicester",
  "Coventry",
  "Kingston upon Hull",
  "Bradford",
  "Stoke-on-Trent",
  "Wolverhampton",
  "Nottingham",
  "Derby",
  "Southampton",
  "Portsmouth",
  "Plymouth",
  "Brighton",
  "Reading",
  "Northampton",
  "Luton",
  "Swindon",
  "Milton Keynes",
  "Oxford",
  "Cambridge",
  "York",
  "Blackpool",
  "Middlesbrough",
  "Bolton",
  "Stockport",
  "Warrington",
  "Huddersfield",
  "Preston",
  "Norwich",
  "Peterborough",
  "Exeter",
  "Chelmsford",
  "Gloucester",
  "Bath",
  "Colchester",
  "Ipswich",
  "Chester",
  "Dundee",
  "Edinburgh",
  "Glasgow",
  "Aberdeen",
  "Belfast"
];

const selections = {
  height: "Average height",
  hair: "Black hair",
  eyes: "Brown eyes",
  behavior: "Kind",
  seeking: "Friends",
  hobby: "Reading"
};

function updateDescription(idText) {
  const textarea = document.getElementById(idText);

  // Collect all dropdown wrappers dynamically
  const wraps = document.querySelectorAll(".lh-dropdown-wrap");

  let sentence = "";

  wraps.forEach((wrap, index) => {
    const hiddenInput = wrap.querySelector("input[type='hidden']");
    const value = hiddenInput ? hiddenInput.value : "";
    const prefixSpan = wrap.previousElementSibling?.querySelector("span");

    const prefix = prefixSpan ? prefixSpan.textContent.trim() : "";

    if (value) {
      if (sentence === "") {
        // First item: just use prefix + value
        sentence += `${prefix} ${value}`;
      } else {
        // Next items: add space then prefix + value
        sentence += ` ${prefix} ${value}`;
      }
    }
  });

  if (sentence) {
    sentence = sentence.trim() + ".";
  }

  textarea.value = sentence;
}


// Render location function
// Render list dynamically
function renderLocations(list, listId, location) {
  const locationList = document.getElementById(listId);
  const locationId = document.getElementById(location);

  locationList.innerHTML = "";
  list.forEach((loc) => {
      const li = document.createElement("li");
      li.textContent = loc;
      li.addEventListener("click", () => {
          selectedLocation.textContent = loc;
          locationId.value = loc;
          locationPopup.classList.remove("active");
      });
      locationList.appendChild(li);
  });
}

// Get the current location via button
document.addEventListener("DOMContentLoaded", () => {
  const locationButtons = document.querySelectorAll(".current-location-btn");

  if (!locationButtons.length) {
    // Tidak ada tombol di halaman ini → langsung keluar
    return;
  }

  locationButtons.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      e.stopPropagation();

      if (!navigator.geolocation) {
        alert("Geolocation not supported by your browser.");
        return;
      }

      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;

          try {
            const response = await fetch(
              `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`
            );
            const data = await response.json();

            if (data && data.address) {
              const city =
                data.address.city ||
                data.address.town ||
                data.address.village ||
                data.address.county;

              if (city) {
                selectedLocation.textContent = city;
                locationId.value = city;
              } else {
                selectedLocation.textContent = "Unknown location";
              }
            } else {
              selectedLocation.textContent = `Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`;
            }
          } catch (error) {
            alert("Could not get address from coordinates.");
            selectedLocation.textContent = `Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`;
          }

          locationPopup.classList.remove("active");
        },
        (error) => alert("Permission denied or unavailable")
      );
    });
  });
});


class BootstrapPinInput {
    constructor(options) {
      this.inputs = document.querySelectorAll(".pin-input-field");
      this.submitBtn = document.getElementById("submitBtn");
      this.messageContainer = document.getElementById("messageContainer");
      this.messageAlert = document.getElementById("messageAlert");
      this.loadingSpinner = document.getElementById("loadingSpinner");
      this.correctPin = "123456"; // Ganti dengan PIN yang diinginkan
      this.condition = options;
  
      this.init();
    }
  
    init() {
      // Event listeners untuk setiap input
      this.inputs.forEach((input, index) => {
        input.addEventListener("input", (e) => this.handleInput(e, index));
        input.addEventListener("keydown", (e) => this.handleKeydown(e, index));
        input.addEventListener("paste", (e) => this.handlePaste(e, index));
        input.addEventListener("focus", () => this.clearError());
        input.addEventListener("blur", () => this.updateInputState(input));
      });
  
      // Event listeners untuk tombol
      if (this.condition) {
          this.submitBtn.addEventListener("click", () => this.handleSubmit());
      } else {
          return;
      }
  
      
    }
  
    handleInput(e, index) {
      const value = e.target.value;
  
      // Hanya terima angka
      if (!/^\d$/.test(value)) {
        e.target.value = "";
        return;
      }
  
      // Update visual state
      this.updateInputState(e.target);
  
      // Pindah ke input berikutnya
      if (value && index < this.inputs.length - 1) {
        this.inputs[index + 1].focus();
      }
  
      this.updateSubmitButton();
      this.clearError();
    }
  
    handleKeydown(e, index) {
      // Handle backspace
      if (e.key === "Backspace") {
        if (!e.target.value && index > 0) {
          this.inputs[index - 1].focus();
          this.inputs[index - 1].value = "";
          this.updateInputState(this.inputs[index - 1]);
        }
      }
  
      // Handle arrow keys
      if (e.key === "ArrowLeft" && index > 0) {
        e.preventDefault();
        this.inputs[index - 1].focus();
      }
      if (e.key === "ArrowRight" && index < this.inputs.length - 1) {
        e.preventDefault();
        this.inputs[index + 1].focus();
      }
  
      // Handle Enter
      if (e.key === "Enter") {
        e.preventDefault();
        this.handleSubmit();
      }
  
      this.updateSubmitButton();
    }
  
    handlePaste(e, index) {
      e.preventDefault();
      const pastedData = e.clipboardData.getData("text");
      const digits = pastedData.replace(/\D/g, "").split("");
  
      // Isi input dengan digit yang di-paste
      digits.forEach((digit, i) => {
        if (index + i < this.inputs.length) {
          this.inputs[index + i].value = digit;
          this.updateInputState(this.inputs[index + i]);
        }
      });
  
      // Focus pada input berikutnya atau input terakhir
      const nextEmptyIndex = Math.min(
        index + digits.length,
        this.inputs.length - 1
      );
      this.inputs[nextEmptyIndex].focus();
  
      this.updateSubmitButton();
      this.clearError();
    }
  
    updateInputState(input) {
      // Update visual state berdasarkan isi input
      if (input.value) {
        input.classList.add("filled");
      } else {
        input.classList.remove("filled");
      }
    }
  
    updateSubmitButton() {
      const allFilled = Array.from(this.inputs).every(
        (input) => input.value !== ""
      );
      this.submitBtn.disabled = !allFilled;
    }
  
    async handleSubmit() {
      const pin = Array.from(this.inputs)
        .map((input) => input.value)
        .join("");
  
      if (pin.length !== this.inputs.length) {
        this.showMessage("Silakan lengkapi semua digit PIN", "danger");
        return;
      }
  
      // Show loading state
      this.setLoadingState(true);
  
      // Simulasi delay untuk demo (hapus ini di production)
      await new Promise((resolve) => setTimeout(resolve, 1000));
  
      if (pin === this.correctPin) {
        this.showMessage("✅ Box found success redirected...", "success");
  
        // Redirect ke detail.html setelah 1.5 detik
        setTimeout(() => {
          window.location.href = "ad-detail.html";
        }, 1500);
      } else {
        this.showMessage("❌ The PO number is incorrect.", "danger");
        this.showError();
      }
  
      this.setLoadingState(false);
    }
  
    setLoadingState(loading) {
      if (loading) {
        this.submitBtn.disabled = true;
        this.loadingSpinner.classList.remove("d-none");
        this.submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
      } else {
        this.updateSubmitButton();
        this.loadingSpinner.classList.add("d-none");
        this.submitBtn.innerHTML = "View Message";
      }
    }
  
    showError() {
      this.inputs.forEach((input) => {
        input.classList.add("error");
        setTimeout(() => {
          input.classList.remove("error");
        }, 500);
      });
    }
  
    clearError() {
      this.inputs.forEach((input) => input.classList.remove("error"));
    }
  
    showMessage(text, type) {
      this.messageAlert.className = `alert alert-${type} mb-0`;
      this.messageAlert.innerHTML = text;
      this.messageContainer.classList.add("show");
  
      // Auto hide setelah 4 detik kecuali error
      if (type !== "danger") {
        setTimeout(() => {
          this.messageContainer.classList.remove("show");
        }, 4000);
      }
    }
  
    clearAll() {
      this.inputs.forEach((input) => {
        input.value = "";
        input.classList.remove("filled", "error");
      });
      this.inputs[0].focus();
      this.updateSubmitButton();
      this.messageContainer.classList.remove("show");
    }
  }
  
  function getOrdinal(n) {
    const s = ["th", "st", "nd", "rd"];
    const v = n % 100;
    return s[(v - 20) % 10] || s[v] || s[0];
  }
  
  function updateDate() {
    const now = new Date();
  
    const days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
    const months = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ];
  
    const dayName = days[now.getDay()];
    const date = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();
  
    const formatted = `${dayName} ${date}${getOrdinal(date)} ${month} ${year}`;
    document.getElementById("current-date").textContent = formatted;
  }
  
  // Initial run
  updateDate();
  
  // Optional: update every second (real-time)
  setInterval(updateDate, 1000);
  
  // Navbar dropdown toggle menu
  const hamburger = document.getElementById("lhHamburger");
  const menu = document.getElementById("lhMobileMenu");
  
  // Toggle menu
  hamburger.addEventListener("click", (e) => {
    e.stopPropagation(); // Prevent it from triggering the document click
    menu.classList.toggle("active");
  });
  
  // Close when clicking outside
  document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !hamburger.contains(e.target)) {
      menu.classList.remove("active");
    }
  });
  
  // Textarea number character info
  function updateLHtextarea() {
    const textarea = document.getElementById("lh-textarea");
    const counter = document.getElementById("lh-textarea-info");
    counter.textContent = textarea.value.length;
  }
  
  // const selections = {}; // store selected values

  // // Toggle dropdown
  // document.querySelectorAll(".lh-dropdown-button").forEach((btn) => {
  //   btn.addEventListener("click", function () {
  //     const wrap = this.parentElement;
  //     document.querySelectorAll(".lh-dropdown-wrap").forEach((el) => {
  //       if (el !== wrap) el.classList.remove("open");
  //     });
  //     wrap.classList.toggle("open");
  //   });
  // });

  // // Select option
  // document.querySelectorAll(".lh-option").forEach((option) => {
  //   option.addEventListener("click", function () {
  //     const wrap = this.closest(".lh-dropdown-wrap");
  //     const btn = wrap.querySelector(".lh-dropdown-button");
  //     const field = wrap.dataset.field;

  //     // Save selection
  //     selections[field] = this.textContent;

  //     // Update button text
  //     btn.textContent = this.textContent;

  //     // Close dropdown
  //     wrap.classList.remove("open");

  //     // Update textarea
  //     updateDescription();
  //   });
  // });

  // // Close dropdown when clicking outside
  // document.addEventListener("click", function (e) {
  //   if (!e.target.closest(".lh-dropdown-wrap")) {
  //     document.querySelectorAll(".lh-dropdown-wrap").forEach((el) => {
  //       el.classList.remove("open");
  //     });
  //   }
  // });

  // function updateDescription() {
  //   const textarea = document.getElementById("description");

  //   // Combine selections into a sentence-like string
  //   const sentence = Object.values(selections).join(", ");

  //   textarea.value = sentence;
  // }
  
  // Countryselector for select phone number options
  class CountrySelector {
    constructor(options) {
      this.triggerEl = document.querySelector(options.trigger);
      this.popupEl = document.querySelector(options.popup);
      this.searchEl = this.popupEl.querySelector("input");
      this.inputHidden = document.getElementById(options.input);
      this.listEl = this.popupEl.querySelector("ul");
      this.countries = [];
  
      this.init(options.apiUrl);
    }
  
    async init(apiUrl) {
      // 1. Fetch countries
      this.countries = await this.fetchCountries(apiUrl);
  
      // 2. Render
      this.renderCountries(this.countries);
  
      // 3. Setup events
      this.setupEvents();
    }
  
    async fetchCountries(apiUrl) {
      try {
        const res = await fetch(apiUrl);
        const data = await res.json();
  
        return data
          .filter(c => c.idd?.root) // skip countries without phone code
          .map(c => ({
            name: c.name.common,
            code: c.idd.root + (c.idd.suffixes?.[0] || ""),
            flag: c.flags.svg
          }))
          .sort((a, b) => a.name.localeCompare(b.name));
      } catch (e) {
        console.error("Failed to fetch countries:", e);
        return [];
      }
    }
  
    setupEvents() {
      // Open popup
      this.triggerEl.addEventListener("click", () => {
        this.popupEl.classList.remove("hidden");
        this.searchEl.value = "";
        this.renderCountries(this.countries);
        this.searchEl.focus();
      });
  
      // Close popup when clicking outside
      this.popupEl.addEventListener("click", (e) => {
        if (e.target === this.popupEl) {
          this.popupEl.classList.add("hidden");
        }
      });
  
      // Search countries
      this.searchEl.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = this.countries.filter(
          (c) =>
            c.name.toLowerCase().includes(query) ||
            c.code.includes(query) ||
            c.flag.includes(query)
        );
        this.renderCountries(filtered);
      });
  
      // Select country
      this.listEl.addEventListener("click", (e) => {
        const li = e.target.closest("li");
        if (!li) return;
  
        const code = li.dataset.code;
        const selected = this.countries.find((c) => c.code === code);
  
        if (selected) {
          const display = `<img style="width:20px" src='${selected.flag}' alt='Flag image icon' /> ${selected.code}`;
          this.triggerEl.innerHTML = display;
          this.inputHidden.value = selected.code;
          this.popupEl.classList.add("hidden");
        }
      });
    }
  
    renderCountries(list) {
      this.listEl.innerHTML = "";
      list.forEach((c) => {
        const li = document.createElement("li");
        li.innerHTML = `<img style="width:20px" src='${c.flag}' alt='Flag image icon' /> ${c.name} (${c.code})`;
        li.dataset.code = c.code;
        this.listEl.appendChild(li);
      });
    }
  
    setDefault(code) {
      const country = this.countries.find((c) => c.code === code);
      if (country) {
        this.triggerEl.innerHTML = `<img style="width:20px" src='${country.flag}' alt='Flag image icon' /> ${country.code}`;
        this.inputHidden.value = country.code;
      }
    }
  }
  
  // Handle add or remove class
  function clickAction(selector, callback) {
    const elements = document.querySelectorAll(selector);
    elements.forEach((el, index) => {
      el.addEventListener("click", (e) => callback(e.target, index, el));
    });
  }
  
  function getClass(selector) {
    const elements = document.querySelectorAll(selector);
    let result;
    elements.forEach((el) => {
      return el;
    });
  }
  
  // get id
  function getIdElement(el) {
    return document.getElementById(el);
  }
  
  // redirect page
  function movePage(el, page) {
    clickAction(el, () => {
        window.location.href = page;
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    // Open popup
    document.querySelectorAll("[data-target]").forEach(trigger => {
      trigger.addEventListener("click", () => {
        const modalId = trigger.getAttribute("data-target");
        if (!modalId) return; // skip if missing or empty
        const modal = document.querySelector(`.lh-popup#${modalId}`);
        modal?.classList.add("active"); // safe optional chaining
      });
    });    
  
    // Close popup
    document.querySelectorAll(".lh-popup[data-modal]").forEach(modal => {
      modal.querySelectorAll("[data-close]").forEach(closeBtn => {
        closeBtn.addEventListener("click", () => {
          modal.classList.remove("active");
        });
      });
  
      // Optional: close if click outside
      modal.addEventListener("click", e => {
        if (e.target === modal) {
          modal.classList.remove("active");
        }
      });
    });
  
    // Optional: close with ESC
    document.addEventListener("keydown", e => {
      if (e.key === "Escape") {
        document.querySelectorAll(".lh-popup.is-active")
          .forEach(modal => modal.classList.remove("active"));
      }
    });
  });
    
  
    
  