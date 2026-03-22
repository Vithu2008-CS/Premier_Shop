@extends('layouts.admin_noble')
@section('title', 'Order ' . $order->order_number)

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Order: <span class="text-primary">#{{ $order->order_number }}</span></h4>
  </div>
  <div class="d-flex align-items-center flex-wrap text-nowrap">
    <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-outline-primary btn-icon-text mr-2 mb-2 mb-md-0">
      <i class="btn-icon-prepend" data-feather="printer"></i>
      Download PDF
    </a>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
      <i class="btn-icon-prepend" data-feather="arrow-left"></i>
      Back to List
    </a>
  </div>
</div>

<div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="wd-35 h-35 rounded bg-light-primary d-flex align-items-center justify-content-center mr-2 text-primary">
                                            <i data-feather="package" class="icon-sm"></i>
                                        </div>
                                        <span class="font-weight-bold">{{ $item->product->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">£{{ number_format($item->price, 2) }}</td>
                                <td class="text-right font-weight-bold">£{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 justify-content-end">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>£{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success font-weight-bold">
                            <span>Discount ({{ $order->coupon_code }}):</span>
                            <span>-£{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping:</span>
                            <span>£{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h4 class="font-weight-bold">Total:</h4>
                            <h4 class="text-primary font-weight-bold">£{{ number_format($order->total, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Customer Info --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Customer Information</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="wd-45 h-45 rounded-circle bg-light-info d-flex align-items-center justify-content-center mr-3 text-info font-weight-bold" style="font-size: 1rem;">
                        {{ substr($order->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $order->user->name }}</h6>
                        <p class="text-muted small">{{ $order->user->email }}</p>
                    </div>
                </div>
                @if($order->user->phone)
                <p class="text-muted small mb-0"><i data-feather="phone" class="icon-xs mr-2"></i>{{ $order->user->phone }}</p>
                @endif
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Shipping Details</h6>
                @if(isset($order->shipping_address['address_line']))
                    <p class="text-muted small mb-2">{{ $order->shipping_address['address_line'] }}</p>
                    <p class="text-muted small mb-3">{{ $order->shipping_address['city'] ?? '' }}</p>
                    @if(isset($order->shipping_address['phone']))
                        <p class="text-muted small mb-0 font-weight-bold">
                            <i data-feather="truck" class="icon-xs mr-2 text-primary"></i> 
                            {{ $order->shipping_address['phone'] }}
                        </p>
                    @endif
                @else
                    <p class="text-muted small italic">No shipping address recorded</p>
                @endif
            </div>
        </div>

        {{-- Status Update --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Update Status & Tracking</h6>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Current Status</label>
                        <select name="status" class="form-control">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Processing Date</label>
                        <input type="datetime-local" name="processing_date" class="form-control" value="{{ $order->processing_date ? $order->processing_date->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Shipped Date</label>
                        <input type="datetime-local" name="shipped_date" class="form-control" value="{{ $order->shipped_date ? $order->shipped_date->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Delivered Date</label>
                        <input type="datetime-local" name="delivered_date" class="form-control" value="{{ $order->delivered_date ? $order->delivered_date->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3">
                        <i data-feather="refresh-ccw" class="icon-sm mr-2"></i> Update Order
                    </button>
                </form>
            </div>
        </div>

        {{-- Delivery Proof --}}
        @if($order->status === 'delivered' && $order->delivery_proof)
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Delivery Proof</h6>
                    <img src="{{ (str_starts_with($order->delivery_proof, 'data:image') || str_starts_with($order->delivery_proof, 'http')) ? $order->delivery_proof : asset('storage/' . $order->delivery_proof) }}" class="img-fluid rounded border shadow-sm" alt="Delivery Proof">
                    <p class="text-muted small mt-2 text-center italic">
                        Delivered: {{ $order->delivered_date ? $order->delivered_date->format('M d, Y H:i') : 'N/A' }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Driver Assignment --}}
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Driver Assignment</h6>
                @if($order->driver)
                    <div class="d-flex align-items-center mb-4 p-2 rounded bg-light">
                        <div class="wd-40 h-40 rounded bg-warning d-flex align-items-center justify-content-center mr-3 text-white font-weight-bold">
                            {{ substr($order->driver->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->driver->name }}</h6>
                            <span class="text-success small">On Duty</span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border mb-4">
                        <p class="text-muted small mb-0">No driver assigned yet.</p>
                    </div>
                @endif

                <form action="{{ route('admin.orders.assignDriver', $order) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="driver_id" class="form-control">
                            <option value="">Select Driver...</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ $order->driver_id == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }} ({{ $driver->assigned_orders_count ?? 0 }} active)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-block mt-2">
                        <i data-feather="user-plus" class="icon-sm mr-2"></i> Assign Driver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection