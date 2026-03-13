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
        <div class="col-lg-8 reveal-slide-left">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <h4 class="fw-bold mb-0">{{ $order->order_number }}</h4>
                            <a href="{{ route('orders.print', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-printer me-1"></i> Print Order
                            </a>
                        </div>
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

        <div class="col-lg-4 reveal-slide-right">
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
                    <h5 class="fw-bold mb-3">Order Tracking</h5>
                    <div class="tracking-timeline position-relative">
                        <div style="position:absolute;left:15px;top:10px;bottom:10px;width:2px;background:#e9ecef;"></div>
                        
                        <div class="d-flex mb-3 position-relative">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;z-index:2;margin-right:15px;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Order Placed</h6>
                                <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <div class="d-flex mb-3 position-relative">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $order->processing_date ? 'linear-gradient(135deg,#6C5CE7,#A29BFE)' : '#e9ecef' }};display:flex;align-items:center;justify-content:center;color:{{ $order->processing_date ? '#fff' : '#6c757d' }};z-index:2;margin-right:15px;">
                                <i class="bi bi-box"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold {{ $order->processing_date ? '' : 'text-muted' }}">Processing</h6>
                                @if($order->processing_date)
                                    <small class="text-muted">{{ $order->processing_date->format('d M Y, H:i') }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex mb-3 position-relative">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $order->shipped_date ? 'linear-gradient(135deg,#6C5CE7,#A29BFE)' : '#e9ecef' }};display:flex;align-items:center;justify-content:center;color:{{ $order->shipped_date ? '#fff' : '#6c757d' }};z-index:2;margin-right:15px;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold {{ $order->shipped_date ? '' : 'text-muted' }}">Shipped</h6>
                                @if($order->shipped_date)
                                    <small class="text-muted">{{ $order->shipped_date->format('d M Y, H:i') }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex position-relative">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $order->delivered_date ? 'linear-gradient(135deg,#00b894,#55efc4)' : '#e9ecef' }};display:flex;align-items:center;justify-content:center;color:{{ $order->delivered_date ? '#fff' : '#6c757d' }};z-index:2;margin-right:15px;">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold {{ $order->delivered_date ? 'text-success' : 'text-muted' }}">Delivered</h6>
                                @if($order->delivered_date)
                                    <small class="text-muted">{{ $order->delivered_date->format('d M Y, H:i') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Shipping Address</h5>
                    <p class="mb-0">{{ $order->shipping_address['address_line'] ?? '' }}<br>{{ $order->shipping_address['city'] ?? '' }}<br>{{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
            </div>

            @if($order->status === 'pending')
            <div class="mt-4">
                <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                    Cancel Order
                </button>
            </div>

            <!-- Cancel Order Modal -->
            <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Cancel Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <p class="text-muted mb-4">Are you sure you want to cancel this order? This action cannot be undone.</p>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Reason for cancellation <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control bg-light border-0" rows="3" required placeholder="Please let us know why you are cancelling..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Order</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">Confirm Cancellation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled' && $order->cancellation_reason)
            <div class="alert alert-danger mt-4 rounded-4 border-0">
                <i class="bi bi-x-circle-fill me-2"></i><strong>Cancellation Reason:</strong><br>
                {{ $order->cancellation_reason }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection