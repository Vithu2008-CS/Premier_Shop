@extends('layouts.admin')
@section('title', 'Create Coupon — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Create Coupon</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<div class="admin-card">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Coupon Code *</label>
                <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="e.g. SPRING20">
                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (£)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Value *</label>
                <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}" step="0.01" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Min Order Amount (£)</label>
                <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0" value="{{ old('min_order_amount') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Valid From</label>
                <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Valid Until</label>
                <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Usage Limit</label>
                <input type="number" name="usage_limit" class="form-control" min="1" value="{{ old('usage_limit') }}" placeholder="Unlimited">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-admin btn-lg"><i class="bi bi-plus-lg me-1"></i> Create Coupon</button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-admin-outline btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
