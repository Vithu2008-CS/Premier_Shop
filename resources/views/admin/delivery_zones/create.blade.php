{{--
    admin/delivery_zones/create.blade.php — Create delivery zone form
    ===================================================================
    Distance band + pricing rule with a live preview and floating save bar.
    POST → admin.delivery-zones.store → DeliveryZoneController::store()
--}}
@extends('layouts.admin_noble')
@section('title', 'Add Delivery Zone')

@push('styles')
@include('admin.delivery_zones._styles')
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.delivery-zones.index') }}">Delivery Zones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Zone</li>
        </ol>
        <a href="{{ route('admin.delivery-zones.index') }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;">
            <i class="bi bi-arrow-left" style="font-size:0.85rem;margin-right:6px;"></i> Back to Zones
        </a>
    </nav>

    <form action="{{ route('admin.delivery-zones.store') }}" method="POST" id="zone-create-form" class="row">
        @csrf
        <div class="col-lg-8 mb-4">
            @include('admin.delivery_zones._form')
        </div>
    </form>
</div>

{{-- Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center" style="font-family:'Outfit',sans-serif;gap:8px;">
        <span class="pulse-green"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.68rem;letter-spacing:0.5px;font-weight:600;white-space:nowrap;">New Zone</span>
    </div>
    <div class="button-group">
        <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-outline-light">Cancel</a>
        <button type="submit" form="zone-create-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Save Zone
        </button>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.delivery_zones._form_script')
@endpush
