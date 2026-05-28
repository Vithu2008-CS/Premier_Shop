{{--
    admin/settings/index.blade.php — System settings form
    =======================================================
    Tabbed sections: Shop Info (name, address, phone, logo), Shipping (flat rate,
    free threshold, Google Maps API key), Loyalty (points per £, redemption rate),
    Email (SMTP settings), Maintenance mode toggle.
    PATCH → admin.settings.update → SettingController::update()
    Settings persisted via Setting model (column or other_settings JSON blob).
--}}
@extends('layouts.admin_noble')
@section('title', 'System Settings')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">System Settings</li>
  </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card grid-margin stretch-card">
            <div class="card-body">
                <h6 class="card-title">General Shop Settings</h6>
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    
                    {{-- Hidden coordinate fields --}}
                    <input type="hidden" name="origin_latitude" id="origin_latitude" value="{{ $settings->other_settings['origin_latitude'] ?? '' }}">
                    <input type="hidden" name="origin_longitude" id="origin_longitude" value="{{ $settings->other_settings['origin_longitude'] ?? '' }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shop Name</label>
                            <input type="text" name="shop_name" class="form-control" value="{{ $settings->shop_name }}" placeholder="E.g., Premier Shop">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Address (Origin)</label>
                            <input type="text" name="origin_address" id="origin_address" class="form-control" value="{{ $settings->origin_address }}" placeholder="Full store address">
                        </div>
                    </div>

                    {{-- Map Location Picker --}}
                    <div class="row mt-2">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Shop Location on Map</label>
                            <div id="settingsMap" style="height: 320px; border-radius: 12px; border: 1px solid #e8ebf1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 10px;"></div>
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle text-primary me-1"></i> Drag the marker or click on the map to set your shop's physical location. The address text above will be updated automatically via reverse-geocoding.
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="icon-sm mr-2"></i> Update General Info
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card grid-margin stretch-card">
            <div class="card-body">
                <h6 class="card-title">Shop Opening Hours</h6>
                
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-4">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Opening Time</th>
                                    <th>Closing Time</th>
                                    <th>Closed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                @endphp
                                @foreach($days as $day)
                                    <tr>
                                        <td class="font-weight-bold text-capitalize align-middle">{{ $day }}</td>
                                        @php
                                            $otherSettings = $settings->other_settings ?? [];
                                            $shopHours = $otherSettings['shop_hours'] ?? [];
                                            $hours = $shopHours[$day] ?? [];
                                            $openTime = $hours['open'] ?? '';
                                            $closeTime = $hours['close'] ?? '';
                                            $isClosed = $hours['closed'] ?? false;
                                        @endphp
                                        <td>
                                            <input type="time" name="shop_hours[{{ $day }}][open]" class="form-control form-control-sm" value="{{ $openTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <input type="time" name="shop_hours[{{ $day }}][close]" class="form-control form-control-sm" value="{{ $closeTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                        </td>
                                        <td class="align-middle">
                                            <div class="form-check form-switch mb-0">
                                                <input type="hidden" name="shop_hours[{{ $day }}][closed]" value="0">
                                                <input class="form-check-input closed-toggle" type="checkbox" name="shop_hours[{{ $day }}][closed]" value="1" {{ $isClosed ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Shop Notice Banner (Optional, displayed on homepage)</label>
                        <textarea name="shop_notice" class="form-control" rows="2" placeholder="E.g., Special holiday hours in effect!">{{ ($settings->other_settings['shop_notice'] ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="icon-sm mr-2"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card grid-margin stretch-card">
            <div class="card-body">
                <h6 class="card-title text-primary"><i class="bi bi-star flex-shrink-0 me-2"></i> Loyalty Rewards System</h6>
                
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    
                    @php $otherSettings = $settings->other_settings ?? []; @endphp
                    
                    <div class="form-check form-switch mb-3 mt-3">
                        <input type="hidden" name="loyalty_enabled" value="0">
                        <input class="form-check-input" type="checkbox" name="loyalty_enabled" value="1" id="loyaltyEnabledToggle" {{ isset($otherSettings['loyalty_enabled']) && $otherSettings['loyalty_enabled'] ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="loyaltyEnabledToggle">Enable Loyalty Points</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">POINTS EARNED PER £1 SPENT</label>
                        <div class="input-group">
                            <input type="number" name="points_per_pound" class="form-control fw-bold fs-5" value="{{ $otherSettings['points_per_pound'] ?? 1 }}" min="1">
                            <span class="input-group-text bg-light text-muted">Pts</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">VALUE OF 1 POINT (£)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">£</span>
                            <input type="number" step="0.01" name="points_redemption_value" class="form-control fw-bold fs-5 text-primary" value="{{ $otherSettings['points_redemption_value'] ?? 0.01 }}" min="0.01">
                        </div>
                        <small class="form-text text-muted mt-2 d-block">Example: 0.01 means 100 points = £1.00 discount.</small>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold pb-2 pt-2">
                            Save Loyalty Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card grid-margin stretch-card text-center d-none d-lg-flex">
            <div class="card-body py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width:80px;height:80px;border-radius:50%;">
                        <i data-feather="clock" class="icon-lg"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold mb-3">Shop Hours Format</h5>
                <p class="text-muted small mb-0 px-3">
                    Toggle "Closed" if the shop is closed for the entire day.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.closed-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const tr = this.closest('tr');
                const inputs = tr.querySelectorAll('input[type="time"]');
                inputs.forEach(input => {
                    input.disabled = this.checked;
                    if(this.checked) input.value = '';
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Premium style details for Leaflet Map in Settings */
    #settingsMap {
        transition: all 0.3s ease;
        z-index: 5;
    }
    #settingsMap:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important;
    }
    /* Style for leaflet popup in admin settings */
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-family: inherit;
    }
    .leaflet-popup-content {
        font-weight: 500;
        color: #0f172a;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Elements
    const latInput = document.getElementById('origin_latitude');
    const lngInput = document.getElementById('origin_longitude');
    const addressInput = document.getElementById('origin_address');
    
    // 2. Initial coordinates
    let initialLat = parseFloat(latInput.value);
    let initialLng = parseFloat(lngInput.value);
    
    // Default to London (SW1A 1AA) if coordinates are not configured
    const defaultLat = 51.5074;
    const defaultLng = -0.1278;
    const isUsingDefault = isNaN(initialLat) || isNaN(initialLng);
    
    const startLat = isUsingDefault ? defaultLat : initialLat;
    const startLng = isUsingDefault ? defaultLng : initialLng;
    
    // 3. Initialize Map
    const map = L.map('settingsMap').setView([startLat, startLng], isUsingDefault ? 12 : 16);
    
    // Modern tile layer (OpenStreetMap / CartoDB Voyager feels very clean and premium)
    const activeTheme = localStorage.getItem('admin_theme') || 'light';
    const tileUrl = activeTheme === 'dark' 
        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' 
        : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
        
    const attribution = activeTheme === 'dark'
        ? '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        : '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';

    L.tileLayer(tileUrl, {
        attribution: attribution,
        maxZoom: 20
    }).addTo(map);
    
    // 4. Create Draggable Marker
    const marker = L.marker([startLat, startLng], {
        draggable: true,
        autoPan: true
    }).addTo(map);
    
    if (!isUsingDefault) {
        marker.bindPopup("<b>Shop Location</b><br>Currently configured origin.").openPopup();
    } else {
        marker.bindPopup("<b>Default Location</b><br>Drag me to set your shop!").openPopup();
    }
    
    // 5. If using default, but an address exists, geocode it!
    if (isUsingDefault && addressInput.value.trim().length > 0) {
        const address = addressInput.value.trim();
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], 16);
                    marker.setLatLng([lat, lon]);
                    latInput.value = lat;
                    lngInput.value = lon;
                    marker.bindPopup("<b>Geocoded Address</b><br>Centered based on address text.").openPopup();
                }
            })
            .catch(err => console.error("Error geocoding initial address:", err));
    }
    
    // 6. Geolocation Callback (Reverse Geocoding)
    let geocodeTimeout = null;
    function reverseGeocode(lat, lng) {
        if (geocodeTimeout) clearTimeout(geocodeTimeout);
        
        geocodeTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        addressInput.value = data.display_name;
                        marker.bindPopup(`<b>Location Set!</b><br>${data.name || 'Shop Origin'}`).openPopup();
                    }
                })
                .catch(err => {
                    console.error("Error reverse geocoding coordinates:", err);
                    marker.bindPopup("<b>Location Set!</b><br>Address lookup limit reached.").openPopup();
                });
        }, 800); // 800ms debounce
    }
    
    // 7. Event Handlers
    function updateCoordinates(lat, lng, doGeocode = true) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        if (doGeocode) {
            reverseGeocode(lat, lng);
        }
    }
    
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng, true);
    });
    
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        marker.setLatLng([lat, lng]);
        updateCoordinates(lat, lng, true);
    });
});
</script>
@endpush

