@extends('layouts.app')
@section('title', 'Create ad | Help me write it')
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
<a href="{{ url('/ad/create') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
    <!-- Form start line -->
    <form action="{{ route('create.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if (session('success'))
          <div class="lh-alert mb-3 lh-alert-success" id="alert">
            {{ session('success') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
        @endif

        @if ($errors->any())
            <div class="lh-alert lh-alert-error d-block mb-3">
                <strong>Whoops!</strong> Please fix the following:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <input type="hidden" name="location" id="location">

        @foreach($options as $opt)
            <div class="sentence d-inline-block text-uppercase mb-2">
                <span>{{ $opt->text }}</span>
            </div>

            @if($opt->input_type === 'dropdown')
                <div class="lh-dropdown-wrap" data-field="{{ $opt->title }}">
                    <button class="lh-dropdown-button" type="button">
                        {{ strtoupper($opt->value[0]) }}
                    </button>
                    <div class="lh-dropdown-menu">
                        <input type="hidden" name="{{ $opt->title }}" value="{{ $opt->value[0] }}">
                        @foreach($opt->value as $val)
                            <div class="lh-option">{{ strtoupper($val) }}</div>
                        @endforeach
                    </div>
                </div>
            @elseif($opt->input_type === 'text')
                <input type="text" name="{{ $opt->title }}" class="input-line" />
            @elseif($opt->input_type === 'textarea')
                <textarea name="{{ $opt->title }}"></textarea>
            @endif
        @endforeach

        <button class="lh-dropdown-button" id="selectedLocation" type="button" data-target="locationPopup">
            LOCATION
        </button>

        <div class="mb-4"></div>

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

        <textarea style="display: none;" name="description" id="description" rows="3" class="w-full mt-3" readonly></textarea>

        <button class="lh-button" type="submit">Continue</button>
        
    </form>
</div>

@include('components.location')

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

$(document).ready(function () {
    const $searchInput = $("#searchInput");
    const $selectedLocation = $("#selectedLocation");

    // Initial setup
    updateDescription("description");
    locations.shift();
    renderLocations(locations, "locationList", "location");

    // Search filter
    $searchInput.on("input", function () {
        const query = $(this).val().toLowerCase();
        const filtered = locations.filter(loc => loc.toLowerCase().includes(query));
        renderLocations(filtered, "locationList", "location");
    });

    // Handle current location button
    $(".current-location-btn").on("click", function (e) {
        e.stopPropagation();

        if (!navigator.geolocation) {
            return alert("Geolocation not supported by your browser.");
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
                            $selectedLocation.text(city);
                            $("#location").val(city);
                        } else {
                            $selectedLocation.text("Unknown location");
                        }
                    } else {
                        $selectedLocation.text(`Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`);
                    }
                } catch (error) {
                    alert("Could not get address from coordinates.");
                    $selectedLocation.text(`Lat: ${lat.toFixed(3)}, Lon: ${lon.toFixed(3)}`);
                }

                $("#locationPopup").removeClass("active");
            },
            () => alert("Permission denied or unavailable")
        );
    });
});
</script>
@endsection