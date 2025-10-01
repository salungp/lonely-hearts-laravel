<!-- Location pop up -->
<div class="lh-popup" id="locationPopup" data-modal>
    <div class="lh-popup-header">
        <button id="closePopupLocation" data-close>
            <img src="{{ asset('icons/close.svg') }}" alt="Close button" />
        </button>
    </div>
    <div class="lh-popup-body">
        <div class="container-sm">
            <h2 class="lh-title mb-3" style="text-align: left">Address</h2>
            <div class="location-field">
                <input
                type="text"
                id="searchInput"
                placeholder="Search location..."
                class="input-none"
                />
                <button class="current-location-btn">
                <img src="{{ asset('icons/location.svg') }}" alt="Pin svg icon">
                </button>
            </div>
            <ul id="locationList"></ul>
        </div>
    </div>
</div>