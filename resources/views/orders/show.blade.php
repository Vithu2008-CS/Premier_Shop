@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' - Premier Shop')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">{{ $order->order_number }}</h4>
                        @php $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} fs-6">{{ ucfirst($order->status) }}</span>
                    </div>

                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <div>
                                <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                                <small class="text-muted">Qty: {{ $item->quantity }} × £{{ number_format($item->price, 2) }}</small>
                            </div>
                            <span class="fw-bold">£{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Order Details</h5>
                    <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                    <hr>
                    <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>£{{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-1 text-success"><span>Discount ({{ $order->coupon_code }})</span><span>-£{{ number_format($order->discount_amount, 2) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>£{{ number_format($order->shipping_cost, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span class="fw-bold fs-5">Total</span><span class="fw-bold fs-5 text-primary">£{{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Shipping Address</h5>
                    @if($order->shipping_address)
                        <p class="mb-0">{{ $order->shipping_address['address_line'] ?? '' }}<br>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['postcode'] ?? '' }}<br>{{ $order->shipping_address['phone'] ?? '' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
