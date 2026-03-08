@extends('layouts.admin')
@section('title', 'Edit Coupon — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Edit Coupon</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                <li class="breadcrumb-item active">{{ $coupon->code }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="admin-card">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Coupon Code *</label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $coupon->code) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="percentage" {{ $coupon->discount_type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ $coupon->discount_type === 'fixed' ? 'selected' : '' }}>Fixed Amount (£)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Discount Value *</label>
                <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Min Order Amount (£)</label>
                <input type="number" name="min_order_amount" class="form-control" step="0.01" value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Valid From</label>
                <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from', $coupon->valid_from?->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Valid Until</label>
                <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until', $coupon->valid_until?->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Usage Limit</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-admin btn-lg"><i class="bi bi-save me-1"></i> Update Coupon</button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-admin-outline btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
