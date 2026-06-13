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
<script nonce="{{ Vite::cspNonce() }}">
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
<style>
    #settingsMap { transition: all 0.3s ease; z-index: 5; }
    #settingsMap:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important; }
    /* Remove Google Maps default UI clutter */
    #settingsMap .gm-style-cc, #settingsMap a[href*="maps.google"] { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=geocoding" crossorigin=""></script>
<script nonce="{{ Vite::cspNonce() }}">
document.addEventListener('DOMContentLoaded', function () {
    const latInput     = document.getElementById('origin_latitude');
    const lngInput     = document.getElementById('origin_longitude');
    const addressInput = document.getElementById('origin_address');
    const geocoder     = new google.maps.Geocoder();

    const defaultLat = 51.5074;
    const defaultLng = -0.1278;
    let initialLat   = parseFloat(latInput.value);
    let initialLng   = parseFloat(lngInput.value);
    const isDefault  = isNaN(initialLat) || isNaN(initialLng);
    const startLat   = isDefault ? defaultLat : initialLat;
    const startLng   = isDefault ? defaultLng : initialLng;

    const isDark = document.documentElement.getAttribute('data-admin-theme') === 'dark';
    const NIGHT_STYLES = [
        { elementType: 'geometry',                stylers: [{ color: '#0c1427' }] },
        { elementType: 'labels.text.fill',        stylers: [{ color: '#8ec3b9' }] },
        { elementType: 'labels.text.stroke',      stylers: [{ color: '#1a3646' }] },
        { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#304a7d' }] },
        { featureType: 'water', elementType: 'geometry.fill', stylers: [{ color: '#17263c' }] },
    ];

    const map = new google.maps.Map(document.getElementById('settingsMap'), {
        zoom:             isDefault ? 12 : 16,
        center:           { lat: startLat, lng: startLng },
        styles:           isDark ? NIGHT_STYLES : [],
        mapTypeControl:   false,
        streetViewControl: false,
        fullscreenControl: false,
    });

    const infoWindow = new google.maps.InfoWindow();
    const marker = new google.maps.Marker({
        position:  { lat: startLat, lng: startLng },
        map:       map,
        draggable: true,
        title:     'Shop Location',
        icon: {
            path:        google.maps.SymbolPath.CIRCLE,
            scale:       10,
            fillColor:   '#743089',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2.5,
        },
    });

    // Theme observer
    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.attributeName === 'data-admin-theme') {
                const dark = document.documentElement.getAttribute('data-admin-theme') === 'dark';
                map.setOptions({ styles: dark ? NIGHT_STYLES : [] });
            }
        });
    }).observe(document.documentElement, { attributes: true });

    function showInfo(title, body) {
        infoWindow.setContent('<div style="font-family:\'Inter\',sans-serif;font-size:0.83rem;"><b>' + title + '</b><br>' + body + '</div>');
        infoWindow.open(map, marker);
    }

    // Show initial popup
    showInfo(isDefault ? 'Default Location' : 'Shop Location',
             isDefault ? 'Drag or click map to set your shop.' : 'Currently configured origin.');

    // Reverse geocode using Google API
    let geocodeTimer = null;
    function reverseGeocode(lat, lng) {
        clearTimeout(geocodeTimer);
        geocodeTimer = setTimeout(function () {
            geocoder.geocode({ location: { lat: lat, lng: lng } }, function (results, status) {
                if (status === 'OK' && results[0]) {
                    addressInput.value = results[0].formatted_address;
                    showInfo('Location Set', results[0].formatted_address.substring(0, 50) + '…');
                } else {
                    showInfo('Location Set', lat.toFixed(5) + ', ' + lng.toFixed(5));
                }
            });
        }, 700);
    }

    function updatePosition(lat, lng, doGeocode) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        if (doGeocode) reverseGeocode(lat, lng);
    }

    marker.addListener('dragend', function () {
        const pos = marker.getPosition();
        updatePosition(pos.lat(), pos.lng(), true);
    });

    map.addListener('click', function (e) {
        const lat = e.latLng.lat();
        const lng = e.latLng.lng();
        marker.setPosition({ lat: lat, lng: lng });
        updatePosition(lat, lng, true);
    });

    // Forward geocode address on page load if coordinates are missing
    if (isDefault && addressInput.value.trim().length > 0) {
        geocoder.geocode({ address: addressInput.value.trim() }, function (results, status) {
            if (status === 'OK') {
                const loc = results[0].geometry.location;
                map.setCenter(loc);
                map.setZoom(16);
                marker.setPosition(loc);
                latInput.value = loc.lat().toFixed(6);
                lngInput.value = loc.lng().toFixed(6);
                showInfo('Address Found', results[0].formatted_address.substring(0, 50) + '…');
            }
        });
    }
});
</script>
@endpush

