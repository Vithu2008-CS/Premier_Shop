{{--
    admin/drivers/index.blade.php — Driver monitoring dashboard
    ============================================================
    Table of all driver accounts: name, email, assigned active orders count, status.
    "Track" button per row opens a Leaflet live-location modal (polls every 15 s,
    stops when the modal is closed — never hammers the server when not in use).
    Variable: $drivers (with processing_orders_count)
--}}
@extends('layouts.admin_noble')
@section('title', 'Driver Monitoring')

@push('plugin-styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">
@endpush

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
                Add New Driver
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
                                          onsubmit="return confirm('Permanently delete this driver account?');">
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
                <div class="track-stat-chip" id="track-stat-coords">
                    <i class="bi bi-pin-map"></i> <span id="track-coords-text">—</span>
                </div>
            </div>

            {{-- Map --}}
            <div class="modal-body px-4 pt-3 pb-0">
                {{-- Loading overlay --}}
                <div id="track-map-overlay" class="track-map-overlay">
                    <div class="track-map-overlay-inner" id="track-map-overlay-content">
                        <div class="track-spinner"></div>
                        <span id="track-map-overlay-text">Loading location…</span>
                    </div>
                </div>
                <div id="driver-tracking-map" class="track-map rounded-3"></div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 px-4 py-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                <span class="text-muted" style="font-size:0.75rem;">
                    <i class="bi bi-info-circle mr-1"></i>
                    Refreshes every 15 s while this panel is open. Closes tracking automatically when dismissed.
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

/* Leaflet dark tile contrast fix */
.leaflet-tile { filter: brightness(0.92) contrast(1.05); }
html[data-admin-theme="light"] .leaflet-tile { filter: none; }

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
    .card-header { flex-direction: column; align-items: flex-start !important; }
}
</style>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLGk="
        crossorigin=""></script>
