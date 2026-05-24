{{--
    admin/shipping_rates/index.blade.php — Shipping Rates configuration
    ====================================================================
    Fields: base_connection_fee, per_mile_rate, per_kg_surcharge
    PUT → admin.shipping-rates.update → ShippingRateController::update()
--}}
@extends('layouts.admin_noble')
@section('title', 'Shipping Rates Configuration')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Shipping Rates</li>
  </ol>
</nav>

<div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-primary">
                            <i data-feather="truck" class="me-2 icon-md align-middle text-primary"></i>
                            Shipping Rate Computation Engine
                        </h4>
                        <p class="text-muted small">Configure global weight- and distance-based delivery rate variables.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                        <i data-feather="check-circle" class="icon-sm me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert" style="border-radius: 10px;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.shipping-rates.update') }}" method="POST" class="forms-sample">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="base_connection_fee" class="form-label fw-bold text-muted small px-1">
                                BASE CONNECTION FEE (£) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">£</span>
                                <input type="number" step="0.01" min="0.01" name="base_connection_fee" id="base_connection_fee" 
                                       class="form-control form-control-lg @error('base_connection_fee') is-invalid @enderror" 
                                       value="{{ old('base_connection_fee', $rates->base_connection_fee) }}" required>
                            </div>
                            <small class="text-muted d-block mt-2">The flat starting fee applied to every delivery regardless of weight or mileage.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="per_mile_rate" class="form-label fw-bold text-muted small px-1">
                                PER-MILE ROAD RATE (£) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">£</span>
                                <input type="number" step="0.01" min="0.01" name="per_mile_rate" id="per_mile_rate" 
                                       class="form-control form-control-lg @error('per_mile_rate') is-invalid @enderror" 
                                       value="{{ old('per_mile_rate', $rates->per_mile_rate) }}" required>
                            </div>
                            <small class="text-muted d-block mt-2">The charge applied per mile of road distance computed via Google Maps.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="per_kg_surcharge" class="form-label fw-bold text-muted small px-1">
                            PER-KG WEIGHT SURCHARGE (£) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">£</span>
                            <input type="number" step="0.01" min="0.01" name="per_kg_surcharge" id="per_kg_surcharge" 
                                   class="form-control form-control-lg @error('per_kg_surcharge') is-invalid @enderror" 
                                   value="{{ old('per_kg_surcharge', $rates->per_kg_surcharge) }}" required>
                        </div>
                        <small class="text-muted d-block mt-2">Extra charge added for every kilogram of total package weight in the cart.</small>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-lg btn-outline-secondary px-4 rounded-pill">Cancel</a>
                        <button type="submit" class="btn btn-lg btn-primary px-4 rounded-pill">
                            <i data-feather="save" class="icon-sm me-2"></i> Update Global Rates
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, rgba(108, 92, 231, 0.1) 0%, rgba(224, 86, 253, 0.1) 100%);">
            <div class="card-body p-4 text-center">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-white shadow-sm text-primary rounded-circle" style="width: 60px; height: 60px;">
                    <i data-feather="info" class="icon-md"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">Mathematical Engine Formula</h5>
                <p class="text-muted small mb-4 line-height-md">
                    The total shipping cost is computed dynamically in real-time as follows:
                </p>
                <div class="bg-white p-3 rounded-4 shadow-sm border border-light text-start mb-3">
                    <code class="d-block text-primary fw-bold text-center fs-6 py-2">
                        Shipping = Base Fee + (Miles × Mile Rate) + (Weight × Kg Surcharge)
                    </code>
                </div>
                <small class="text-muted d-block mt-2 fst-italic">
                    All parameters must be positive numbers greater than 0 to guarantee profitable operations.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
