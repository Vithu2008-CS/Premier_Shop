{{--
    admin/drivers/index.blade.php — Driver monitoring dashboard
    ============================================================
    Table of all driver accounts: name, email, assigned active orders count, status.
    "Track" button per row opens a Google Maps live-location modal (polls every 10 s,
    stops when the modal is closed — never hammers the server when not in use).
    Variable: $drivers (with processing_orders_count)
--}}
@extends('layouts.admin_noble')
@section('title', 'Driver Monitoring')



@section('content')
<div class="container-fluid px-0" style="padding-bottom: 40px;">

    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="h3 mb-0 text-gray-800 fw-bold" style="font-family:'Outfit',sans-serif;">Driver Monitoring</h2>
            <p class="text-muted mb-0">Track live locations, review active workloads, and manage delivery drivers</p>
        </div>
        <div class="col-md-6 text-right d-none d-md-block">
            <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size:0.8rem;">
                Total: {{ $drivers->count() }} Drivers
            </span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
         style="border-radius:12px;background:rgba(16,185,129,0.12);color:#10b981;padding:0.75rem 1rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill mr-2"></i>
                <span class="font-weight-bold" style="font-size:0.82rem;">{{ session('success') }}</span>
            </div>
            <button type="button" class="close p-0" data-dismiss="alert" style="color:#10b981;opacity:0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
         style="border-radius:12px;background:rgba(239,68,68,0.12);color:#ef4444;padding:0.75rem 1rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                <span class="font-weight-bold" style="font-size:0.82rem;">{{ session('error') }}</span>
            </div>
            <button type="button" class="close p-0" data-dismiss="alert" style="color:#ef4444;opacity:0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Drivers Table Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <h5 class="card-title mb-0 font-weight-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                <i class="bi bi-truck text-primary mr-2" style="font-size:1.15rem;"></i>
                Active Fleet Directory
            </h5>
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary btn-curved d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-person-plus-fill mr-2" style="font-size:0.95rem;"></i>
                <span class="d-none d-md-inline">Add New Driver</span>
                <span class="d-none d-sm-inline d-md-none">Add Driver</span>
                <span class="d-inline d-sm-none">Add</span>
            </a>
        </div>

        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.8px;border-bottom:1.5px solid rgba(0,0,0,0.04);">
                            <th class="ps-4 py-3 pl-4 align-middle">Driver</th>
                            <th class="py-3 align-middle d-none d-sm-table-cell">Email</th>
                            <th class="py-3 align-middle">Status</th>
                            <th class="py-3 align-middle">Workload</th>
                            <th class="py-3 align-middle d-none d-md-table-cell">Joined</th>
                            <th class="py-3 align-middle text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.02);transition:all 0.2s ease;">
                            <td class="ps-4 pl-4 py-3 align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                         style="width:42px;height:42px;min-width:42px;font-size:1rem;">
                                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                                           class="text-dark-theme-aware text-hover-primary text-decoration-none font-weight-bold"
                                           style="font-size:0.88rem;">{{ $driver->name }}</a>
                                        <span class="d-block d-sm-none text-muted small" style="font-size:0.76rem;font-weight:normal;">{{ $driver->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="align-middle text-muted small d-none d-sm-table-cell" style="font-size:0.82rem;">
                                {{ $driver->email }}
                            </td>

                            <td class="align-middle">
                                @if($driver->is_on_duty)
                                    <span class="badge bg-soft-success d-inline-flex align-items-center font-weight-bold px-2" style="font-size:0.72rem;border-radius:20px;padding-top:4px;padding-bottom:4px;">
                                        <span class="pulse-green mr-1"></span> ON DUTY
                                    </span>
                                @else
                                    <span class="badge bg-soft-secondary d-inline-flex align-items-center font-weight-bold px-2" style="font-size:0.72rem;border-radius:20px;padding-top:4px;padding-bottom:4px;">
                                        <span class="indicator-grey mr-1"></span> OFF DUTY
                                    </span>
                                @endif
                            </td>

                            <td class="align-middle">
                                <span class="badge bg-soft-primary font-weight-bold px-3" style="font-size:0.74rem;border-radius:20px;padding-top:5px;padding-bottom:5px;">
                                    {{ $driver->processing_orders_count }} Orders
                                </span>
                            </td>

                            <td class="align-middle text-muted small d-none d-md-table-cell" style="font-size:0.82rem;">
                                {{ $driver->created_at->format('d M Y') }}
                            </td>

                            <td class="text-right pr-4 align-middle">
                                <div class="d-flex align-items-center justify-content-end" style="gap:8px;">
                                    {{-- Track Location --}}
                                    <button type="button"
                                            class="btn-track-driver"
                                            data-driver-id="{{ $driver->id }}"
                                            data-driver-name="{{ $driver->name }}"
                                            data-on-duty="{{ $driver->is_on_duty ? '1' : '0' }}"
                                            title="Track Live Location"
                                            data-toggle="modal"
                                            data-target="#trackDriverModal">
                                        <i class="bi bi-broadcast"></i>
                                        <span class="d-none d-md-inline">Track</span>
                                    </button>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="d-inline-block m-0"
                                          data-confirm="Permanently delete this driver account?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-premium-delete">
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-md-inline">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center py-4">
                                    <i class="bi bi-truck text-muted mb-3" style="font-size:2.5rem;"></i>
                                    <p class="text-muted font-weight-bold mb-0" style="font-size:0.9rem;">No drivers registered yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     LIVE TRACKING MODAL
     Opens when "Track" is clicked. Polls the location API every 15 s.
     Polling starts on modal open, stops on modal close — no wasted requests.
════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="trackDriverModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:720px;">
        <div class="modal-content track-modal-card">

            {{-- Header --}}
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div class="track-modal-avatar" id="track-modal-avatar">?</div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" style="font-family:'Outfit',sans-serif;font-size:1.05rem;" id="track-modal-driver-name">Driver</h5>
                        <div id="track-modal-duty-badge"></div>
                    </div>
                </div>
                <div class="ml-auto d-flex align-items-center" style="gap:10px;">
                    <span id="track-refresh-label" class="text-muted" style="font-size:0.75rem;"></span>
                    <button type="button" id="track-manual-refresh" class="btn-track-refresh" title="Refresh now">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button type="button" class="close" data-dismiss="modal" style="color:#94a3b8;font-size:1.4rem;padding:0;">&times;</button>
                </div>
            </div>

            {{-- Stats strip --}}
            <div class="px-4 pt-3 pb-0 d-flex flex-wrap" style="gap:10px;">
                <div class="track-stat-chip" id="track-stat-orders">
                    <i class="bi bi-box-seam"></i> <span id="track-orders-count">—</span> active orders
                </div>
                <div class="track-stat-chip" id="track-stat-age">
                    <i class="bi bi-clock"></i> <span id="track-location-age">No location yet</span>
                </div>
                <div class="track-stat-chip" id="track-stat-motion">
                    <i class="bi bi-arrow-up-right-circle" id="track-motion-icon"></i>
                    <span id="track-motion-text">Waiting…</span>
                </div>
                <div class="track-stat-chip" id="track-stat-coords">
                    <i class="bi bi-pin-map"></i> <span id="track-coords-text">—</span>
                </div>
            </div>

            {{-- Map — overlay + map wrapped together so overlay positions correctly --}}
            <div class="modal-body px-4 pt-3 pb-0">
                <div style="position:relative;">
                    <div id="track-map-overlay" class="track-map-overlay">
                        <div class="track-map-overlay-inner" id="track-map-overlay-content">
                            <div class="track-spinner"></div>
                            <span id="track-map-overlay-text">Loading location…</span>
                        </div>
                    </div>
                    <div id="driver-tracking-map" class="track-map rounded-3"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                <span class="text-muted" style="font-size:0.75rem;">
                    <i class="bi bi-info-circle mr-1"></i>
                    Live — refreshes every 10 s. Tracking stops automatically when dismissed.
                </span>
                <button type="button" class="btn-close-track" data-dismiss="modal">
                    <i class="bi bi-x-circle mr-1"></i> Stop Tracking
                </button>
            </div>

        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
.rounded-4 { border-radius: 18px !important; }

/* ── Soft badges ─────────────────────────────────────────────────────────── */
.bg-soft-primary   { background: rgba(108,92,231,0.1) !important; color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
html[data-admin-theme="dark"] .bg-soft-primary   { background: rgba(167,139,250,0.15) !important; color: #a78bfa !important; }
html[data-admin-theme="dark"] .bg-soft-success   { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148,163,184,0.15) !important; color: #94a3b8 !important; }

/* ── Table ───────────────────────────────────────────────────────────────── */
tbody tr:hover { background-color: rgba(108,92,231,0.015) !important; }
html[data-admin-theme="dark"] tbody tr:hover { background-color: rgba(255,255,255,0.01) !important; }
html[data-admin-theme="dark"] td { color: #cbd5e1 !important; }
html[data-admin-theme="light"] .text-dark-theme-aware { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-dark-theme-aware { color: #cbd5e1 !important; }
.text-hover-primary:hover { color: #6c5ce7 !important; text-decoration: none !important; }
html[data-admin-theme="dark"] .text-hover-primary:hover { color: #a78bfa !important; }

/* ── Status dots ─────────────────────────────────────────────────────────── */
.pulse-green {
    width: 7px; height: 7px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: pulseG 1.6s infinite ease-in-out;
}
@keyframes pulseG {
    0%,100% { transform: scale(0.95); opacity: 0.5; }
    50%      { transform: scale(1.2);  opacity: 1;   }
}
.indicator-grey { width:7px; height:7px; background:#64748b; border-radius:50%; display:inline-block; }

/* ── Add Driver button ───────────────────────────────────────────────────── */
.btn-curved {
    border-radius: 30px !important;
    padding: 8px 24px !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
    color: #fff !important;
    transition: all 0.25s ease !important;
}
.btn-curved:hover { transform: translateY(-1px) !important; box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }

/* ── Track button ────────────────────────────────────────────────────────── */
.btn-track-driver {
    background: transparent;
    border: 1.5px solid #00b894;
    color: #00b894;
    border-radius: 50px;
    padding: 5px 14px;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
    outline: none;
    line-height: 1;
}
.btn-track-driver:hover {
    background: rgba(0,184,148,0.08);
    box-shadow: 0 4px 10px rgba(0,184,148,0.15);
    transform: translateY(-0.5px);
}
html[data-admin-theme="dark"] .btn-track-driver { border-color: #00cec9; color: #00cec9; }

/* ── Delete button ───────────────────────────────────────────────────────── */
.btn-premium-delete {
    background: transparent;
    border: 1.5px solid #ff3366;
    color: #ff3366;
    border-radius: 50px;
    padding: 5px 14px;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
    outline: none;
    line-height: 1;
}
.btn-premium-delete:hover { background: rgba(255,51,102,0.05); box-shadow: 0 4px 10px rgba(255,51,102,0.12); transform: translateY(-0.5px); }

/* ── Dark mode card ──────────────────────────────────────────────────────── */
html[data-admin-theme="dark"] .card { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }

/* ═══════════════════════════════════════════════════════════════════════════
   TRACKING MODAL
══════════════════════════════════════════════════════════════════════════ */
.track-modal-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 22px;
    overflow: hidden;
}
html[data-admin-theme="light"] .track-modal-card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    color: #1e293b;
}

.track-modal-avatar {
    width: 44px; height: 44px; border-radius: 14px;
    background: linear-gradient(135deg,#6c5ce7,#a78bfa);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.1rem;
    color: #fff; flex-shrink: 0;
}

.track-stat-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 30px;
    font-size: 0.76rem; color: rgba(255,255,255,0.55);
}
html[data-admin-theme="light"] .track-stat-chip {
    background: rgba(0,0,0,0.03);
    border-color: rgba(0,0,0,0.07);
    color: #475569;
}

/* Staleness colour classes applied via JS */
.track-stat-chip.fresh  { border-color: rgba(0,184,148,0.3); color: #34d399; background: rgba(0,184,148,0.07); }
.track-stat-chip.stale  { border-color: rgba(245,158,11,0.3); color: #fbbf24; background: rgba(245,158,11,0.07); }
.track-stat-chip.old    { border-color: rgba(239,68,68,0.3);  color: #f87171; background: rgba(239,68,68,0.07);  }

/* Map container */
.track-map {
    width: 100%; height: 380px;
    background: #0f172a;
    border-radius: 14px;
    position: relative;
    z-index: 1;
    border: 1px solid rgba(255,255,255,0.07);
}
html[data-admin-theme="light"] .track-map {
    background: #e2e8f0;
    border-color: rgba(0,0,0,0.08);
}

/* Map overlay (loading / error / no-location state) */
.track-map-overlay {
    position: absolute; inset: 0;
    background: rgba(15,23,42,0.7);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    z-index: 1000; backdrop-filter: blur(4px);
    transition: opacity 0.3s ease;
}
html[data-admin-theme="light"] .track-map-overlay { background: rgba(241,245,249,0.75); }
.track-map-overlay.hidden { opacity: 0; pointer-events: none; }

.track-map-overlay-inner {
    display: flex; flex-direction: column; align-items: center;
    gap: 12px; color: rgba(255,255,255,0.7); font-size: 0.88rem; font-weight: 600;
    font-family: 'Outfit', sans-serif; text-align: center; padding: 0 24px;
}
html[data-admin-theme="light"] .track-map-overlay-inner { color: #475569; }

/* Spinner */
.track-spinner {
    width: 36px; height: 36px;
    border: 3px solid rgba(108,92,231,0.2);
    border-top-color: #6c5ce7;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Refresh button */
.btn-track-refresh {
    width: 32px; height: 32px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.55);
    cursor: pointer; transition: all 0.2s ease; font-size: 0.9rem;
    outline: none;
}
.btn-track-refresh:hover { background: rgba(255,255,255,0.12); color: #fff; }
html[data-admin-theme="light"] .btn-track-refresh {
    background: rgba(0,0,0,0.04); border-color: rgba(0,0,0,0.1); color: #64748b;
}
html[data-admin-theme="light"] .btn-track-refresh:hover { background: rgba(0,0,0,0.08); color: #1e293b; }

.btn-track-refresh.spinning i { animation: spin 0.6s linear infinite; }

/* Footer close button */
.btn-close-track {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 18px; border-radius: 50px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
    font-size: 0.82rem; font-weight: 700;
    font-family: 'Outfit', sans-serif; cursor: pointer;
    transition: all 0.2s ease; outline: none;
}
.btn-close-track:hover { background: rgba(255,255,255,0.1); color: #fff; }
html[data-admin-theme="light"] .btn-close-track {
    background: rgba(0,0,0,0.04); border-color: rgba(0,0,0,0.1); color: #475569;
}

/* Motion chip states */
.track-stat-chip.moving  { border-color:rgba(0,184,148,0.35); color:#34d399; background:rgba(0,184,148,0.08); }
.track-stat-chip.stopped { border-color:rgba(148,163,184,0.2); color:#94a3b8; }

/* Duty badge in modal */
.modal-duty-on  { display:inline-flex; align-items:center; gap:5px; font-size:0.72rem; font-weight:700; color:#34d399; }
.modal-duty-off { display:inline-flex; align-items:center; gap:5px; font-size:0.72rem; font-weight:700; color:#94a3b8; }

/* ── Mobile ──────────────────────────────────────────────────────────────── */
@media (max-width: 767px) {
    .track-map { height: 260px; }
    .modal-dialog { margin: 0.75rem; }
    .track-modal-card { border-radius: 16px; }
    .btn-curved { padding: 6px 16px !important; font-size: 0.78rem !important; }
    .btn-track-driver, .btn-premium-delete { padding: 5px 10px !important; font-size: 0.73rem !important; }
}
@media (max-width: 575px) {
    .track-map { height: 220px; }
    .card-header {
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .card-header .btn-curved {
        margin-left: auto !important;
    }
}
</style>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}"></script>
<script nonce="{{ Vite::cspNonce() }}">
(function () {
    'use strict';

    const POLL_MS    = 10000;
    const STALE_WARN = 120;
    const STALE_OLD  = 300;
    const MAX_TRAIL  = 20;

    let pollTimer   = null;
    let countTimer  = null;
    let trackMap    = null;
    let trackMarker = null;
    let trackCircle = null;
    let trailLine   = null;
    let infoWindow  = null;
    let posTrail    = [];
    let lastLat     = null;
    let lastLng     = null;
    let animTimer   = null;
    let currentId   = null;
    let mapReady    = false;
    let pendingData = null;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const modal       = document.getElementById('trackDriverModal');
    const mapEl       = document.getElementById('driver-tracking-map');
    const overlay     = document.getElementById('track-map-overlay');
    const overlayTxt  = document.getElementById('track-map-overlay-text');
    const spinner     = overlay ? overlay.querySelector('.track-spinner') : null;
    const nameEl      = document.getElementById('track-modal-driver-name');
    const avatarEl    = document.getElementById('track-modal-avatar');
    const dutyEl      = document.getElementById('track-modal-duty-badge');
    const ordersEl    = document.getElementById('track-orders-count');
    const ageEl       = document.getElementById('track-location-age');
    const ageChipEl   = document.getElementById('track-stat-age');
    const motionEl    = document.getElementById('track-motion-text');
    const motionChip  = document.getElementById('track-stat-motion');
    const coordsEl    = document.getElementById('track-coords-text');
    const countEl     = document.getElementById('track-refresh-label');
    const refreshBtn  = document.getElementById('track-manual-refresh');
    const CSRF        = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute('content') || '';

    // Full Google Maps Night style array
    const GOOGLE_NIGHT = [
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

    // ── Map init ──────────────────────────────────────────────────────────────
    function initMap() {
        if (mapReady || !mapEl) return;

        if (typeof google === 'undefined' || !google.maps) {
            setOverlay('⚠ Google Maps failed to load.', false);
            return;
        }

        try {
            const dark = document.documentElement.getAttribute('data-admin-theme') === 'dark';
            trackMap = new google.maps.Map(mapEl, {
                zoom: 15,
                center: { lat: 51.505, lng: -0.09 },
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
                streetViewControl: false,
                mapTypeControl: false,
                fullscreenControl: false,
                styles: dark ? GOOGLE_NIGHT : [],
            });

            infoWindow = new google.maps.InfoWindow();
            mapReady = true;
        } catch (e) {
            console.error('Google Map init error:', e);
        }
    }

    // Watch data-admin-theme attribute for changes
    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.attributeName === 'data-admin-theme' && trackMap) {
                const dark = document.documentElement.getAttribute('data-admin-theme') === 'dark';
                trackMap.setOptions({ styles: dark ? GOOGLE_NIGHT : [] });
            }
        });
    }).observe(document.documentElement, { attributes: true });

    // ── Overlay ───────────────────────────────────────────────────────────────
    function setOverlay(text, spin) {
        if (!overlay) return;
        overlay.classList.remove('hidden');
        if (overlayTxt) overlayTxt.textContent = text;
        if (spinner)    spinner.style.display  = spin ? 'block' : 'none';
    }
    function hideOverlay() { overlay && overlay.classList.add('hidden'); }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function fmtAge(s) {
        if (s === null) return 'No location yet';
        if (s < 60)     return 'Just now';
        if (s < 3600)   return Math.floor(s / 60) + ' min ago';
        return Math.floor(s / 3600) + ' hr ago';
    }
    function ageClass(s) {
        if (s === null || s > STALE_OLD)  return 'old';
        if (s > STALE_WARN)               return 'stale';
        return 'fresh';
    }
    function haversineM(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const d1 = (lat2-lat1)*Math.PI/180, d2 = (lng2-lng1)*Math.PI/180;
        const a  = Math.sin(d1/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(d2/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // ── Countdown ─────────────────────────────────────────────────────────────
    function startCountdown() {
        clearInterval(countTimer);
        let secs = POLL_MS / 1000;
        if (countEl) countEl.textContent = 'Refreshes in ' + secs + ' s';
        countTimer = setInterval(function () {
            secs--;
            if (countEl) countEl.textContent = 'Refreshes in ' + secs + ' s';
            if (secs <= 0) clearInterval(countTimer);
        }, 1000);
    }

    // ── Smooth marker animation ───────────────────────────────────────────────
    function animateMarker(fromLat, fromLng, toLat, toLng) {
        if (animTimer) clearInterval(animTimer);
        var steps = 40, step = 0;
        animTimer = setInterval(function () {
            step++;
            var f   = step / steps;
            var lat = fromLat + (toLat - fromLat) * f;
            var lng = fromLng + (toLng - fromLng) * f;
            if (trackMarker) trackMarker.setPosition({ lat: lat, lng: lng });
            if (step >= steps) clearInterval(animTimer);
        }, 25);
    }

    // ── Trail breadcrumb ──────────────────────────────────────────────────────
    function updateTrail(lat, lng) {
        posTrail.push({ lat: lat, lng: lng });
        if (posTrail.length > MAX_TRAIL) posTrail.shift();
        if (posTrail.length < 2) return;
        if (!trailLine) {
            trailLine = new google.maps.Polyline({
                path: posTrail,
                geodesic: true,
                strokeColor: '#6c5ce7',
                strokeOpacity: 0.4,
                strokeWeight: 2.5,
                map: trackMap,
            });
        } else {
            trailLine.setPath(posTrail);
        }
    }

    // ── Render location data from API ─────────────────────────────────────────
    function render(data) {
        var lat  = data.latitude  != null ? parseFloat(data.latitude)  : null;
        var lng  = data.longitude != null ? parseFloat(data.longitude) : null;
        var ageS = data.location_age_seconds;
        var duty = data.is_on_duty;

        // ── Stats ─────────────────────────────────────────────────────────────
        try {
            if (dutyEl) dutyEl.innerHTML = duty
                ? '<span class="modal-duty-on"><span class="pulse-green"></span>On Duty</span>'
                : '<span class="modal-duty-off"><i class="bi bi-moon-stars-fill"></i>Off Duty</span>';
            if (ordersEl) ordersEl.textContent = (data.active_orders_count != null ? data.active_orders_count : '—');
            if (ageEl)     ageEl.textContent   = fmtAge(ageS);
            if (ageChipEl) ageChipEl.className = 'track-stat-chip ' + ageClass(ageS);
        } catch(e) { console.warn('Stats render error:', e); }

        // ── No position ───────────────────────────────────────────────────────
        if (!lat || !lng) {
            setOverlay(duty ? '📍 Waiting for driver GPS…' : '⚫ Driver is off duty.', false);
            if (coordsEl)   coordsEl.textContent  = '—';
            if (motionEl)   motionEl.textContent   = 'No data';
            if (motionChip) motionChip.className   = 'track-stat-chip';
            return;
        }

        // ── Coords + motion ───────────────────────────────────────────────────
        try {
            if (coordsEl) coordsEl.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
            var moved = lastLat !== null && haversineM(lastLat, lastLng, lat, lng) > 8;
            if (motionEl)   motionEl.textContent  = moved ? 'Moving' : 'Stationary';
            if (motionChip) motionChip.className  = 'track-stat-chip ' + (moved ? 'moving' : 'stopped');
        } catch(e) { console.warn('Coords render error:', e); }

        // ── Map operations ────────────────────────────────────────────────────
        try {
            if (!mapReady) initMap();

            if (!trackMap || !mapReady) {
                setOverlay('📍 ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + '\nMap unavailable', false);
                lastLat = lat; lastLng = lng;
                return;
            }

            var fresh  = ageS !== null && ageS < STALE_OLD;
            var driverName = nameEl ? nameEl.textContent : 'Driver';

            if (!trackMarker) {
                trackMarker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: trackMap,
                    title: driverName,
                    icon: {
                        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                        scale: 7,
                        fillColor: moved ? '#6c5ce7' : '#94a3b8',
                        fillOpacity: 0.95,
                        strokeColor: '#ffffff',
                        strokeWeight: 2.5,
                    },
                });

                trackMarker.addListener('click', function() {
                    infoWindow.setContent('<b>' + driverName + '</b><br>' + fmtAge(ageS));
                    infoWindow.open(trackMap, trackMarker);
                });
            } else {
                if (lastLat !== null && moved) {
                    animateMarker(lastLat, lastLng, lat, lng);
                } else {
                    trackMarker.setPosition({ lat: lat, lng: lng });
                }
                trackMarker.setIcon({
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 7,
                    fillColor: moved ? '#6c5ce7' : '#94a3b8',
                    fillOpacity: 0.95,
                    strokeColor: '#ffffff',
                    strokeWeight: 2.5,
                });
            }

            if (!trackCircle) {
                trackCircle = new google.maps.Circle({
                    strokeColor: fresh ? '#6c5ce7' : '#94a3b8',
                    strokeOpacity: 0.8,
                    strokeWeight: 1,
                    fillColor: fresh ? '#6c5ce7' : '#94a3b8',
                    fillOpacity: 0.07,
                    map: trackMap,
                    center: { lat: lat, lng: lng },
                    radius: 60,
                });
            } else {
                trackCircle.setCenter({ lat: lat, lng: lng });
                trackCircle.setOptions({
                    strokeColor: fresh ? '#6c5ce7' : '#94a3b8',
                    fillColor: fresh ? '#6c5ce7' : '#94a3b8',
                });
            }

            if (lastLat === null || moved) updateTrail(lat, lng);

            if (lastLat === null) {
                trackMap.setCenter({ lat: lat, lng: lng });
                trackMap.setZoom(15);
            } else if (moved) {
                trackMap.panTo({ lat: lat, lng: lng });
            }

            hideOverlay();
        } catch (mapErr) {
            console.error('Map render error:', mapErr);
            setOverlay('📍 ' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ' — map error, retrying…', false);
        }

        lastLat = lat;
        lastLng = lng;
    }

    // ── Fetch ─────────────────────────────────────────────────────────────────
    function fetchLocation(manual) {
        if (!currentId) return;
        if (manual && refreshBtn) refreshBtn.classList.add('spinning');

        fetch('/admin/drivers/' + currentId + '/location', {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(function (r) {
            if (r.status === 403) throw { isHttp: true, code: 403 };
            if (r.status === 404) throw { isHttp: true, code: 404 };
            if (!r.ok)            throw { isHttp: true, code: r.status };
            return r.json();
        })
        .then(function (d) {
            try { render(d); } catch (e) { console.error('render() error:', e); }
            startCountdown();
        })
        .catch(function (e) {
            if (!e || !e.isHttp) return;
            var msg = e.code === 404 ? 'Driver not found.'
                    : e.code === 403 ? 'Access denied.'
                    : 'Connection error (HTTP ' + e.code + '). Retrying…';
            setOverlay('⚠ ' + msg, false);
        })
        .finally(function () {
            if (refreshBtn) refreshBtn.classList.remove('spinning');
        });
    }

    // ── Poll lifecycle ────────────────────────────────────────────────────────
    function startPoll() {
        stopPoll();
        fetchLocation(false);
        pollTimer = setInterval(function () { fetchLocation(false); }, POLL_MS);
    }
    function stopPoll() {
        clearInterval(pollTimer);
        clearInterval(countTimer);
        clearInterval(animTimer);
        pollTimer = null;
    }

    // ── Store driver data on button click ─────────────────────────────────────
    document.querySelectorAll('.btn-track-driver').forEach(function (btn) {
        btn.addEventListener('click', function () {
            pendingData = {
                id:     this.getAttribute('data-driver-id'),
                name:   this.getAttribute('data-driver-name') || 'Driver',
                onDuty: this.getAttribute('data-on-duty') === '1',
            };
        });
    });

    // ── Modal events ──────────────────────────────────────────────────────────
    $(modal).on('show.bs.modal', function () {
        if (!pendingData) return;

        currentId = pendingData.id;
        var name  = pendingData.name;
        var duty  = pendingData.onDuty;

        if (nameEl)   nameEl.textContent  = name;
        if (avatarEl) avatarEl.textContent = name.charAt(0).toUpperCase();
        if (dutyEl)   dutyEl.innerHTML     = duty
            ? '<span class="modal-duty-on"><span class="pulse-green"></span>On Duty</span>'
            : '<span class="modal-duty-off"><i class="bi bi-moon-stars-fill"></i>Off Duty</span>';

        // Reset
        if (ordersEl)   ordersEl.textContent  = '—';
        if (ageEl)      ageEl.textContent      = 'Loading…';
        if (coordsEl)   coordsEl.textContent   = '—';
        if (motionEl)   motionEl.textContent   = 'Waiting…';
        if (ageChipEl)  ageChipEl.className    = 'track-stat-chip';
        if (motionChip) motionChip.className   = 'track-stat-chip';
        if (countEl)    countEl.textContent    = '';
        trackMarker = null; trackCircle = null; trailLine = null;
        posTrail = []; lastLat = null; lastLng = null;
        setOverlay('Loading location…', true);
    });

    $(modal).on('shown.bs.modal', function () {
        initMap();
        if (trackMap) {
            google.maps.event.trigger(trackMap, 'resize');
        }
        // Ensure infoWindow exists (initMap only runs once; modal can open many times)
        if (!infoWindow && typeof google !== 'undefined' && google.maps) {
            infoWindow = new google.maps.InfoWindow();
        }
        startPoll();
    });

    $(modal).on('hidden.bs.modal', function () {
        stopPoll();
        currentId = null;
        if (trackMarker) { trackMarker.setMap(null); trackMarker = null; }
        if (trackCircle) { trackCircle.setMap(null); trackCircle = null; }
        if (trailLine)   { trailLine.setMap(null);   trailLine   = null; }
        if (infoWindow)  { infoWindow.close(); /* do NOT null — initMap won't recreate it */ }
        posTrail = []; lastLat = null; lastLng = null;
        if (countEl) countEl.textContent = '';
    });

    if (refreshBtn) refreshBtn.addEventListener('click', function () { fetchLocation(true); });

    document.addEventListener('visibilitychange', function () {
        if (!currentId) return;
        document.hidden ? stopPoll() : startPoll();
    });

})();
</script>
@endpush
