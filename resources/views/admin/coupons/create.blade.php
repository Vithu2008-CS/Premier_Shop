@extends('layouts.admin_noble')
@section('title', 'Create Coupon')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Coupon Details</h6>
        
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="e.g. SAVE20">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Discount Type</label>
                    <select name="discount_type" class="form-control">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (£)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                    <input type="number" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value') }}" step="0.01" min="0" required>
                    @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Minimum Order Amount (£)</label>
                    <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" value="{{ old('min_order_amount') }}" placeholder="0.00">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Valid From</label>
                    <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from', now()->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Valid Until</label>
                    <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Usage Limit (per code)</label>
                    <input type="number" name="usage_limit" class="form-control" min="1" value="{{ old('usage_limit') }}" placeholder="Unlimited">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            Coupon is Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary mr-2">
                    <i data-feather="plus-square" class="icon-sm mr-2"></i> Create Coupon
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
