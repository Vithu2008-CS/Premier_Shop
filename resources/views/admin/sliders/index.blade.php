{{--
    admin/sliders/index.blade.php — Redesigned modern slider management card grid
    Variable: $sliders (Promotion records, type='slider', ordered by order_priority)
--}}
@extends('layouts.admin_noble')
@section('title', 'Home Sliders Management')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
.rounded-4 { border-radius: 18px !important; }
.rounded-3 { border-radius: 12px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Soft badges */
.bg-soft-primary   { background: rgba(108,92,231,0.1) !important; color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-danger    { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
html[data-admin-theme="dark"] .bg-soft-primary   { background: rgba(167,139,250,0.15) !important; color: #a78bfa !important; }
html[data-admin-theme="dark"] .bg-soft-success   { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148,163,184,0.15) !important; color: #94a3b8 !important; }
html[data-admin-theme="dark"] .bg-soft-warning   { background: rgba(251,191,36,0.15) !important; color: #fbbf24 !important; }
html[data-admin-theme="dark"] .bg-soft-danger    { background: rgba(248,113,113,0.15) !important; color: #f87171 !important; }

/* Curved Buttons */
.btn-curved {
    border-radius: 30px !important;
    padding: 8px 22px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
    color: #fff !important;
    transition: all 0.25s ease !important;
}
.btn-curved:hover { transform: translateY(-1px) !important; box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }

/* stats bar cards */
.slider-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}
.slider-stat-card {
    border-radius: 18px !important;
    padding: 18px 24px !important;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important;
    transition: transform 0.2s ease;
}
.slider-stat-card:hover {
    transform: translateY(-2px);
}
.slider-stat-card .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.slider-stat-card .stat-num {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1.1;
    font-family: 'Outfit', sans-serif;
}
.slider-stat-card .stat-label {
    font-size: 0.76rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}
html[data-admin-theme="dark"] .slider-stat-card .stat-label {
    color: #94a3b8;
}

/* Card Grid styling */
.slider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}
.slider-card {
    border-radius: 18px !important;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: none !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
}
.slider-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(108,92,231,0.1) !important;
}
html[data-admin-theme="dark"] .slider-card {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
}
html[data-admin-theme="dark"] .slider-card:hover {
    box-shadow: 0 12px 30px rgba(167,139,250,0.15) !important;
}

.slider-card-thumb {
    position: relative;
    width: 100%;
    padding-top: 52%;
    background: #1e293b center/cover no-repeat;
    overflow: hidden;
}
.slider-card-thumb-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.slider-card:hover .slider-card-thumb-img {
    transform: scale(1.05);
}
.slider-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,0.7) 0%, rgba(15,23,42,0) 60%);
    pointer-events: none;
}
.slider-card-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    display: flex;
    gap: 6px;
    z-index: 2;
}
.slider-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.slider-badge.active   { background: rgba(16,185,129,0.85); color: #fff; }
.slider-badge.inactive { background: rgba(100,116,139,0.8); color: #fff; }
.slider-badge.priority { background: rgba(245,158,11,0.85); color: #fff; }

.slider-align-tag {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.8rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.slider-card-body {
    padding: 1.25rem;
}
.slider-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.slider-card-subtitle {
    font-size: 0.8rem;
    opacity: 0.6;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 12px;
    min-height: 1.2rem;
}
.slider-link-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.72rem;
    color: #6c5ce7;
    background: rgba(108,92,231,0.06);
    padding: 4px 10px;
    border-radius: 20px;
    text-decoration: none !important;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
    transition: all 0.2s ease;
}
.slider-link-chip:hover {
    background: rgba(108,92,231,0.12);
    color: #6c5ce7;
}
html[data-admin-theme="dark"] .slider-link-chip {
    color: #a78bfa;
    background: rgba(167,139,250,0.1);
}
html[data-admin-theme="dark"] .slider-link-chip:hover {
    background: rgba(167,139,250,0.18);
}

.slider-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.8rem 1.25rem 1rem;
    border-top: 1.5px solid rgba(108,92,231,0.05) !important;
    gap: 8px;
}
html[data-admin-theme="dark"] .slider-card-footer {
    border-top-color: rgba(255,255,255,0.04) !important;
}

.slider-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.slider-action-btn.btn-edit    { background: rgba(108,92,231,0.08); color: #6c5ce7; }
.slider-action-btn.btn-edit:hover { background: #6c5ce7; color: #fff; }
html[data-admin-theme="dark"] .slider-action-btn.btn-edit { background: rgba(167,139,250,0.12); color: #a78bfa; }
html[data-admin-theme="dark"] .slider-action-btn.btn-edit:hover { background: #a78bfa; color: #1e293b; }

.slider-action-btn.btn-toggle-on  { background: rgba(16,185,129,0.08); color: #10b981; }
.slider-action-btn.btn-toggle-on:hover { background: #10b981; color: #fff; }
.slider-action-btn.btn-toggle-off { background: rgba(100,116,139,0.08); color: #64748b; }
.slider-action-btn.btn-toggle-off:hover { background: #64748b; color: #fff; }

.slider-action-btn.btn-delete  { background: rgba(239,68,68,0.08); color: #ef4444; width: 32px; height: 32px; padding: 0; border-radius: 50%; }
.slider-action-btn.btn-delete:hover { background: #ef4444; color: #fff; }

.slider-empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 5rem 2rem;
}

@media (max-width: 767px) {
    .btn-curved { padding: 6px 18px !important; font-size: 0.78rem !important; }
    .slider-stats-grid { gap: 10px; margin-bottom: 1.5rem; }
    .slider-stat-card { padding: 12px 16px !important; gap: 12px; }
    .slider-stat-card .stat-icon { width: 36px; height: 36px; border-radius: 8px; }
    .slider-stat-card .stat-icon svg { width: 16px !important; height: 16px !important; }
    .slider-stat-card .stat-num { font-size: 1.2rem; }
    .slider-stat-card .stat-label { font-size: 0.68rem; }
    .slider-grid { grid-template-columns: 1fr; gap: 1rem; }
    .slider-card-body { padding: 1rem; }
    .slider-card-footer { padding: 0.8rem 1rem; }
    .slider-action-btn { padding: 6px 12px; font-size: 0.72rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 40px;">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Home Sliders</li>
        </ol>
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-curved d-inline-flex align-items-center justify-content-center">
            <i class="bi bi-plus-circle-fill mr-2" style="font-size:0.95rem; margin-right: 6px;"></i> Add New Slider
        </a>
    </nav>

    @php
        $total    = $sliders->count();
        $active   = $sliders->where('is_active', true)->count();
        $inactive = $total - $active;
    @endphp

    {{-- Page Title --}}
    <div class="mb-4">
        <h2 class="h3 mb-1 text-theme-dark-bold fw-bold" style="font-family:'Outfit',sans-serif;">Home Sliders Directory</h2>
        <p class="text-muted mb-0">Customize and manage your storefront's interactive full-width landing carousel sliders.</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert"
         style="border-radius:12px;background:rgba(16,185,129,0.12);color:#10b981;padding:0.75rem 1rem;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill mr-2" style="margin-right: 6px;"></i>
                <span class="font-weight-bold" style="font-size:0.82rem;">{{ session('success') }}</span>
            </div>
            <button type="button" class="close p-0" data-dismiss="alert" style="color:#10b981;opacity:0.8;line-height: 1;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Premium Stats Grid --}}
    <div class="slider-stats-grid">
        <div class="card slider-stat-card theme-card-bg">
            <div class="stat-icon bg-soft-primary">
                <i data-feather="image" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="stat-num text-theme-dark-bold">{{ $total }}</div>
                <div class="stat-label">Total Slides</div>
            </div>
        </div>
        <div class="card slider-stat-card theme-card-bg">
            <div class="stat-icon bg-soft-success">
                <i data-feather="eye" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="stat-num text-theme-dark-bold">{{ $active }}</div>
                <div class="stat-label">Live Slides</div>
            </div>
        </div>
        <div class="card slider-stat-card theme-card-bg">
            <div class="stat-icon bg-soft-secondary">
                <i data-feather="eye-off" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div class="stat-num text-theme-dark-bold">{{ $inactive }}</div>
                <div class="stat-label">Inactive</div>
            </div>
        </div>
    </div>

    {{-- Slider Cards Grid --}}
    <div class="slider-grid">
        @forelse($sliders as $slider)
            @php
                $imgSrc = str_starts_with($slider->image_path, 'http')
                    ? $slider->image_path
                    : asset('storage/' . $slider->image_path);
                $alignIcon = match($slider->text_align ?? 'center') {
                    'left'  => 'align-left',
                    'right' => 'align-right',
                    default => 'align-center',
                };
            @endphp
            <div class="card slider-card theme-card-bg">
                {{-- Slide Thumbnail --}}
                <div class="slider-card-thumb">
                    <img src="{{ $imgSrc }}" alt="{{ $slider->title }}" class="slider-card-thumb-img" loading="lazy">
                    <div class="slider-card-overlay"></div>

                    {{-- Badges --}}
                    <div class="slider-card-badges">
                        <span class="slider-badge {{ $slider->is_active ? 'active' : 'inactive' }}">
                            {{ $slider->is_active ? 'Live' : 'Off' }}
                        </span>
                        <span class="slider-badge priority">
                            #{{ $slider->order_priority }} Priority
                        </span>
                    </div>

                    {{-- Text Alignment Indicator --}}
                    <div class="slider-align-tag" title="Text alignment: {{ $slider->text_align ?? 'center' }}">
                        <i data-feather="{{ $alignIcon }}" style="width:14px;height:14px;"></i>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="slider-card-body">
                    <div class="slider-card-title text-theme-dark-bold">{{ $slider->title ?: 'Untitled Slide' }}</div>
                    <div class="slider-card-subtitle text-muted">{{ $slider->subtitle ?: '— No Subtitle —' }}</div>
                    @if($slider->link_url)
                        <a href="{{ $slider->link_url }}" target="_blank" rel="noopener" class="slider-link-chip">
                            <i data-feather="link" style="width:11px;height:11px;flex-shrink:0;"></i>
                            {{ $slider->link_url }}
                        </a>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="slider-card-footer">
                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="slider-action-btn btn-edit">
                        <i data-feather="edit-2" style="width:12px;height:12px;"></i> Edit
                    </a>

                    <form action="{{ route('admin.sliders.toggle-active', $slider) }}" method="POST" class="d-inline m-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="slider-action-btn {{ $slider->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                            <i data-feather="{{ $slider->is_active ? 'eye-off' : 'eye' }}" style="width:12px;height:12px;"></i>
                            {{ $slider->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
                          onsubmit="return confirm('Permanently delete this slider banner? This action is irreversible.');" class="d-inline m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="slider-action-btn btn-delete" title="Delete Slider">
                            <i data-feather="trash-2" style="width:12px;height:12px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="slider-empty-state card theme-card-bg py-5 border-0 rounded-4">
                <i data-feather="image" style="width:56px;height:56px;stroke-width:1;color:#6c5ce7;opacity:0.65;" class="mb-3"></i>
                <h5 class="fw-bold text-theme-dark-bold mb-1">No homepage sliders found</h5>
                <p class="text-muted small mb-4">Create dynamic sliders to display promo updates on the storefront main slider.</p>
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-curved">
                    <i class="bi bi-plus-circle-fill mr-2" style="font-size:0.95rem; margin-right: 6px;"></i> Create First Slider
                </a>
            </div>
        @endforelse
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
