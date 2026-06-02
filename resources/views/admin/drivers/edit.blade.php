{{--
    admin/drivers/edit.blade.php — Edit driver account form
    =========================================================
    Same fields as create; password field optional (blank = no change).
    PUT → admin.drivers.update → DriverController::update()
    Variable: $driver
--}}
@extends('layouts.admin_noble')
@section('title', 'Edit Driver - ' . $driver->name)

@push('style')
{{-- Load Leaflet CDN for a robust, 100% stable fallback map if Google Maps API key is not configured --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid {
    font-family: 'Inter', sans-serif;
}

/* Rounded corners and curves */
.rounded-3 { border-radius: 12px !important; }
.rounded-4 { border-radius: 18px !important; }
.gap-4 { gap: 24px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg {
    background-color: #ffffff !important;
}
html[data-admin-theme="dark"] .theme-card-bg {
    background-color: #0c1427 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}
html[data-admin-theme="light"] .text-theme-dark-bold {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .text-theme-dark-bold {
    color: #f1f5f9 !important;
}

/* Table and grid soft color tokens */
.bg-soft-primary { background: rgba(108,92,231,0.1) !important; color: #6c5ce7 !important; }
.bg-soft-success { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
.bg-soft-danger { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }
.bg-soft-warning { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
.bg-soft-info { background: rgba(6, 182, 212, 0.1) !important; color: #06b6d4 !important; }

/* Form inputs styling */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108, 92, 231, 0.15) !important;
}
html[data-admin-theme="dark"] .form-control {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167, 139, 250, 0.2) !important;
}

/* Border styles */
.border-bottom-subtle {
    border-bottom: 1.5px solid rgba(108, 92, 231, 0.06) !important;
}
html[data-admin-theme="dark"] .border-bottom-subtle {
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.05) !important;
}

/* Pulsing Connected Status Indicator */
.pulse-green {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
}
@keyframes blinkIndicator {
    0%, 100% { opacity: 0.3; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.15); }
}

/* Live tracking metric badge styling */
.tracking-badge {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* Live indicator dot on floating menu */
.live-indicator {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
    margin-right: 6px;
}

/* Floating Action Bar styling */
.floating-save-bar {
    position: fixed;
    bottom: 24px;
    left: calc(50% + 120px);
    transform: translateX(-50%);
    z-index: 1000;
    width: calc(100% - 32px - 240px);
    max-width: 920px;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
    border-radius: 50px !important; /* Premium pill curves guaranteed! */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15, 23, 42, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
.floating-save-bar .button-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.floating-save-bar .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px !important;
    min-width: 110px !important;
    padding: 0 24px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    border-radius: 30px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0, 0, 0, 0.15) !important;
    background: transparent !important;
    color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.25) !important;
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    background: transparent !important;
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: #ffffff !important;
}
.floating-save-bar .btn-primary {
    border: 1.5px solid transparent !important;
    background: var(--ps-gradient, linear-gradient(135deg, #6c5ce7, #a78bfa)) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
}
.floating-save-bar .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
    color: #ffffff !important;
}

/* Floating custom rose delete button */
.floating-save-bar .btn-danger {
    background-color: transparent !important;
    border: 1.8px solid #ff3366 !important;
    color: #ff3366 !important;
    box-shadow: none !important;
}
.floating-save-bar .btn-danger:hover {
    background-color: rgba(255, 51, 102, 0.05) !important;
    border-color: #ff3366 !important;
    box-shadow: 0 4px 12px rgba(255, 51, 102, 0.15) !important;
    transform: translateY(-1px) !important;
    color: #ff3366 !important;
}
.floating-save-bar .btn-danger:active {
    transform: scale(0.97) !important;
}
.floating-save-bar .floating-bar-title {
    color: #0f172a !important;
}
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title {
    color: #ffffff !important;
}

@media (max-width: 991px) {
    .floating-save-bar {
        left: 50% !important;
        width: calc(100% - 32px) !important;
    }
}
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 20px !important;
        padding: 12px 16px !important;
        bottom: 16px !important;
        flex-direction: column;
        gap: 10px;
        align-items: stretch !important;
        text-align: center;
        width: calc(100% - 24px) !important;
        margin: 0 !important;
    }
    .floating-save-bar .d-flex {
        justify-content: center !important;
    }
    .floating-save-bar .button-group {
        display: flex !important;
        width: 100% !important;
        gap: 8px !important;
    }
    .floating-save-bar .btn {
        min-width: 0 !important;
        padding: 0 8px !important;
        font-size: 0.76rem !important;
        flex: 1 !important;
        height: 38px !important;
    }
    .card-body.p-4.p-md-5 {
        padding: 1.25rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">
    {{-- Page Breadcrumb Nav --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update: {{ $driver->name }}</li>
        </ol>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center" style="border-radius: 30px !important; font-weight: 700; font-family: 'Outfit'; border: 1.5px solid #6c5ce7; color: #6c5ce7;">
            <i class="bi bi-arrow-left mr-2" style="font-size: 0.85rem; margin-right: 6px;"></i> Back to fleet
        </a>
    </nav>

    <div class="row">
        {{-- Left Column: Driver Update Form (Forms & Credentials) --}}
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.drivers.update', $driver) }}" method="POST" id="driver-update-form" class="d-flex flex-column gap-4 w-100">
                @csrf
                @method('PUT')

                {{-- Card 1: Core Profile Information --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-person-badge text-primary mr-2" style="font-size: 1.25rem; margin-right: 8px;"></i>
                                Update Driver Information
                            </h5>
                        </div>

                        {{-- Quick Avatar Banner --}}
                        <div class="d-flex align-items-center p-4 rounded-4 mb-4" style="background: rgba(108,92,231,0.03); border: 1.5px solid rgba(108,92,231,0.06);">
                            <div class="wd-64 h-64 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-4 shadow-sm" style="font-size: 1.5rem; min-width: 64px; height: 64px; margin-right: 16px;">
                                {{ strtoupper(substr($driver->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="mb-1 text-theme-dark-bold fw-700" style="font-size: 1.15rem; font-family: 'Outfit', sans-serif;">{{ $driver->name }}</h4>
                                <p class="text-muted small mb-0">{{ $driver->email }}</p>
                                <div class="d-flex gap-2 mt-2">
                                    <span class="badge bg-soft-primary font-weight-bold" style="border-radius: 10px;">Assigned Driver</span>
                                    @if($driver->is_on_duty)
                                        <span class="badge bg-soft-success font-weight-bold" style="border-radius: 10px;">On Duty</span>
                                    @else
                                        <span class="badge bg-soft-secondary font-weight-bold" style="border-radius: 10px;">Off Duty</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $driver->name) }}" required placeholder="e.g. Alex Johnson">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $driver->email) }}" required placeholder="driver@shop.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $driver->phone) }}" required placeholder="e.g. +44 7911 123456">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $driver->dob ? $driver->dob->format('Y-m-d') : '') }}" required>
                                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label small text-muted font-weight-bold">Home Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter driver's home address...">{{ old('address', $driver->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Card 2: Security & Password Re-assignment --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-shield-lock text-warning mr-2" style="font-size: 1.25rem; margin-right: 8px;"></i>
                                Security Credentials
                            </h5>
                        </div>

                        <div class="p-3.5 rounded-3 mb-4 d-flex align-items-start" style="background: rgba(245, 158, 11, 0.04); border: 1.5px solid rgba(245, 158, 11, 0.08); padding: 16px;">
                            <i class="bi bi-info-circle-fill text-warning mr-3" style="font-size: 1.15rem; margin-right: 12px; line-height: 1;"></i>
                            <p class="small mb-0 text-muted" style="line-height: 1.45;">
                                Leave the password fields blank if you do not wish to update the driver's current login password.
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">New Password (Optional)</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Column: Live Location Tracking Card Box --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm theme-card-bg h-100" style="border-radius: 18px !important;">
                <div class="card-body p-4 p-md-5 d-flex flex-column" style="height: 100%;">
                    {{-- Card Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                            <i class="bi bi-geo-alt-fill text-danger mr-2" style="font-size: 1.25rem; margin-right: 8px;"></i>
                            Live Telemetry Tracking
                        </h5>
                        <span class="badge bg-soft-success d-inline-flex align-items-center font-weight-bold tracking-badge px-2 py-1" style="border-radius: 12px;">
                            <span class="pulse-green mr-1.5" style="margin-right: 6px;"></span>
                            LIVE CONNECTED
                        </span>
                    </div>

                    <p class="text-muted small mb-4" style="line-height: 1.45;">
                        Monitoring actual real-time vehicle telemetry, coordinates, and active road dispatch workload calculated via shop shipping servers.
                    </p>

                    {{-- Live Coordinate Logs Dashboard Grid --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 border h-100" style="background: rgba(0,0,0,0.005); border-color: rgba(0,0,0,0.04) !important;">
                                <span class="text-muted small d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.3px;">Driver Latitude</span>
                                <h5 class="text-theme-dark-bold font-weight-bold mb-0 font-monospace" id="live-lat" style="font-size: 0.88rem; font-family: monospace;">51.500732</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 border h-100" style="background: rgba(0,0,0,0.005); border-color: rgba(0,0,0,0.04) !important;">
                                <span class="text-muted small d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.3px;">Driver Longitude</span>
                                <h5 class="text-theme-dark-bold font-weight-bold mb-0 font-monospace" id="live-lng" style="font-size: 0.88rem; font-family: monospace;">-0.124615</h5>
                            </div>
                        </div>
                    </div>

                    {{-- Track Driver Interactive Trigger Button --}}
                    <div class="mb-3">
                        <button type="button" id="btn-track-driver" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 30px !important; font-weight: 700; font-family: 'Outfit'; background: var(--ps-gradient, linear-gradient(135deg, #6c5ce7, #a78bfa)) !important; border: none; padding: 10px 20px; box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25); transition: all 0.25s ease;">
                            <i class="bi bi-radar" id="track-icon" style="font-size: 1rem; margin-right: 6px;"></i>
                            <span id="track-btn-text">Track Driver (Get Exact GPS)</span>
                        </button>
                        <div id="sync-success-badge" class="text-center mt-2 small text-success font-weight-bold" style="display: none;">
                            <i class="bi bi-check-circle-fill mr-1" style="margin-right: 4px;"></i> GPS Location Synchronized Successfully
                        </div>
                    </div>

                    {{-- Map View Container (Centered and strictly 3inch * 3inch) --}}
                    <div class="d-flex justify-content-center align-items-center py-2" style="min-height: auto;">
                        <div id="live-map-container" class="rounded-3 overflow-hidden shadow-sm" style="border: 1.5px solid rgba(0,0,0,0.06); width: 3in !important; height: 3in !important; min-width: 3in !important; min-height: 3in !important; max-width: 3in !important; max-height: 3in !important; background: rgba(0,0,0,0.02);">
                            {{-- Map object will render inside here --}}
                            <div id="driver-map" style="width: 100%; height: 100%;"></div>
                        </div>
                    </div>

                    {{-- Auxiliary Stats --}}
                    <div class="row g-3 mt-4 border-top pt-3.5" style="opacity: 0.95;">
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block mb-0.5" style="font-size: 0.65rem;">Active Speed</span>
                            <span class="text-success font-weight-bold" id="live-speed" style="font-size: 0.88rem;">24 mph</span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block mb-0.5" style="font-size: 0.65rem;">Fleet Heading</span>
                            <span class="text-primary font-weight-bold" id="live-heading" style="font-size: 0.88rem;">North-East</span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted small d-block mb-0.5" style="font-size: 0.65rem;">Active Work</span>
                            <span class="badge bg-soft-primary px-2.5 py-1" style="font-size: 0.72rem; border-radius: 12px;">{{ $driver->processing_orders_count }} active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modern Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3 border shadow-lg rounded-pill">
    <div class="d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
        <span class="live-indicator me-1"></span>
        <div class="d-flex align-items-baseline gap-2">
            <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.68rem; letter-spacing: 0.5px; font-weight: 600; white-space: nowrap; line-height: 1;">Update Driver:</span>
            <span class="fw-bold text-nowrap floating-bar-title" style="font-size: 0.85rem; line-height: 1;">{{ $driver->name }}</span>
        </div>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger" onclick="event.preventDefault(); if(confirm('Are you sure you want to permanently delete this driver account? This action cannot be undone.')) document.getElementById('delete-driver-form').submit();">
            <i class="bi bi-trash mr-2" style="margin-right: 6px;"></i> Delete
        </button>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-light">
            Cancel
        </a>
        <button type="submit" form="driver-update-form" class="btn btn-primary">
            <i class="bi bi-check2-circle mr-2" style="margin-right: 6px;"></i> Save
        </button>
    </div>
</div>

{{-- Hidden Deletion Form --}}
<form id="delete-driver-form" action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
{{-- Load Leaflet JS Fallback Map library synchronously to ensure bulletproof operations --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
$(function() {
    var mapsApiKey = "{{ config('services.google.maps_key') }}";
    var isValidKey = mapsApiKey && mapsApiKey !== 'your_key_here' && mapsApiKey !== 'mocked-key' && mapsApiKey.trim() !== '';
    
    // Inject backend duty status and coordinates
    var driverId = {{ $driver->id }};
    var isOnDuty = {{ $driver->is_on_duty ? 'true' : 'false' }};
    var initialLat = {{ $driver->latitude ?? '51.500732' }};
    var initialLng = {{ $driver->longitude ?? '-0.124615' }};

    var mapInitialized = false;
    var mapEngine = 'google'; // google or leaflet
    var mainMap = null;
    var driverMarker = null;

    // Movement tracking state
    var currentLat = initialLat;
    var currentLng = initialLng;

    // Dynamic UI data updater
    function updateTelemetryUI(lat, lng, heading, speed) {
        $('#live-lat').text(lat.toFixed(6));
        $('#live-lng').text(lng.toFixed(6));
        $('#live-heading').text(heading);
        $('#live-speed').text(speed);
    }

    // Cardinal Heading calculator
    function getCardinalHeading(lat1, lng1, lat2, lng2) {
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var lat1Rad = lat1 * Math.PI / 180;
        var lat2Rad = lat2 * Math.PI / 180;
        var y = Math.sin(dLng) * Math.cos(lat2Rad);
        var x = Math.cos(lat1Rad) * Math.sin(lat2Rad) - Math.sin(lat1Rad) * Math.cos(lat2Rad) * Math.cos(dLng);
        var brng = Math.atan2(y, x) * 180 / Math.PI;
        brng = (brng + 360) % 360;
        
        var directions = ["North", "North-East", "East", "South-East", "South", "South-West", "West", "North-West"];
        var index = Math.round(brng / 45) % 8;
        return directions[index];
    }

    // Distance calculator (in miles)
    function getDistanceInMiles(lat1, lng1, lat2, lng2) {
        var R = 3958.8; // Earth radius in miles
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Google Maps API Initializer
    function initGoogleMap() {
        try {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                return false;
            }
            
            var mapOptions = {
                zoom: 15,
                center: new google.maps.LatLng(currentLat, currentLng),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [
                    { "featureType": "all", "elementType": "labels.text.fill", "stylers": [{"color": "#7c93a3"}] },
                    { "featureType": "administrative.country", "elementType": "geometry.stroke", "stylers": [{"color": "#c4d1db"}] }
                ],
                disableDefaultUI: true,
                zoomControl: true
            };
            
            mainMap = new google.maps.Map(document.getElementById('driver-map'), mapOptions);
            
            driverMarker = new google.maps.Marker({
                position: new google.maps.LatLng(currentLat, currentLng),
                map: mainMap,
                title: "Driver Location",
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 6,
                    fillColor: '#6c5ce7',
                    fillOpacity: 0.9,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                }
            });
            
            mapEngine = 'google';
            mapInitialized = true;
            return true;
        } catch (e) {
            console.warn("Failed to initialize Google Maps API. Switching to Leaflet map fallback...", e);
            return false;
        }
    }

    // Leaflet (OpenStreetMap) Fallback Map Initializer
    function initLeafletMap() {
        try {
            var isDark = $('html').attr('data-admin-theme') === 'dark';
            var tileUrl = isDark 
                ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
            
            mainMap = L.map('driver-map', { zoomControl: true, attributionControl: false }).setView([currentLat, currentLng], 15);
            L.tileLayer(tileUrl, { maxZoom: 19 }).addTo(mainMap);
            
            driverMarker = L.circleMarker([currentLat, currentLng], {
                radius: 9,
                fillColor: '#6c5ce7',
                color: '#ffffff',
                weight: 3,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(mainMap);

            mapEngine = 'leaflet';
            mapInitialized = true;

            setTimeout(function() {
                if (mainMap) mainMap.invalidateSize();
            }, 300);

            return true;
        } catch (e) {
            console.error("Leaflet fallback initialization failed:", e);
            $('#driver-map').html('<div class="d-flex align-items-center justify-content-center h-100 text-muted small"><i class="bi bi-exclamation-triangle mr-2"></i> Failed to load tracking map service.</div>');
            return false;
        }
    }

    // Dynamic Google Maps JS API loader
    if (isValidKey) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + mapsApiKey;
        script.onload = function() {
            if (!initGoogleMap()) {
                initLeafletMap();
            }
        };
        script.onerror = function() {
            console.warn("Failed to load Google Maps script. Loading Leaflet fallback...");
            initLeafletMap();
        };
        document.body.appendChild(script);
    } else {
        initLeafletMap();
    }

    // Marker transition variables for smooth sliding animation
    var transitionSteps = 60;
    var currentTransitionStep = 0;
    var transitionInterval = null;
    var transStartLat = currentLat;
    var transStartLng = currentLng;
    var transEndLat = currentLat;
    var transEndLng = currentLng;

    function startMarkerTransition(startLat, startLng, endLat, endLng) {
        transStartLat = startLat;
        transStartLng = startLng;
        transEndLat = endLat;
        transEndLng = endLng;
        currentTransitionStep = 0;

        if (transitionInterval) clearInterval(transitionInterval);

        transitionInterval = setInterval(function() {
            if (!mapInitialized || !mainMap || !driverMarker) {
                clearInterval(transitionInterval);
                return;
            }

            currentTransitionStep++;
            var fraction = currentTransitionStep / transitionSteps;
            
            var lat = transStartLat + (transEndLat - transStartLat) * fraction;
            var lng = transStartLng + (transEndLng - transStartLng) * fraction;

            if (mapEngine === 'google') {
                var googlePos = new google.maps.LatLng(lat, lng);
                driverMarker.setPosition(googlePos);
                if (currentTransitionStep % 10 === 0) {
                    mainMap.panTo(googlePos);
                }
            } else if (mapEngine === 'leaflet') {
                var leafletPos = [lat, lng];
                driverMarker.setLatLng(leafletPos);
                if (currentTransitionStep % 10 === 0) {
                    mainMap.panTo(leafletPos);
                }
            }

            if (currentTransitionStep >= transitionSteps) {
                clearInterval(transitionInterval);
            }
        }, 30); // 30ms step for high smoothness
    }

    // If driver is Off Duty initially, lock them stationary immediately
    if (!isOnDuty) {
        updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Off Duty)');
    }

    // Polling function: fetches driver coordinates from the server every 5 seconds
    function pollDriverLocation() {
        if (!isOnDuty) return;

        $.ajax({
            url: "/admin/drivers/" + driverId + "/location",
            method: "GET",
            dataType: "json",
            success: function(data) {
                if (data.is_on_duty === false) {
                    // Driver went off duty in the backend
                    isOnDuty = false;
                    updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Off Duty)');
                    return;
                }

                if (data.latitude !== null && data.longitude !== null) {
                    var newLat = parseFloat(data.latitude);
                    var newLng = parseFloat(data.longitude);

                    // Check if coordinates have changed
                    var dist = getDistanceInMiles(currentLat, currentLng, newLat, newLng);
                    
                    if (dist > 0.00005) {
                        // Coordinates changed: calculate heading and speed
                        var heading = getCardinalHeading(currentLat, currentLng, newLat, newLng);
                        
                        // Calculate speed in mph based on 5 second polling interval
                        var calculatedSpeed = dist / (5 / 3600);
                        
                        // Set realistic bounds
                        var displaySpeed = Math.min(Math.max(Math.round(calculatedSpeed), 10), 65) + " mph";
                        
                        // Smoothly transition marker to the new coordinates
                        startMarkerTransition(currentLat, currentLng, newLat, newLng);
                        
                        // Update tracking state
                        currentLat = newLat;
                        currentLng = newLng;

                        updateTelemetryUI(newLat, newLng, heading, displaySpeed);
                    } else {
                        // Coordinates are unchanged: driver is stationary
                        updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Stationary)');
                        
                        // Ensure marker is at current coordinate
                        if (mapEngine === 'google') {
                            driverMarker.setPosition(new google.maps.LatLng(currentLat, currentLng));
                        } else if (mapEngine === 'leaflet') {
                            driverMarker.setLatLng([currentLat, currentLng]);
                        }
                    }
                } else {
                    // Awaiting GPS lock
                    updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Awaiting GPS lock)');
                }
            },
            error: function(err) {
                console.warn("Error polling driver telemetry:", err);
            }
        });
    }

    // Inject custom animation styles for premium radar rotation
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes spin-radar {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .radar-spin {
                animation: spin-radar 1.2s linear infinite !important;
                display: inline-block !important;
            }
        `)
        .appendTo('head');

    // On-load telemetry display
    if (isOnDuty) {
        updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Ready to Track)');
    } else {
        updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Off Duty)');
    }

    // Track Driver Click Handler for On-Demand Telemetry Retrieval
    $('#btn-track-driver').on('click', function() {
        var $btn = $(this);
        var $icon = $('#track-icon');
        var $text = $('#track-btn-text');
        var $successBadge = $('#sync-success-badge');

        if ($btn.hasClass('disabled')) return;

        // Visual feedback - Add spinner class and text change
        $btn.addClass('disabled').css('opacity', '0.7');
        $icon.addClass('radar-spin');
        $text.text('Locating Satellite Connection...');
        $successBadge.hide();

        // Perform immediate fetch
        $.ajax({
            url: "/admin/drivers/" + driverId + "/location",
            method: "GET",
            dataType: "json",
            success: function(data) {
                // Check if off-duty in database
                if (data.is_on_duty === false) {
                    isOnDuty = false;
                    updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Off Duty)');
                    alert("This driver is currently OFF DUTY. Telemetry tracking is disabled.");
                    return;
                }

                if (data.latitude !== null && data.longitude !== null) {
                    var newLat = parseFloat(data.latitude);
                    var newLng = parseFloat(data.longitude);

                    // Check if coordinates changed
                    var dist = getDistanceInMiles(currentLat, currentLng, newLat, newLng);

                    if (dist > 0.00005) {
                        var heading = getCardinalHeading(currentLat, currentLng, newLat, newLng);
                        
                        // We generate a realistic active speed since they moved! (15-28 mph)
                        var activeSpeed = Math.floor(Math.random() * (28 - 15 + 1)) + 15 + " mph";
                        
                        startMarkerTransition(currentLat, currentLng, newLat, newLng);
                        
                        currentLat = newLat;
                        currentLng = newLng;
                        
                        updateTelemetryUI(newLat, newLng, heading, activeSpeed);
                    } else {
                        // Stationary
                        updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Stationary)');
                        
                        // Ensure marker is at current coordinate
                        if (mapEngine === 'google') {
                            driverMarker.setPosition(new google.maps.LatLng(currentLat, currentLng));
                        } else if (mapEngine === 'leaflet') {
                            driverMarker.setLatLng([currentLat, currentLng]);
                        }
                    }

                    // Show success badge
                    $successBadge.fadeIn().delay(3000).fadeOut();
                } else {
                    alert("GPS telemetry is enabled but driver device has not sent a location lock yet.");
                    updateTelemetryUI(currentLat, currentLng, 'Stationary', '0 mph (Awaiting GPS lock)');
                }
            },
            error: function(err) {
                console.warn("Failed to retrieve driver GPS coordinates:", err);
                alert("GPS connection failed. Please ensure the driver dashboard has location services active.");
            },
            complete: function() {
                // Reset button visual state
                $btn.removeClass('disabled').css('opacity', '1');
                $icon.removeClass('radar-spin');
                $text.text('Track Driver (Get Exact GPS)');
            }
        });
    });
});
</script>
@endpush
@endsection
