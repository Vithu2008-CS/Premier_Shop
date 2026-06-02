{{--
    admin/drivers/edit.blade.php — Edit driver account form
    =========================================================
    Same fields as create; password field optional (blank = no change).
    PUT → admin.drivers.update → DriverController::update()
    Variable: $driver
--}}
@extends('layouts.admin_noble')
@section('title', 'Edit Driver - ' . $driver->name)

{{-- Leaflet CSS must go into plugin-styles stack (rendered in <head> before @stack('styles')) --}}
@push('plugin-styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">
@endpush

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }

.rounded-3 { border-radius: 12px !important; }
.rounded-4 { border-radius: 18px !important; }
.gap-4     { gap: 24px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Soft colour tokens */
.bg-soft-primary   { background: rgba(108,92,231,0.1) !important;  color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important;   color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important;  color: #64748b !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important;   color: #f59e0b !important; }

/* Form inputs */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0,0,0,0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s ease !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108,92,231,0.15) !important;
}
html[data-admin-theme="dark"] .form-control {
    background-color: #080f1d !important;
    border-color: rgba(255,255,255,0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167,139,250,0.2) !important;
}

/* Border */
.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* Pulsing green dot */
.pulse-green {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: blinkDot 1.5s infinite ease-in-out;
}
@keyframes blinkDot {
    0%,100% { opacity: 0.3; transform: scale(0.9); }
    50%      { opacity: 1;   transform: scale(1.15); }
}
.live-indicator {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: blinkDot 1.5s infinite ease-in-out; margin-right: 6px;
}
.tracking-badge { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; }

/* ── Map container ────────────────────────────────────────────────────────── */
#live-map-container {
    width: 100%;
    height: 300px;
    border-radius: 14px;
    overflow: hidden;
    border: 1.5px solid rgba(0,0,0,0.06);
    background: #e2e8f0;
    position: relative;
}
#driver-map { width: 100%; height: 100%; }
html[data-admin-theme="dark"] #live-map-container { border-color: rgba(255,255,255,0.06); background: #0f172a; }

/* Darken Leaflet tiles in dark mode */
html[data-admin-theme="dark"] .leaflet-tile { filter: brightness(0.88) contrast(1.05); }
html[data-admin-theme="light"] .leaflet-tile { filter: none; }

/* ── Floating save bar ────────────────────────────────────────────────────── */
.floating-save-bar {
    position: fixed; bottom: 24px; left: calc(50% + 120px);
    transform: translateX(-50%); z-index: 1000;
    width: calc(100% - 32px - 240px); max-width: 920px;
    background: rgba(255,255,255,0.85) !important;
    backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06) !important;
    border-radius: 50px !important;
    transition: all 0.3s ease;
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15,23,42,0.85) !important;
    border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}