<script>
(function () {
    'use strict';

    // ── Config ────────────────────────────────────────────────────────────────
    const POLL_INTERVAL_MS  = 15000;   // 15 s while modal is open
    const STALE_WARN_S      = 120;     // yellow after 2 min
    const STALE_OLD_S       = 300;     // red after 5 min

    // ── State ─────────────────────────────────────────────────────────────────
    let pollTimer    = null;
    let trackMap     = null;
    let trackMarker  = null;
    let trackCircle  = null;
    let currentDriverId   = null;
    let lastRefreshTime   = null;
    let mapInitialised    = false;
    let pollsUntilNext    = POLL_INTERVAL_MS / 1000;
    let countdownTimer    = null;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const modal        = document.getElementById('trackDriverModal');
    const mapEl        = document.getElementById('driver-tracking-map');
    const overlay      = document.getElementById('track-map-overlay');
    const overlayText  = document.getElementById('track-map-overlay-text');
    const spinner      = overlay?.querySelector('.track-spinner');
    const driverNameEl = document.getElementById('track-modal-driver-name');
    const avatarEl     = document.getElementById('track-modal-avatar');
    const dutyBadgeEl  = document.getElementById('track-modal-duty-badge');
    const ordersEl     = document.getElementById('track-orders-count');
    const ageEl        = document.getElementById('track-location-age');
    const ageChipEl    = document.getElementById('track-stat-age');
    const coordsEl     = document.getElementById('track-coords-text');
    const refreshLabel = document.getElementById('track-refresh-label');
    const refreshBtn   = document.getElementById('track-manual-refresh');

    const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute?.('content') ?? '';

    // ── Custom map marker (pulsing truck icon) ────────────────────────────────
    function makeDriverIcon(fresh) {
        const colour = fresh ? '#6c5ce7' : '#94a3b8';
        const glow   = fresh ? 'rgba(108,92,231,0.35)' : 'rgba(148,163,184,0.2)';
        return L.divIcon({
            html: `<div style="
                width:42px;height:42px;
                background:${colour};
                border-radius:50%;
                border:3px solid #fff;
                box-shadow:0 0 0 6px ${glow}, 0 4px 14px rgba(0,0,0,0.35);
                display:flex;align-items:center;justify-content:center;
                font-size:18px;
            ">🚚</div>`,
            className: '',
            iconSize: [42, 42],
            iconAnchor: [21, 21],
            popupAnchor: [0, -24],
        });
    }

    // ── Map init (deferred until first show so dimensions are known) ──────────
    function initMap() {
        if (mapInitialised || !mapEl) return;
        try {
            trackMap = L.map('driver-tracking-map', { zoomControl: true, attributionControl: true });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(trackMap);
            mapInitialised = true;
        } catch (e) {
            showOverlay('❌ Map failed to load. Check internet connection.', false);
        }
    }

    // ── Overlay helpers ───────────────────────────────────────────────────────
    function showOverlay(text, showSpinner) {
        if (!overlay) return;
        overlay.classList.remove('hidden');
        if (overlayText) overlayText.textContent = text;
        if (spinner)     spinner.style.display = showSpinner ? 'block' : 'none';
    }
    function hideOverlay() {
        if (overlay) overlay.classList.add('hidden');
    }

    // ── Staleness helpers ─────────────────────────────────────────────────────
    function ageText(seconds) {
        if (seconds === null) return 'No location yet';
        if (seconds < 60)     return 'Just now';
        if (seconds < 3600)   return `${Math.floor(seconds / 60)} min ago`;
        return `${Math.floor(seconds / 3600)} hr ago`;
    }
    function ageClass(seconds) {
        if (seconds === null || seconds > STALE_OLD_S)  return 'old';
        if (seconds > STALE_WARN_S)                     return 'stale';
        return 'fresh';
    }

    // ── Countdown label ───────────────────────────────────────────────────────
    function startCountdown() {
        clearInterval(countdownTimer);
        pollsUntilNext = POLL_INTERVAL_MS / 1000;
        countdownTimer = setInterval(function () {
            pollsUntilNext--;
            if (refreshLabel) refreshLabel.textContent = `Refreshes in ${pollsUntilNext} s`;
            if (pollsUntilNext <= 0) clearInterval(countdownTimer);
        }, 1000);
    }

    // ── Fetch + render location ───────────────────────────────────────────────
    function fetchLocation(manual) {
        if (!currentDriverId) return;

        if (manual) {
            refreshBtn?.classList.add('spinning');
        }

        fetch(`{{ url('admin/drivers') }}/${currentDriverId}/location`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(function (res) {
            if (res.status === 403) throw Object.assign(new Error('Forbidden'), { code: 403 });
            if (res.status === 404) throw Object.assign(new Error('Driver not found'), { code: 404 });
            if (!res.ok)            throw Object.assign(new Error('Server error ' + res.status), { code: res.status });
            return res.json();
        })
        .then(function (data) {
            renderLocation(data);
            lastRefreshTime = Date.now();
            startCountdown();
        })
        .catch(function (err) {
            const msg = err.code === 404 ? 'Driver not found.'
                      : err.code === 403 ? 'Access denied.'
                      : 'Failed to fetch location. Check connection.';
            showOverlay('⚠ ' + msg, false);
        })
        .finally(function () {
            refreshBtn?.classList.remove('spinning');
        });
    }

    function renderLocation(data) {
        const { latitude: lat, longitude: lng, is_on_duty, location_age_seconds, active_orders_count } = data;

        // Duty badge
        if (dutyBadgeEl) {
            dutyBadgeEl.innerHTML = is_on_duty
                ? '<span class="modal-duty-on"><span class="pulse-green"></span>On Duty</span>'
                : '<span class="modal-duty-off"><i class="bi bi-moon-stars-fill"></i>Off Duty</span>';
        }

        // Orders chip
        if (ordersEl) ordersEl.textContent = active_orders_count ?? '—';

        // Age chip
        const ageS   = location_age_seconds;
        const ageTxt = ageText(ageS);
        const ageCls = ageClass(ageS);
        if (ageEl)    ageEl.textContent = ageTxt;
        if (ageChipEl) {
            ageChipEl.className = 'track-stat-chip ' + ageCls;
        }

        // No location data yet
        if (!lat || !lng) {
            showOverlay(is_on_duty ? '📍 Waiting for driver to share location…' : '⚫ Driver is off duty — no location available.', false);
            if (coordsEl) coordsEl.textContent = '—';
            return;
        }

        // Coords chip
        if (coordsEl) coordsEl.textContent = `${parseFloat(lat).toFixed(5)}, ${parseFloat(lng).toFixed(5)}`;

        // Map marker
        const fresh = ageS !== null && ageS < STALE_OLD_S;
        const latlng = [parseFloat(lat), parseFloat(lng)];

        if (!trackMarker) {
            trackMarker = L.marker(latlng, { icon: makeDriverIcon(fresh) })
                .bindPopup(`<b>${driverNameEl?.textContent ?? 'Driver'}</b><br>Updated: ${ageTxt}`)
                .addTo(trackMap);
        } else {
            trackMarker.setLatLng(latlng)
                .setIcon(makeDriverIcon(fresh))
                .setPopupContent(`<b>${driverNameEl?.textContent ?? 'Driver'}</b><br>Updated: ${ageTxt}`);
        }

        // Accuracy circle (500 m placeholder when accuracy is unknown from server)
        if (!trackCircle) {
            trackCircle = L.circle(latlng, {
                radius: 80, weight: 1,
                color: fresh ? '#6c5ce7' : '#94a3b8',
                fillColor: fresh ? '#6c5ce7' : '#94a3b8',
                fillOpacity: 0.07,
            }).addTo(trackMap);
        } else {
            trackCircle.setLatLng(latlng).setStyle({
                color: fresh ? '#6c5ce7' : '#94a3b8',
                fillColor: fresh ? '#6c5ce7' : '#94a3b8',
            });
        }

        trackMap.setView(latlng, trackMap.getZoom() < 14 ? 15 : trackMap.getZoom());
        hideOverlay();
    }

    // ── Polling lifecycle ─────────────────────────────────────────────────────
    function startPolling() {
        stopPolling();
        fetchLocation(false);
        pollTimer = setInterval(function () { fetchLocation(false); }, POLL_INTERVAL_MS);
    }
    function stopPolling() {
        clearInterval(pollTimer);
        clearInterval(countdownTimer);
        pollTimer = null;
    }

    // ── Modal events ──────────────────────────────────────────────────────────
    // Bootstrap fires shown.bs.modal after the modal is fully visible
    $(modal).on('show.bs.modal', function (e) {
        const btn       = e.relatedTarget;
        currentDriverId = btn?.dataset?.driverId;
        const name      = btn?.dataset?.driverName ?? 'Driver';
        const onDuty    = btn?.dataset?.onDuty === '1';

        // Update header
        if (driverNameEl) driverNameEl.textContent = name;
        if (avatarEl)     avatarEl.textContent     = name.charAt(0).toUpperCase();
        if (dutyBadgeEl)  dutyBadgeEl.innerHTML    = onDuty
            ? '<span class="modal-duty-on"><span class="pulse-green"></span>On Duty</span>'
            : '<span class="modal-duty-off"><i class="bi bi-moon-stars-fill"></i>Off Duty</span>';

        // Reset stat chips
        if (ordersEl)    ordersEl.textContent  = '—';
        if (ageEl)       ageEl.textContent     = 'Loading…';
        if (coordsEl)    coordsEl.textContent  = '—';
        if (ageChipEl)   ageChipEl.className   = 'track-stat-chip';
        if (refreshLabel) refreshLabel.textContent = '';

        // Reset marker state so a new one is created for the new driver
        trackMarker = null;
        trackCircle = null;

        showOverlay('Loading location…', true);
    });

    $(modal).on('shown.bs.modal', function () {
        // Map must be initialised after modal is visible (needs layout dimensions)
        initMap();
        if (trackMap) trackMap.invalidateSize();
        startPolling();
    });

    $(modal).on('hidden.bs.modal', function () {
        stopPolling();
        currentDriverId = null;
        // Clean up old marker/circle from the map so next driver starts fresh
        if (trackMarker && trackMap) { trackMap.removeLayer(trackMarker); trackMarker = null; }
        if (trackCircle && trackMap) { trackMap.removeLayer(trackCircle); trackCircle = null; }
        if (refreshLabel) refreshLabel.textContent = '';
    });

    // ── Manual refresh ────────────────────────────────────────────────────────
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () { fetchLocation(true); });
    }

    // ── Pause polling when tab is hidden, resume on focus ────────────────────
    document.addEventListener('visibilitychange', function () {
        if (!currentDriverId) return;
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
        }
    });

})();
</script>
@endpush
