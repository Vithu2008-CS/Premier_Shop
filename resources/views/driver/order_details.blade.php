@extends('layouts.driver')
@section('title', 'Order Details — Driver')

@section('content')
<div class="container py-5">
    <div class="mb-4 reveal-3d">
        <a href="{{ route('driver.dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
        <h2 class="fw-bold mt-2">Order #{{ $order->order_number }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 reveal-slide-left">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-4">Customer Details</h5>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.2rem;">
                        {{ substr($order->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $order->user->name }}</h6>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-2">Delivery Address</h6>
                <div class="p-3 bg-light rounded-3 mb-4">
                    <p class="mb-0" style="line-height:1.6;">
                        {{ $order->shipping_address['address_line'] ?? '' }}<br>
                        {{ $order->shipping_address['city'] ?? '' }}<br>
                        <i class="bi bi-telephone me-1 mt-2"></i> {{ $order->shipping_address['phone'] ?? 'No phone' }}
                    </p>
                </div>

                <h5 class="fw-bold mb-4">Order Items</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">£{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Grand Total</th>
                                <th class="text-end fw-bold fs-5">£{{ number_format($order->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 reveal-slide-right">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3">Update Order Status</h5>
                <p class="text-muted small mb-4">Upload a proof of delivery to mark this order as delivered.</p>

                <form action="{{ route('driver.orders.complete', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Delivery Proof (Photo)</label>
                        <div class="input-group">
                            <input type="file" name="delivery_proof" class="form-control" accept="image/*" required>
                        </div>
                        <div class="form-text small">Take a photo of the delivered package.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold tilt-3d" 
                        {{ $order->status === 'delivered' ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-2"></i>
                        {{ $order->status === 'delivered' ? 'Already Delivered' : 'Mark as Delivered' }}
                    </button>
                </form>
            </div>

            @if($order->status === 'delivered' && $order->delivery_proof)
                <div class="card border-0 shadow-sm rounded-4 p-4 reveal-3d">
                    <h5 class="fw-bold mb-3">Delivery Proof</h5>
                    <img src="{{ asset('storage/' . $order->delivery_proof) }}" class="img-fluid rounded-3 shadow-sm" alt="Delivery Proof">
                    <div class="mt-2 small text-muted">
                        Delivered on: {{ $order->delivered_date->format('M d, Y H:i') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
