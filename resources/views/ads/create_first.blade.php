@extends('layouts.app')
@section('title', 'Create ad | Ill write it')
@section('meta')
<style>
.lh-photo-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

/* Upload box default state */
.upload-box {
  width: 100%;
  min-height: 160px;
  border: 2px dashed #BD8919;
  background-color: #F4DDAC;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: 0.2s ease;
}

.upload-box:hover {
  background-color: #f7e8c3;
}

/* Camera icon style */
.camera-icon {
  font-size: 2rem;
  color: #b07a20;
}

/* Preview container */
.photo-preview {
  position: relative;
  width: 100%;
  min-height: 160px;
  border: 2px dashed #b07a20;
  background-color: #f3e0b5;
  border-radius: 4px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

/* Image preview style */
.photo-preview img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
  position: relative;
  box-shadow: 0px 4px 3px rgba(0, 0, 0, .1);
}

/* Remove button */
.remove-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  background-color: #fff;
  border-radius: 50%;
  border: none;
  width: 22px;
  height: 22px;
  font-size: 14px;
  cursor: pointer;
  line-height: 1;
  color: #333;
  box-shadow: 0px 4px 3px rgba(0, 0, 0, .2);
}
</style>
@endsection
@section('back')
<a href="{{ url('/ad/create/') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <h1 class="lh-title mb-3">I'll write it</h1>

    <form action="{{ route('create.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="position-relative mb-2">
            <input type="hidden" name="location" id="location">
            <textarea
                class="lh-textarea"
                oninput="updateLHtextarea()"
                name="description"
                id="lh-textarea"
                maxlength="300"
                placeholder="Write your own ad"
            ></textarea>

            <div class="lh-textarea-info">
                <span id="lh-textarea-info">0</span>/300
            </div>

            <button data-target="helpMePopup" type="button" class="d-flex textarea-button position-absolute" id="showPopup">
                Help me write
            </button>
        </div>

        @error('description')
            <div class="text-uppercase text-danger mb-3">{{ $message }}</div>
        @enderror

        <div class="location-field" id="locationField" data-target="locationPopup" style="margin-bottom: 16px;">
            <div class="d-flex align-items-center" style="gap: 12px;">
                <span class="icon">
                    <img src="{{ asset('icons/pin.svg') }}" alt="Pin svg icon">
                </span>
                <span id="selectedLocation">SELECT LOCATION</span>
            </div>
            <button class="current-location-btn" type="button">
                <img src="{{ asset('icons/location.svg') }}" alt="Pin svg icon">
            </button>
        </div>

        <div data-target="uploadPopup" class="location-field file-field mb-4">
            <div class="d-flex align-items-center" style="gap: 12px;">
                <span class="icon">
                    <img src="{{ asset('icons/file.svg') }}" alt="Pin svg icon">
                </span>
                <span id="photoSelectText">SELECT PHOTO</span>
            </div>
            <button class="file-info" type="button">
                <img src="{{ asset('icons/info.svg') }}" alt="Pin svg icon">
            </button>
        </div>
        <input type="file" id="photoUploadReady" class="lh-photo-input" name="photos[]" accept="image/*" multiple hidden>

        <button class="lh-button-secondary mb-2 mt-2" data-bs-toggle="modal" data-bs-target="#staySafeModal" type="button">
            Stay Safe
        </button>

        <button class="lh-button" type="submit">Continue</button>
        <div id="result" class="mt-3" style="white-space: pre-wrap;"></div>
    </form>
</div>

<div data-modal id="helpMePopup" class="lh-popup" style="height: 90vh">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div id="screenOne" class="container-sm">
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

<div data-modal id="uploadPopup" class="lh-popup" style="height: 90vh">
    <div class="lh-popup-header">
        <button data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <!-- Screen one secenario -->
        <div class="container-sm" id="screenOne">
            <h2 class="lh-title" style="text-align: left">Add your photo</h2>
            <p class="mb-3" style="font-family: 'Merriweather'; color: var(--green-dark); text-align: left;">
              You can add a total of 5 photos
            </p>
            <div class="lh-photo-upload mb-3">
        
            <!-- hidden file input -->
            <input type="file" id="photoUpload" class="lh-photo-input" accept="image/*" multiple hidden>
        
            <!-- upload box (clickable trigger) -->
            <div id="uploadBox" class="upload-box lh-photo-add" style="cursor: pointer;">
                <img src="{{ asset('icons/camera.svg') }}" alt="Camera icon on upload field">
            </div>
        
            <!-- live photo preview -->
            <div id="photoPreview" class="photo-preview lh-photo-preview"></div>
            </div>
        
            <button type="button" class="lh-button">
            <span class="btn-text">Set Photo</span>
            </button>
          </div>          
    <!-- End scenario -->
    </div>
</div>

