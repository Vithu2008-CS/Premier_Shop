{{--
    admin/sliders/index.blade.php — Slider management: 3 typed sections
    Variables: $mainSliders, $subSliders1, $subSliders2 (Promotion records)
--}}
@extends('layouts.admin_noble')
@section('title', 'Home Sliders Management')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

.bg-soft-primary   { background: rgba(108,92,231,0.1) !important; color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-info      { background: rgba(6,182,212,0.1) !important;  color: #06b6d4 !important; }
html[data-admin-theme="dark"] .bg-soft-primary   { background: rgba(167,139,250,0.15) !important; color: #a78bfa !important; }
html[data-admin-theme="dark"] .bg-soft-success   { background: rgba(52,211,153,0.15) !important;  color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148,163,184,0.15) !important; color: #94a3b8 !important; }
html[data-admin-theme="dark"] .bg-soft-warning   { background: rgba(251,191,36,0.15) !important;  color: #fbbf24 !important; }
html[data-admin-theme="dark"] .bg-soft-info      { background: rgba(103,232,249,0.15) !important; color: #67e8f9 !important; }

/* Add button */
.btn-curved {
    border-radius: 30px !important; padding: 8px 22px !important;
    font-size: 0.82rem !important; font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important; box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
    color: #fff !important; transition: all 0.25s ease !important;
}
.btn-curved:hover { transform: translateY(-1px) !important; box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }

/* Section header */
.slider-section-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
    margin-bottom: 1.25rem; padding-bottom: 0.75rem;
    border-bottom: 2px solid rgba(108,92,231,0.07);
}
html[data-admin-theme="dark"] .slider-section-header {
    border-bottom-color: rgba(255,255,255,0.05);
}
.slider-section-title {
    display: flex; align-items: center; gap: 10px;
    font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 1.05rem;
}
.slider-type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px; border-radius: 20px;
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.type-pill-main { background: rgba(108,92,231,0.12); color: #6c5ce7; }
.type-pill-sub1 { background: rgba(16,185,129,0.12);  color: #10b981; }
.type-pill-sub2 { background: rgba(245,158,11,0.12);  color: #f59e0b; }
html[data-admin-theme="dark"] .type-pill-main { background: rgba(167,139,250,0.18); color: #a78bfa; }
html[data-admin-theme="dark"] .type-pill-sub1 { background: rgba(52,211,153,0.18);  color: #34d399; }
html[data-admin-theme="dark"] .type-pill-sub2 { background: rgba(251,191,36,0.18);  color: #fbbf24; }

.size-hint {
    font-size: 0.72rem; color: #94a3b8; font-family: monospace;
    background: rgba(0,0,0,0.03); padding: 3px 8px; border-radius: 6px;
}
html[data-admin-theme="dark"] .size-hint { background: rgba(255,255,255,0.04); color: #64748b; }

/* Card grid */
.slider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.25rem;
}
.slider-card {
    border-radius: 18px !important; overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: none !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
}
.slider-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(108,92,231,0.1) !important; }
html[data-admin-theme="dark"] .slider-card:hover { box-shadow: 0 12px 30px rgba(167,139,250,0.12) !important; }

.slider-card-thumb {
    position: relative; width: 100%; padding-top: 52%;
    background: #1e293b center/cover no-repeat; overflow: hidden;
}
.slider-card-thumb-img {
    position: absolute; inset: 0; width: 100%; height: 100%;
    object-fit: cover; transition: transform 0.4s ease;
}
.slider-card:hover .slider-card-thumb-img { transform: scale(1.05); }
.slider-card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.65) 0%, rgba(15,23,42,0) 55%);
    pointer-events: none;
}
.slider-card-badges {
    position: absolute; top: 12px; left: 12px;
    display: flex; gap: 6px; z-index: 2;
}
.slider-badge {
    padding: 4px 10px; border-radius: 20px;
    font-size: 0.63rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
}
.slider-badge.active   { background: rgba(16,185,129,0.85); color: #fff; }
.slider-badge.inactive { background: rgba(100,116,139,0.8);  color: #fff; }
.slider-badge.priority { background: rgba(245,158,11,0.85);  color: #fff; }

/* Button position indicator top-right */
.slider-pos-tag {
    position: absolute; top: 12px; right: 12px; z-index: 2;
    width: 26px; height: 26px; border-radius: 7px;
    background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.75rem;
    border: 1px solid rgba(255,255,255,0.12);
}

/* CTA button preview chip */
.slider-cta-chip {
    position: absolute; bottom: 12px; left: 12px; right: 12px; z-index: 3;
    display: flex; justify-content: center;
}
.slider-cta-chip span {
    display: inline-block; padding: 4px 14px; border-radius: 20px;
    background: rgba(255,255,255,0.9); color: #1a1a2e;
    font-size: 0.72rem; font-weight: 700;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;
}

.slider-card-body { padding: 1rem 1.25rem; }
.slider-card-label {
    font-weight: 600; font-size: 0.82rem; margin-bottom: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.slider-link-chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.7rem; color: #6c5ce7;
    background: rgba(108,92,231,0.06); padding: 3px 9px; border-radius: 20px;
    text-decoration: none !important; max-width: 100%;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 600; transition: all 0.2s ease;
}
.slider-link-chip:hover { background: rgba(108,92,231,0.12); color: #6c5ce7; }
html[data-admin-theme="dark"] .slider-link-chip { color: #a78bfa; background: rgba(167,139,250,0.1); }
html[data-admin-theme="dark"] .slider-link-chip:hover { background: rgba(167,139,250,0.18); }

.slider-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.7rem 1.25rem 0.85rem;
    border-top: 1.5px solid rgba(108,92,231,0.05) !important; gap: 8px;
}
html[data-admin-theme="dark"] .slider-card-footer { border-top-color: rgba(255,255,255,0.04) !important; }

.slider-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 5px; padding: 6px 13px; border-radius: 30px;
    font-size: 0.73rem; font-weight: 700; border: none;
    cursor: pointer; text-decoration: none !important; transition: all 0.2s ease;
}
.slider-action-btn.btn-edit  { background: rgba(108,92,231,0.08); color: #6c5ce7; }
.slider-action-btn.btn-edit:hover { background: #6c5ce7; color: #fff; }
html[data-admin-theme="dark"] .slider-action-btn.btn-edit { background: rgba(167,139,250,0.12); color: #a78bfa; }
html[data-admin-theme="dark"] .slider-action-btn.btn-edit:hover { background: #a78bfa; color: #1e293b; }
.slider-action-btn.btn-toggle-on  { background: rgba(16,185,129,0.08); color: #10b981; }
.slider-action-btn.btn-toggle-on:hover  { background: #10b981; color: #fff; }
.slider-action-btn.btn-toggle-off { background: rgba(100,116,139,0.08); color: #64748b; }
.slider-action-btn.btn-toggle-off:hover { background: #64748b; color: #fff; }
.slider-action-btn.btn-delete {
    background: rgba(239,68,68,0.08); color: #ef4444;
    width: 30px; height: 30px; padding: 0; border-radius: 50%;
}
.slider-action-btn.btn-delete:hover { background: #ef4444; color: #fff; }

/* Empty state */
.slider-empty { text-align: center; padding: 3rem 2rem; grid-column: 1 / -1; }

/* Section wrap spacing */
.slider-type-section { margin-bottom: 3rem; }

@media (max-width: 767px) {
    .btn-curved { padding: 6px 16px !important; font-size: 0.78rem !important; }
    .slider-grid { grid-template-columns: 1fr; gap: 1rem; }
    .slider-section-title { font-size: 0.9rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 40px;">

    {{-- Breadcrumb + Add Button --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Home Sliders</li>
        </ol>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-curved d-inline-flex align-items-center">
            <i class="bi bi-plus-circle-fill" style="font-size:0.9rem; margin-right:6px;"></i> Add New Slider
        </a>
    </nav>

    <div class="mb-4">
        <h2 class="h3 mb-1 text-theme-dark-bold fw-bold" style="font-family:'Outfit',sans-serif;">Home Sliders Directory</h2>
        <p class="text-muted mb-0 small">Manage storefront banners — 3 independent zones on the homepage.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4"
         style="border-radius:12px;background:rgba(16,185,129,0.12);color:#10b981;padding:0.75rem 1rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span class="fw-bold" style="font-size:0.82rem;">{{ session('success') }}</span>
            </div>
            <button type="button" class="close p-0" data-dismiss="alert" style="color:#10b981;opacity:0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    @php
        function sliderCards($sliders) { return view('admin.sliders._card_loop', compact('sliders'))->render(); }
    @endphp

    {{-- ── SECTION 1: Main Hero Sliders ─────────────────────────────── --}}
    <div class="slider-type-section">
        <div class="slider-section-header">
            <div class="slider-section-title text-theme-dark-bold">
                <span class="type-pill-main slider-type-pill"><i class="bi bi-display me-1"></i> Main Hero</span>
                Full-width carousel at the very top of the homepage
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="size-hint">Desktop: 1920×1080 · Mobile: 800×1200</span>
                <a href="{{ route('admin.sliders.create') }}?type=slider" class="btn-curved btn btn-sm">
                    <i class="bi bi-plus" style="margin-right:4px;"></i> Add
                </a>
            </div>
        </div>

        <div class="slider-grid">
            @forelse($mainSliders as $slider)
                @include('admin.sliders._card', ['slider' => $slider])
            @empty
                <div class="slider-empty card theme-card-bg border-0" style="border-radius:18px;">
                    <i data-feather="monitor" style="width:44px;height:44px;stroke-width:1;color:#6c5ce7;opacity:0.5;margin:0 auto 12px;" ></i>
                    <p class="text-muted small mb-0">No main sliders yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── SECTION 2: Sub-Slider 1 (After New Arrivals) ────────────── --}}
    <div class="slider-type-section">
        <div class="slider-section-header">
            <div class="slider-section-title text-theme-dark-bold">
                <span class="type-pill-sub1 slider-type-pill"><i class="bi bi-layout-text-window me-1"></i> Sub Banner 1</span>
                Appears after the <strong>New Arrivals</strong> section
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="size-hint">Desktop: 1920×600 · Mobile: 800×800</span>
                <a href="{{ route('admin.sliders.create') }}?type=slider_mid" class="btn-curved btn btn-sm">
                    <i class="bi bi-plus" style="margin-right:4px;"></i> Add
                </a>
            </div>
        </div>

        <div class="slider-grid">
            @forelse($subSliders1 as $slider)
                @include('admin.sliders._card', ['slider' => $slider])
            @empty
                <div class="slider-empty card theme-card-bg border-0" style="border-radius:18px;">
                    <i data-feather="image" style="width:44px;height:44px;stroke-width:1;color:#10b981;opacity:0.5;margin:0 auto 12px;"></i>
                    <p class="text-muted small mb-0">No sub-banner 1 sliders yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── SECTION 3: Sub-Slider 2 (After Recently Viewed) ─────────── --}}
    <div class="slider-type-section">
        <div class="slider-section-header">
            <div class="slider-section-title text-theme-dark-bold">
                <span class="type-pill-sub2 slider-type-pill"><i class="bi bi-layout-text-sidebar me-1"></i> Sub Banner 2</span>
                Appears after the <strong>Recently Viewed</strong> section
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="size-hint">Desktop: 1920×600 · Mobile: 800×800</span>
                <a href="{{ route('admin.sliders.create') }}?type=slider_top" class="btn-curved btn btn-sm">
                    <i class="bi bi-plus" style="margin-right:4px;"></i> Add
                </a>
            </div>
        </div>

        <div class="slider-grid">
            @forelse($subSliders2 as $slider)
                @include('admin.sliders._card', ['slider' => $slider])
            @empty
                <div class="slider-empty card theme-card-bg border-0" style="border-radius:18px;">
                    <i data-feather="image" style="width:44px;height:44px;stroke-width:1;color:#f59e0b;opacity:0.5;margin:0 auto 12px;"></i>
                    <p class="text-muted small mb-0">No sub-banner 2 sliders yet.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
