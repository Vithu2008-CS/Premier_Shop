{{--
    admin/delivery_zones/index.blade.php — Delivery zone list
    ==========================================================
    Curved zone cards ordered by distance band. "Add Zone" button creates,
    clicking a zone opens its edit page, the round bin button deletes instantly.
    GET → admin.delivery-zones.index → DeliveryZoneController::index()
--}}
@extends('layouts.admin_noble')
@section('title', 'Delivery Zones')

@push('styles')
@include('admin.delivery_zones._styles')
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- Breadcrumb + Add Zone --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delivery Zones</li>
        </ol>
        <a href="{{ route('admin.delivery-zones.create') }}"
           class="btn btn-primary btn-sm d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';padding:8px 20px;background:linear-gradient(135deg,#743089,#a78bfa);border:none;box-shadow:0 4px 12px rgba(116, 48, 137,0.25);">
            <i class="bi bi-plus-circle" style="font-size:0.9rem;margin-right:6px;"></i> Add Zone
        </a>
    </nav>

    @if(session('success'))
        <div class="alert alert-success rounded-3" style="border-radius:12px !important;">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                    <i class="bi bi-geo-alt-fill text-primary" style="font-size:1.25rem;margin-right:8px;"></i>
                    Delivery Zones
                </h5>
                <span class="badge bg-soft-primary ml-3 zone-badge" style="margin-left:12px;">{{ $zones->count() }} {{ Str::plural('zone', $zones->count()) }}</span>
            </div>

            @forelse($zones as $zone)
                <div class="zone-row d-flex align-items-center justify-content-between flex-wrap p-3 mb-3 theme-card-bg"
                     data-href="{{ route('admin.delivery-zones.edit', $zone) }}" style="gap:12px;">

                    <div class="d-flex align-items-center" style="gap:14px;min-width:0;">
                        <div class="d-flex align-items-center justify-content-center bg-soft-primary"
                             style="width:42px;height:42px;border-radius:14px;flex-shrink:0;">
                            <i class="bi bi-geo-alt" style="font-size:1.1rem;"></i>
                        </div>
                        <div style="min-width:0;">
                            <div class="font-weight-bold text-theme-dark-bold" style="font-family:'Outfit';font-size:0.92rem;">{{ $zone->name }}</div>
                            <div class="text-muted small">{{ number_format($zone->min_miles, 1) }}–{{ number_format($zone->max_miles, 1) }} miles from store</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                        @if($zone->is_free)
                            <span class="zone-badge bg-soft-success">Fully Free</span>
                        @elseif($zone->free_over_amount !== null)
                            <span class="zone-badge bg-soft-success">Free over £{{ number_format($zone->free_over_amount, 2) }}</span>
                            <span class="zone-badge bg-soft-warning">£{{ number_format($zone->delivery_fee, 2) }} under</span>
                        @else
                            <span class="zone-badge bg-soft-warning">£{{ number_format($zone->delivery_fee, 2) }} flat</span>
                        @endif

                        <form action="{{ route('admin.delivery-zones.destroy', $zone) }}" method="POST" class="mb-0 zone-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-zone-delete p-0" title="Delete zone">
                                <i class="bi bi-trash" style="font-size:0.9rem;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary mb-3"
                         style="width:64px;height:64px;border-radius:20px;">
                        <i class="bi bi-geo-alt" style="font-size:1.6rem;"></i>
                    </div>
                    <p class="text-muted mb-1 font-weight-bold" style="font-family:'Outfit';">No delivery zones yet</p>
                    <p class="text-muted small mb-3">Customers are quoted the flat-rate fallback until you add a zone.</p>
                    <a href="{{ route('admin.delivery-zones.create') }}" class="btn btn-primary btn-sm"
                       style="border-radius:30px !important;font-weight:700;padding:8px 20px;background:linear-gradient(135deg,#743089,#a78bfa);border:none;">
                        <i class="bi bi-plus-circle" style="margin-right:6px;"></i> Add your first zone
                    </a>
                </div>
            @endforelse

            @if($zones->isNotEmpty())
                <div class="p-3 rounded-3 bg-soft-primary d-flex align-items-center mt-2" style="gap:8px;">
                    <i class="bi bi-info-circle" style="font-size:1rem;"></i>
                    <span class="small mb-0">Distance is the Google driving distance from the store. Addresses outside every band fall back to the flat rate, and the tightest zone wins where bands overlap.</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
$(function() {
    'use strict';

    // Whole zone card navigates to its edit page — except the delete button
    document.querySelectorAll('.zone-row[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.zone-delete-form')) return;
            window.location.href = row.dataset.href;
        });
    });
});
</script>
@endpush