<!-- Modal -->
<div class="modal lh-modal fade"
    id="staySafeModal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog lh-modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header lh-modal-header">
                <h1 class="modal-title lh-modal-title fs-5" id="exampleModalLabel">
                    Stay Safe
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center mb-4">
                    <img
                    style="width: 88px"
                    src="{{ asset('images/stay-safe.png') }}"
                    alt="Stay safe warning icon"
                    />
                </div>

                <h3 style="text-transform: uppercase; text-align: center">
                    Stay safe while connecting with the new people. not to share
                    personal data, don't send money to strangers.
                </h3>
            </div>
            <div class="modal-footer">
                <button class="lh-button" data-bs-dismiss="modal">
                    got it
                </button>
            </div>
        </div>
    </div>
</div>

@include('components.location')

@endsection
@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
    let lhUploadedPhotos = JSON.parse(localStorage.getItem("lhUploadedPhotos") || "[]");

    const fileInputModal = document.getElementById("photoUpload");
    const previewContainer = document.getElementById("photoPreview");
    const uploadTrigger = document.getElementById("uploadBox");
    const fileInputReady = document.getElementById("photoUploadReady");
    const selectPhotoText = document.getElementById("photoSelectText");
    const uploadPopup = document.getElementById("uploadPopup");
    const setPhotoBtn = uploadPopup.querySelector(".lh-button"); // ← your "Set Photo" button

    // === OPEN MODAL TRIGGER ===
    if (selectPhotoText) {
        selectPhotoText.addEventListener("click", () => {
        uploadPopup.classList.add("active");
        renderPreview();
        });
    }

    // === CLOSE MODAL (data-close button) ===
    const closeButtons = uploadPopup.querySelectorAll("[data-close]");
    closeButtons.forEach(btn =>
        btn.addEventListener("click", () => uploadPopup.classList.remove("active"))
    );

    // === UPLOAD BUTTON IN MODAL ===
    if (uploadTrigger && fileInputModal) {
        uploadTrigger.addEventListener("click", () => fileInputModal.click());
    }

    // === HANDLE FILE SELECTION ===
    if (fileInputModal) {
        fileInputModal.addEventListener("change", (e) => {
        const files = Array.from(e.target.files);
        files.forEach((file) => {
            if (lhUploadedPhotos.length < 5) {
            const reader = new FileReader();
            reader.onload = () => {
                lhUploadedPhotos.push({
                name: file.name,
                type: file.type,
                size: file.size,
                dataUrl: reader.result,
                });
                localStorage.setItem("lhUploadedPhotos", JSON.stringify(lhUploadedPhotos));
                renderPreview();
            };
            reader.readAsDataURL(file);
            }
        });
        });
    }

    // === RENDER PREVIEW INSIDE MODAL ===
    function renderPreview() {
        previewContainer.innerHTML = "";
        lhUploadedPhotos.forEach((photo, index) => {
        const wrapper = document.createElement("div");
        wrapper.classList.add("photo-item");
        wrapper.style.position = "relative";
        wrapper.style.display = "inline-block";
        wrapper.style.margin = "6px";

        const img = document.createElement("img");
        img.src = photo.dataUrl;
        img.style.width = "120px";
        img.style.height = "120px";
        img.style.objectFit = "cover";
        img.style.borderRadius = "4px";

        const removeBtn = document.createElement("button");
        removeBtn.textContent = "×";
        removeBtn.classList.add("remove-btn");
        removeBtn.addEventListener("click", () => {
            lhUploadedPhotos.splice(index, 1);
            localStorage.setItem("lhUploadedPhotos", JSON.stringify(lhUploadedPhotos));
            renderPreview();
        });

        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        previewContainer.appendChild(wrapper);
        });
    }

    // === SET PHOTO BUTTON (SYNC TO MAIN FORM) ===
    if (setPhotoBtn) {
    setPhotoBtn.addEventListener("click", (e) => {
        e.preventDefault();

        // clear existing files
        fileInputReady.value = "";

        // rebuild FileList for hidden input
        const dataTransfer = new DataTransfer();
        lhUploadedPhotos.forEach((photo) => {
        const arr = photo.dataUrl.split(",");
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) u8arr[n] = bstr.charCodeAt(n);
        const file = new File([u8arr], photo.name, { type: mime });
        dataTransfer.items.add(file);
        });
        fileInputReady.files = dataTransfer.files;

        console.log("✅ photoUploadReady files:", fileInputReady.files); // debug log

        // update "SELECT PHOTO" label
        if (selectPhotoText) {
        if (lhUploadedPhotos.length > 0) {
            const names = lhUploadedPhotos.map(f => f.name).join(", ");
            selectPhotoText.textContent = names.length > 30 ? names.slice(0, 30) + "..." : names;
        } else {
            selectPhotoText.textContent = "SELECT PHOTO";
        }
        }

        // close popup
        uploadPopup.classList.remove("active");
    });
    }

    // === RESTORE PREVIEW ON LOAD ===
    renderPreview();
    });

    const searchInput = document.getElementById("searchInput");
    const selectedLocation = document.getElementById("selectedLocation");

    clickAction(".lh-tag", (el) => {
        el.classList.toggle("active");
    });

    // Search filter
    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        const filtered = locations.filter((loc) =>
            loc.toLowerCase().includes(query)
        );
        renderLocations(filtered, "locationList", "location");
    });

    document.addEventListener("DOMContentLoaded", () => {
        locations.shift();
        renderLocations(locations, "locationList", "location");
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
</script>
@endsection