.floating-save-bar .button-group { display: flex; align-items: center; gap: 12px; }
.floating-save-bar .btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 38px !important; min-width: 100px !important;
    padding: 0 22px !important; font-size: 0.82rem !important; font-weight: 700 !important;
    border-radius: 30px !important; transition: all 0.2s ease !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0,0,0,0.15) !important; background: transparent !important; color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover { background: rgba(0,0,0,0.04) !important; color: #1e293b !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light { border-color: rgba(255,255,255,0.3) !important; color: #fff !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover { background: rgba(255,255,255,0.1) !important; }
.floating-save-bar .btn-primary {
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important; color: #fff !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
}
.floating-save-bar .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }
.floating-save-bar .btn-danger {
    background: transparent !important; border: 1.8px solid #ff3366 !important; color: #ff3366 !important;
}
.floating-save-bar .btn-danger:hover { background: rgba(255,51,102,0.05) !important; box-shadow: 0 4px 12px rgba(255,51,102,0.15) !important; transform: translateY(-1px) !important; }
.floating-save-bar .floating-bar-title { color: #0f172a !important; }
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title { color: #ffffff !important; }

@media (max-width: 991px) { .floating-save-bar { left: 50% !important; width: calc(100% - 32px) !important; } }
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 20px !important; padding: 12px 16px !important;
        bottom: 16px !important; flex-direction: column; gap: 10px;
        align-items: stretch !important; text-align: center;
        width: calc(100% - 24px) !important;
    }
    .floating-save-bar .button-group { width: 100%; gap: 8px; }
    .floating-save-bar .btn { min-width: 0 !important; padding: 0 8px !important; font-size: 0.76rem !important; flex: 1; }
    #live-map-container { height: 220px; }
    .card-body.p-4.p-md-5 { padding: 1.25rem !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update: {{ $driver->name }}</li>
        </ol>
        <a href="{{ route('admin.drivers.index') }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;">
            <i class="bi bi-arrow-left mr-2" style="font-size:0.85rem;margin-right:6px;"></i> Back to fleet
        </a>
    </nav>

    <div class="row">

        {{-- ── Left: Edit Form ───────────────────────────────────────────────── --}}
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.drivers.update', $driver) }}" method="POST" id="driver-update-form" class="d-flex flex-column gap-4 w-100">
                @csrf
                @method('PUT')

                {{-- Core Profile --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-person-badge text-primary mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Update Driver Information
                            </h5>
                        </div>

                        {{-- Avatar banner --}}
                        <div class="d-flex align-items-center p-4 rounded-4 mb-4" style="background:rgba(108,92,231,0.03);border:1.5px solid rgba(108,92,231,0.06);">
                            <div class="rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-4 shadow-sm"
                                 style="font-size:1.5rem;min-width:64px;height:64px;margin-right:16px;">
                                {{ strtoupper(substr($driver->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="mb-1 text-theme-dark-bold" style="font-size:1.15rem;font-family:'Outfit',sans-serif;font-weight:700;">{{ $driver->name }}</h4>
                                <p class="text-muted small mb-0">{{ $driver->email }}</p>
                                <div class="d-flex gap-2 mt-2" style="gap:8px;">
                                    <span class="badge bg-soft-primary font-weight-bold" style="border-radius:10px;">Assigned Driver</span>
                                    @if($driver->is_on_duty)
                                        <span class="badge bg-soft-success font-weight-bold" style="border-radius:10px;">On Duty</span>
                                    @else
                                        <span class="badge bg-soft-secondary font-weight-bold" style="border-radius:10px;">Off Duty</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $driver->name) }}" required placeholder="e.g. Alex Johnson">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $driver->email) }}" required placeholder="driver@shop.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $driver->phone) }}" required placeholder="+44 7911 123456">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                       value="{{ old('dob', $driver->dob ? $driver->dob->format('Y-m-d') : '') }}" required>
                                @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label small text-muted font-weight-bold">Home Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                      placeholder="Enter driver's home address…">{{ old('address', $driver->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Security --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-shield-lock text-warning mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Security Credentials
                            </h5>
                        </div>
                        <div class="rounded-3 mb-4 d-flex align-items-start"
                             style="background:rgba(245,158,11,0.04);border:1.5px solid rgba(245,158,11,0.08);padding:16px;">
                            <i class="bi bi-info-circle-fill text-warning" style="font-size:1.15rem;margin-right:12px;line-height:1;flex-shrink:0;"></i>
                            <p class="small mb-0 text-muted" style="line-height:1.45;">
                                Leave the password fields blank if you do not wish to update the driver's current login password.
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">New Password (Optional)</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

        {{-- ── Right: Live Tracking Card ─────────────────────────────────────── --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                <div class="card-body p-4 p-md-5 d-flex flex-column">

                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                            <i class="bi bi-geo-alt-fill text-danger mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                            Live Telemetry Tracking
                        </h5>
                        <span class="badge bg-soft-success d-inline-flex align-items-center font-weight-bold tracking-badge px-2 py-1" style="border-radius:12px;">
                            <span class="pulse-green" style="margin-right:6px;"></span>
                            LIVE CONNECTED
                        </span>
                    </div>

                    <p class="text-muted small mb-4" style="line-height:1.45;">
                        Real-time vehicle coordinates fetched directly from the driver's mobile device via the shop dispatch servers.
                    </p>

                    {{-- Coordinate display --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 border h-100" style="background:rgba(0,0,0,0.005);border-color:rgba(0,0,0,0.04) !important;">
                                <span class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size:0.62rem;letter-spacing:0.3px;">Driver Latitude</span>
                                <h5 class="text-theme-dark-bold font-weight-bold mb-0" id="live-lat" style="font-size:0.88rem;font-family:monospace;">
                                    {{ $driver->latitude ? number_format($driver->latitude, 6) : '—' }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 border h-100" style="background:rgba(0,0,0,0.005);border-color:rgba(0,0,0,0.04) !important;">
                                <span class="text-muted d-block mb-1 text-uppercase font-weight-bold" style="font-size:0.62rem;letter-spacing:0.3px;">Driver Longitude</span>
                                <h5 class="text-theme-dark-bold font-weight-bold mb-0" id="live-lng" style="font-size:0.88rem;font-family:monospace;">
                                    {{ $driver->longitude ? number_format($driver->longitude, 6) : '—' }}
                                </h5>
                            </div>
                        </div>
                    </div>

                    {{-- Track button --}}
                    <div class="mb-3">
                        <button type="button" id="btn-track-driver"
                                class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
                                style="border-radius:30px !important;font-weight:700;font-family:'Outfit';background:linear-gradient(135deg,#6c5ce7,#a78bfa) !important;border:none;padding:10px 20px;box-shadow:0 4px 12px rgba(108,92,231,0.25);gap:8px;">
                            <i class="bi bi-radar" id="track-icon" style="font-size:1rem;"></i>
                            <span id="track-btn-text">Track Driver (Get Exact GPS)</span>
                        </button>
                        <div id="sync-success-badge" class="text-center mt-2 small text-success font-weight-bold" style="display:none;">
                            <i class="bi bi-check-circle-fill" style="margin-right:4px;"></i> GPS Location Synchronised
                        </div>
                    </div>

                    {{-- Map container — fluid width, fixed height so Leaflet tiles load correctly --}}
                    <div id="live-map-container" class="mb-3">
                        <div id="driver-map"></div>
                    </div>

                    {{-- Stats strip --}}
                    <div class="row g-3 border-top pt-3" style="opacity:0.95;">
                        <div class="col-4 text-center">
                            <span class="text-muted d-block mb-1" style="font-size:0.65rem;">Active Speed</span>
                            <span class="text-success font-weight-bold" id="live-speed" style="font-size:0.88rem;">
                                {{ $driver->is_on_duty ? '0 mph' : 'Off Duty' }}
                            </span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted d-block mb-1" style="font-size:0.65rem;">Fleet Heading</span>
                            <span class="text-primary font-weight-bold" id="live-heading" style="font-size:0.88rem;">Stationary</span>
                        </div>
                        <div class="col-4 text-center">
                            <span class="text-muted d-block mb-1" style="font-size:0.65rem;">Active Work</span>
                            @php
                                $activeOrders = $driver->assignedOrders()
                                    ->whereIn('status', ['pending','processing','shipped'])
                                    ->count();
                            @endphp
                            <span class="badge bg-soft-primary px-2" style="font-size:0.72rem;border-radius:12px;">{{ $activeOrders }} active</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center" style="font-family:'Outfit',sans-serif;gap:8px;">
        <span class="live-indicator"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.68rem;letter-spacing:0.5px;font-weight:600;white-space:nowrap;">Update Driver:</span>
        <span class="font-weight-bold text-nowrap floating-bar-title" style="font-size:0.85rem;">{{ $driver->name }}</span>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger"
                onclick="if(confirm('Permanently delete this driver account?'))document.getElementById('delete-driver-form').submit();">
            <i class="bi bi-trash" style="margin-right:6px;"></i> Delete
        </button>
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-light">Cancel</a>
        <button type="submit" form="driver-update-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Save
        </button>
    </div>
</div>

{{-- Hidden delete form --}}
<form id="delete-driver-form" action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
{{-- Correct Leaflet 1.9.4 JS integrity --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLGk="
        crossorigin=""></script>
<script>
$(function () {
    'use strict';

    // ── Backend data ──────────────────────────────────────────────────────────
    var MAPS_API_KEY  = "{{ config('services.google.maps_key') }}";
    var USE_GOOGLE    = MAPS_API_KEY && MAPS_API_KEY !== '' && MAPS_API_KEY !== 'your_key_here';
    var DRIVER_ID     = {{ $driver->id }};
    var IS_ON_DUTY    = {{ $driver->is_on_duty ? 'true' : 'false' }};
    var INIT_LAT      = {{ $driver->latitude  ?? 51.505 }};
    var INIT_LNG      = {{ $driver->longitude ?? -0.09  }};

    // ── State ─────────────────────────────────────────────────────────────────
    var mapEngine      = 'leaflet';   // 'leaflet' | 'google'
    var map            = null;
    var marker         = null;
    var tileLayer      = null;        // Leaflet tile layer — kept for hot-swapping
    var mapReady       = false;
    var curLat         = INIT_LAT;
    var curLng         = INIT_LNG;
    var pollTimer      = null;
    var animTimer      = null;

    // ── Theme helpers ─────────────────────────────────────────────────────────
    function isDark() {
        return document.documentElement.getAttribute('data-admin-theme') === 'dark';
    }

    var TILE_DARK  = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
    var TILE_LIGHT = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
    var TILE_ATTR  = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>';

    // Full Google Maps Night style array
    var GOOGLE_NIGHT = [
        { elementType:"geometry",                                                stylers:[{color:"#0c1427"}] },
        { elementType:"labels.text.fill",                                        stylers:[{color:"#8ec3b9"}] },
        { elementType:"labels.text.stroke",                                      stylers:[{color:"#1a3646"}] },
        { featureType:"administrative.country", elementType:"geometry.stroke",   stylers:[{color:"#4b6878"}] },
        { featureType:"landscape.natural",      elementType:"geometry",          stylers:[{color:"#023e58"}] },
        { featureType:"poi",                    elementType:"geometry",          stylers:[{color:"#283d6a"}] },
        { featureType:"poi.park",               elementType:"geometry.fill",     stylers:[{color:"#023e58"}] },
        { featureType:"road",                   elementType:"geometry",          stylers:[{color:"#304a7d"}] },
        { featureType:"road",                   elementType:"geometry.stroke",   stylers:[{color:"#255763"}] },
        { featureType:"road",                   elementType:"labels.text.fill",  stylers:[{color:"#98a5be"}] },
        { featureType:"road.highway",           elementType:"geometry",          stylers:[{color:"#2c6675"}] },
        { featureType:"road.highway",           elementType:"labels.text.fill",  stylers:[{color:"#b0d5ce"}] },
        { featureType:"transit.line",           elementType:"geometry.fill",     stylers:[{color:"#283d6a"}] },
        { featureType:"transit.station",        elementType:"geometry",          stylers:[{color:"#3a4762"}] },
        { featureType:"water",                  elementType:"geometry.fill",     stylers:[{color:"#17263c"}] },
        { featureType:"water",                  elementType:"labels.text.fill",  stylers:[{color:"#515c6d"}] },
    ];

    // ── Apply theme to existing map ───────────────────────────────────────────
    function applyTheme() {
        var dark = isDark();
        if (mapEngine === 'leaflet' && map) {
            if (tileLayer) map.removeLayer(tileLayer);
            tileLayer = L.tileLayer(dark ? TILE_DARK : TILE_LIGHT, { maxZoom: 19, attribution: TILE_ATTR }).addTo(map);
        } else if (mapEngine === 'google' && map) {
            map.setOptions({ styles: dark ? GOOGLE_NIGHT : [] });
        }
    }

    // Watch data-admin-theme attribute for changes
    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.attributeName === 'data-admin-theme') applyTheme();
        });
    }).observe(document.documentElement, { attributes: true });

    // ── Map size: force Leaflet to recalc tile grid after layout settles ──────
    function fixSize() {
        if (mapEngine === 'leaflet' && map) map.invalidateSize(true);
    }
    [100, 300, 600, 1200, 2000].forEach(function (d) { setTimeout(fixSize, d); });
    $(window).on('resize', fixSize);

    // ── Driver marker icon (Leaflet) ──────────────────────────────────────────
    function driverIcon() {
        return L.divIcon({
            html: '<div style="width:36px;height:36px;background:#6c5ce7;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 5px rgba(108,92,231,0.2),0 4px 14px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;font-size:16px;">🚚</div>',
            className: '',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -20],
        });
    }

    // ── Leaflet init ──────────────────────────────────────────────────────────
    function initLeaflet() {
        try {
            map = L.map('driver-map', { zoomControl: true, attributionControl: true })
                   .setView([curLat, curLng], 15);

            tileLayer = L.tileLayer(isDark() ? TILE_DARK : TILE_LIGHT, { maxZoom: 19, attribution: TILE_ATTR }).addTo(map);

            marker = L.marker([curLat, curLng], { icon: driverIcon() })
                      .bindPopup('<b>{{ addslashes($driver->name) }}</b>')
                      .addTo(map);

            mapEngine = 'leaflet';
            mapReady  = true;

            // Multiple invalidateSize passes — critical for correct tile loading
            [50, 150, 350, 700, 1500].forEach(function (d) {
                setTimeout(function () { map && map.invalidateSize(true); }, d);
            });

            return true;
        } catch (e) {
            console.error('Leaflet init failed:', e);
            document.getElementById('driver-map').innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:0.85rem;gap:6px;"><i class="bi bi-exclamation-triangle"></i> Map unavailable</div>';
            return false;
        }
    }

    // ── Google Maps init ──────────────────────────────────────────────────────
    function initGoogle() {
        try {
            if (typeof google === 'undefined' || !google.maps) return false;

            map = new google.maps.Map(document.getElementById('driver-map'), {
                zoom: 15,
                center: { lat: curLat, lng: curLng },
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: isDark() ? GOOGLE_NIGHT : [],
                zoomControl: true,
                streetViewControl: false,
                mapTypeControl: false,
                fullscreenControl: false,
            });

            marker = new google.maps.Marker({
                position: { lat: curLat, lng: curLng },
                map: map,
                title: '{{ addslashes($driver->name) }}',
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 7,
                    fillColor: '#6c5ce7',
                    fillOpacity: 0.95,
                    strokeColor: '#ffffff',
                    strokeWeight: 2.5,
                },
            });

            mapEngine = 'google';
            mapReady  = true;
            return true;
        } catch (e) {
            console.warn('Google Maps init failed:', e);
            return false;
        }
    }

    // ── Load map engine ───────────────────────────────────────────────────────
    if (USE_GOOGLE) {
        window._gmCb = function () { if (!initGoogle()) initLeaflet(); };
        var s   = document.createElement('script');
        s.src   = 'https://maps.googleapis.com/maps/api/js?key=' + MAPS_API_KEY + '&callback=_gmCb';
        s.async = true;
        s.defer = true;
        s.onerror = function () { initLeaflet(); };
        document.head.appendChild(s);
    } else {
        initLeaflet();
    }

    // ── Telemetry UI helpers ──────────────────────────────────────────────────
    function setCoords(lat, lng) {
        document.getElementById('live-lat').textContent = parseFloat(lat).toFixed(6);
        document.getElementById('live-lng').textContent = parseFloat(lng).toFixed(6);
    }
    function setStats(heading, speed) {
        document.getElementById('live-heading').textContent = heading;
        document.getElementById('live-speed').textContent   = speed;
    }

    function cardinalHeading(lat1, lng1, lat2, lng2) {
        var dLng  = (lng2 - lng1) * Math.PI / 180;
        var y     = Math.sin(dLng) * Math.cos(lat2 * Math.PI / 180);
        var x     = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180)
                  - Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos(dLng);
        var brng  = ((Math.atan2(y, x) * 180 / Math.PI) + 360) % 360;
        return ["North","North-East","East","South-East","South","South-West","West","North-West"][Math.round(brng / 45) % 8];
    }

    function haversineMiles(lat1, lng1, lat2, lng2) {
        var R    = 3958.8;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a    = Math.sin(dLat/2) * Math.sin(dLat/2)
                 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
                 * Math.sin(dLng/2) * Math.sin(dLng/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    // ── Smooth marker animation ───────────────────────────────────────────────
    function animateMarker(sLat, sLng, eLat, eLng) {
        if (animTimer) clearInterval(animTimer);
        var steps = 60, step = 0;
        animTimer = setInterval(function () {
            if (!mapReady || !map || !marker) { clearInterval(animTimer); return; }
            step++;
            var f   = step / steps;
            var lat = sLat + (eLat - sLat) * f;
            var lng = sLng + (eLng - sLng) * f;
            if (mapEngine === 'google') {
                marker.setPosition({ lat: lat, lng: lng });
                if (step % 20 === 0) map.panTo({ lat: lat, lng: lng });
            } else {
                marker.setLatLng([lat, lng]);
                if (step % 20 === 0) map.panTo([lat, lng]);
            }
            if (step >= steps) clearInterval(animTimer);
        }, 25);
    }

    // ── Apply location data from API ──────────────────────────────────────────
    function applyData(data) {
        var onDuty = data.is_on_duty;
        var lat    = data.latitude  != null ? parseFloat(data.latitude)  : null;
        var lng    = data.longitude != null ? parseFloat(data.longitude) : null;

        if (!onDuty) { setStats('Stationary', 'Off Duty'); return; }
        if (lat == null || lng == null) { setStats('Stationary', '0 mph'); return; }

        var dist = haversineMiles(curLat, curLng, lat, lng);

        if (dist > 0.00005) {
            var heading = cardinalHeading(curLat, curLng, lat, lng);
            // Speed: dist / (15s / 3600s/hr)  — capped to 80 mph
            var mph = Math.min(Math.round(dist * 240), 80);
            setStats(heading, mph + ' mph');
            setCoords(lat, lng);
            animateMarker(curLat, curLng, lat, lng);
            curLat = lat; curLng = lng;
        } else {
            setStats('Stationary', '0 mph');
            if (mapReady && marker) {
                mapEngine === 'google'
                    ? marker.setPosition({ lat: curLat, lng: curLng })
                    : marker.setLatLng([curLat, curLng]);
            }
        }
    }

    // ── Fetch from server ─────────────────────────────────────────────────────
    var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute?.('content') ?? '';

    function fetchLocation(onSuccess) {
        return fetch('/admin/drivers/' + DRIVER_ID + '/location', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function (data) { applyData(data); if (onSuccess) onSuccess(data); })
        .catch(function (err) { console.warn('Location poll failed:', err); });
    }

    // ── Auto-poll (15 s while on duty) ────────────────────────────────────────
    function startPoll() {
        if (pollTimer || !IS_ON_DUTY) return;
        pollTimer = setInterval(function () { fetchLocation(null); }, 15000);
    }
    function stopPoll() { clearInterval(pollTimer); pollTimer = null; }

    // Start auto-poll immediately if on duty
    if (IS_ON_DUTY) startPoll();

    // ── Track button ──────────────────────────────────────────────────────────
    $('<style>@keyframes radar-spin{to{transform:rotate(360deg)}}.radar-spin{animation:radar-spin 1.2s linear infinite;display:inline-block}</style>').appendTo('head');

    $('#btn-track-driver').on('click', function () {
        var $btn   = $(this);
        var $icon  = $('#track-icon');
        var $text  = $('#track-btn-text');
        var $badge = $('#sync-success-badge');

        $btn.prop('disabled', true);
        $icon.addClass('radar-spin');
        $text.text('Connecting to satellite…');
        $badge.hide();

        fetchLocation(function (data) {
            $badge.fadeIn().delay(3000).fadeOut();
            if (data.is_on_duty) startPoll();
        }).finally(function () {
            $btn.prop('disabled', false);
            $icon.removeClass('radar-spin');
            $text.text('Track Driver (Get Exact GPS)');
        });
    });

    // Cleanup on page leave
    window.addEventListener('pagehide', stopPoll);
});
</script>
@endpush
