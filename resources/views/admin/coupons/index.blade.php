@extends('layouts.admin_noble')
@section('title', 'Coupons')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Coupons</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Coupon Management</h6>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-icon-text">
                <i class="btn-icon-prepend" data-feather="plus-circle"></i>
                Create Coupon
            </a>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min Order</th>
                <th>Valid Until</th>
                <th>Usage</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($coupons as $coupon)
                <tr>
                  <td>
                    <code class="p-1 px-2 border rounded bg-light text-primary font-weight-bold">{{ $coupon->code }}</code>
                  </td>
                  <td>
                    <span class="badge badge-outline-info text-uppercase">{{ $coupon->discount_type }}</span>
                  </td>
                  <td class="font-weight-bold">
                    {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '£' . number_format($coupon->discount_value, 2) }}
                  </td>
                  <td class="small">
                    {{ $coupon->min_order_amount ? '£' . number_format($coupon->min_order_amount, 2) : 'No Min' }}
                  </td>
                  <td class="text-muted small">
                    {{ $coupon->valid_until ? $coupon->valid_until->format('d M Y') : 'Infinite' }}
                  </td>
                  <td>
                    <span class="text-muted small">{{ $coupon->times_used }}</span>
                    @if($coupon->usage_limit)
                        <span class="text-muted small"> / {{ $coupon->usage_limit }}</span>
                    @endif
                  </td>
                  <td>
                    @if($coupon->is_active)
                        <span class="badge badge-success">ACTIVE</span>
                    @else
                        <span class="badge badge-light text-muted">INACTIVE</span>
                    @endif
                  </td>
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropCoupon-{{ $coupon->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropCoupon-{{ $coupon->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.coupons.edit', $coupon) }}">
                              <i data-feather="edit-2" class="icon-sm mr-2"></i> Edit
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Truly delete this coupon?')">
                              @csrf @method('DELETE')
                              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                  <i data-feather="trash-2" class="icon-sm mr-2"></i> Delete
                              </button>
                          </form>
                        </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i data-feather="tag" class="icon-xxl text-muted mb-3"></i>
                        <p class="text-muted">No coupons available. Start by creating one!</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mt-3">Create First Coupon</a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
