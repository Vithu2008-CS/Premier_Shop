@extends('layouts.admin_noble')
@section('title', 'Orders')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Orders</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Order Management</h6>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td>
                      <a href="{{ route('admin.orders.show', $order) }}" class="font-weight-bold text-primary">
                          {{ $order->order_number }}
                      </a>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                        <div class="wd-30 h-30 rounded-circle bg-light-primary d-flex align-items-center justify-content-center mr-2 text-primary font-weight-bold" style="font-size: 0.7rem;">
                            {{ substr($order->user->name, 0, 1) }}
                        </div>
                        {{ $order->user->name }}
                    </div>
                  </td>
                  <td class="font-weight-bold">£{{ number_format($order->total, 2) }}</td>
                  <td>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <select name="status" class="form-control form-control-sm border-0 bg-light wd-120" onchange="this.form.submit()">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </form>
                  </td>
                  <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                  <td class="text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-icon btn-sm" title="View Details">
                        <i data-feather="eye"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i data-feather="file-text" class="icon-xxl text-muted mb-3"></i>
                        <p class="text-muted">No orders found in the system.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
