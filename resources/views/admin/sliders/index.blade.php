{{--
    admin/sliders/index.blade.php — Slider management card grid
    Variable: $sliders (Promotion records, type='slider', ordered by order_priority)
--}}
@extends('layouts.admin_noble')
@section('title', 'Sliders')

@push('styles')
<style>
/* ── Slider Card Grid ──────────────────────────────────────────── */
.slider-stats-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.slider-stat-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color, #e8e8f7);
    background: var(--card-bg, #fff);
    font-size: 0.85rem;
    font-weight: 600;
}
.slider-stat-pill .stat-num {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1;
}
.slider-stat-pill.stat-total  { color: #6c5ce7; }
.slider-stat-pill.stat-active { color: #00b894; }
.slider-stat-pill.stat-off    { color: #b2bec3; }

.slider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.slider-card {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color, #e8e8f7);
    background: var(--card-bg, #fff);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
}
.slider-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(108,92,231,0.12);
}

/* 16:9 thumbnail */
.slider-card-thumb {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background: #1a1a2e center/cover no-repeat;
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
    transform: scale(1.04);
}
.slider-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 50%);
    pointer-events: none;
}

.slider-card-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    gap: 6px;
    z-index: 2;
}
.slider-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    backdrop-filter: blur(8px);
}
.slider-badge.active   { background: rgba(0,184,148,0.85); color: #fff; }
.slider-badge.inactive { background: rgba(100,100,120,0.75); color: #fff; }
.slider-badge.priority { background: rgba(253,203,110,0.85); color: #2d3436; }

.slider-align-tag {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    width: 28px; height: 28px;
    border-radius: 8px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 0.8rem;
}

.slider-card-body {
    padding: 1rem 1.1rem 0.75rem;
}
.slider-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.slider-card-subtitle {
    font-size: 0.78rem;
    opacity: 0.55;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 10px;
    min-height: 1.1rem;
}

.slider-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 1.1rem 0.8rem;
    border-top: 1px solid var(--border-color, #e8e8f7);
    gap: 6px;
}
.slider-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}
.slider-action-btn.btn-edit    { background: rgba(108,92,231,0.1); color: #6c5ce7; }
.slider-action-btn.btn-edit:hover { background: #6c5ce7; color: #fff; }
.slider-action-btn.btn-toggle-on  { background: rgba(0,184,148,0.1); color: #00b894; }
.slider-action-btn.btn-toggle-on:hover { background: #00b894; color: #fff; }
.slider-action-btn.btn-toggle-off { background: rgba(100,100,120,0.1); color: #636e72; }
.slider-action-btn.btn-toggle-off:hover { background: #636e72; color: #fff; }
.slider-action-btn.btn-delete  { background: rgba(214,48,49,0.08); color: #d63031; margin-left: auto; }
.slider-action-btn.btn-delete:hover { background: #d63031; color: #fff; }

.slider-link-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    color: #6c5ce7;
    opacity: 0.7;
    text-decoration: none;
    padding: 2px 0;
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.slider-link-chip:hover { opacity: 1; }

.slider-empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    opacity: 0.5;
}
</style>
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
        <li class="breadcrumb-item active" aria-current="page">Home Sliders</li>
    </ol>
</nav>

@php
    $total    = $sliders->count();
    $active   = $sliders->where('is_active', true)->count();
    $inactive = $total - $active;
@endphp

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="font-family:'Outfit',sans-serif;">Home Sliders</h4>
        <p class="text-muted small mb-0">Manage the full-width hero carousel on your storefront.</p>
    </div>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i data-feather="plus" style="width:16px;height:16px;"></i> New Slider
    </a>
</div>

{{-- Stats bar --}}
<div class="slider-stats-bar">
    <div class="slider-stat-pill stat-total">
        <span class="stat-num">{{ $total }}</span>
        <span>Total Slides</span>
    </div>
    <div class="slider-stat-pill stat-active">
        <span class="stat-num">{{ $active }}</span>
        <span>Active</span>
    </div>
    <div class="slider-stat-pill stat-off">
        <span class="stat-num">{{ $inactive }}</span>
        <span>Inactive</span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i data-feather="check-circle" class="me-2" style="width:16px;height:16px;"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Card Grid --}}
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
        <div class="slider-card">
            {{-- Thumbnail --}}
            <div class="slider-card-thumb">
                <img src="{{ $imgSrc }}" alt="{{ $slider->title }}" class="slider-card-thumb-img" loading="lazy">
                <div class="slider-card-overlay"></div>

                {{-- Badges --}}
                <div class="slider-card-badges">
                    <span class="slider-badge {{ $slider->is_active ? 'active' : 'inactive' }}">
                        {{ $slider->is_active ? 'Live' : 'Off' }}
                    </span>
                    <span class="slider-badge priority">
                        #{{ $slider->order_priority }}
                    </span>
                </div>

                {{-- Align icon --}}
                <div class="slider-align-tag" title="Text align: {{ $slider->text_align ?? 'center' }}">
                    <i data-feather="{{ $alignIcon }}" style="width:13px;height:13px;"></i>
                </div>
            </div>

            {{-- Body --}}
            <div class="slider-card-body">
                <div class="slider-card-title">{{ $slider->title ?? 'Untitled Slide' }}</div>
                <div class="slider-card-subtitle">{{ $slider->subtitle ?: '—' }}</div>
                @if($slider->link_url)
                    <a href="{{ $slider->link_url }}" target="_blank" rel="noopener" class="slider-link-chip">
                        <i data-feather="link" style="width:11px;height:11px;flex-shrink:0;"></i>
                        {{ $slider->link_url }}
                    </a>
                @endif
            </div>

            {{-- Footer actions --}}
            <div class="slider-card-footer">
                <a href="{{ route('admin.sliders.edit', $slider) }}" class="slider-action-btn btn-edit">
                    <i data-feather="edit-2" style="width:12px;height:12px;"></i> Edit
                </a>

                <form action="{{ route('admin.sliders.toggle-active', $slider) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="slider-action-btn {{ $slider->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                        <i data-feather="{{ $slider->is_active ? 'eye' : 'eye-off' }}" style="width:12px;height:12px;"></i>
                        {{ $slider->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>

                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
                      onsubmit="return confirm('Delete this slider? This cannot be undone.');" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="slider-action-btn btn-delete">
                        <i data-feather="trash-2" style="width:12px;height:12px;"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="slider-empty-state">
            <i data-feather="image" style="width:48px;height:48px;stroke-width:1;"></i>
            <p class="mt-3 mb-1 fw-semibold">No sliders yet</p>
            <small>Create your first homepage banner to get started.</small><br>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm mt-3">
                <i data-feather="plus" style="width:14px;height:14px;"></i> Add Slider
            </a>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush

@endsection
