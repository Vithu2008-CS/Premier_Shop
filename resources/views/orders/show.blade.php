@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' - Premier Shop')

@section('content')
<div class="container section-padding">
    <div class="mb-4 reveal-3d">
        <a href="{{ route('orders.index') }}" class="btn btn-link text-decoration-none text-primary ps-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="row g-5">
        {{-- Left Column: Order Items & Details --}}
        <div class="col-lg-8 reveal-slide-left">
            <div class="card order-card shadow-sm border-0">
                <div class="order-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-receipt text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">{{ $order->order_number }}</h4>
                            <p class="text-muted small mb-0">Placed on {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @php
                        $statusIcons = [
                            'pending' => 'bi-clock-history',
                            'processing' => 'bi-gear-wide-connected',
                            'shipped' => 'bi-truck',
                            'delivered' => 'bi-check2-circle',
                            'cancelled' => 'bi-x-circle'
                        ];
                    @endphp
                    <span class="status-badge status-{{ $order->status }}">
                        <i class="bi {{ $statusIcons[$order->status] ?? 'bi-info-circle' }}"></i>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Order Items</h5>
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center justify-content-between border-bottom py-3 hover-link">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->product->first_image }}" alt="" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px;" 
                                 class="shadow-sm" onerror="this.onerror=null; this.src='/images/placeholder-product.png'">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                <p class="text-muted small mb-0">Quantity: {{ $item->quantity }} × £{{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark fs-5">£{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 pt-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('orders.print', $order) }}" class="btn btn-outline-dark rounded-pill px-4 hover-up shadow-sm">
                            <i class="bi bi-printer me-2"></i>Download PDF Invoice
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-6 col-lg-5">
                    <div class="h-100">
                        <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-1">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>Delivery To
                        </h6>
                        <div class="p-3 bg-light rounded-4 border">
                            <p class="mb-1 fw-bold small">{{ auth()->user()->name }}</p>
                            <p class="mb-0 text-muted small">{{ $order->shipping_address['address_line'] ?? 'N/A' }}</p>
                            <p class="mb-0 text-muted small">{{ $order->shipping_address['city'] ?? '' }}</p>
                            <p class="mt-2 mb-0 small text-primary"><i class="bi bi-telephone-fill me-1"></i> {{ $order->shipping_address['phone'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="h-100">
                        <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-1">
                            <i class="bi bi-receipt-cutoff text-primary me-2"></i>Summary
                        </h6>
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-bold small">£{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="small">Discount</span>
                                <span class="fw-bold small">-£{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Shipping</span>
                                <span class="fw-bold small">£{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-1">
                                <span class="fw-bold text-primary">Total Paid</span>
                                <span class="fw-bold text-primary">£{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Tracking & Actions --}}
        <div class="col-lg-4 reveal-slide-right">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="bi bi-truck text-primary me-2"></i> Order Tracking
                    </h5>
                    
                    <div class="timeline-enhanced mt-3">
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title">Order Placed</span>
                                <span class="timeline-date">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->processing_date ? 'completed' : ($order->status == 'processing' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->processing_date || $order->status == 'processing' ? '' : 'text-muted' }}">Processing</span>
                                @if($order->processing_date)
                                    <span class="timeline-date">{{ $order->processing_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->shipped_date ? 'completed' : ($order->status == 'shipped' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->shipped_date || $order->status == 'shipped' ? '' : 'text-muted' }}">Shipped</span>
                                @if($order->shipped_date)
                                    <span class="timeline-date">{{ $order->shipped_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->delivered_date ? 'completed' : ($order->status == 'delivered' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->delivered_date || $order->status == 'delivered' ? 'text-success' : 'text-muted' }}">Delivered</span>
                                @if($order->delivered_date)
                                    <span class="timeline-date">{{ $order->delivered_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->status === 'pending')
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10" style="border-radius:20px; border: 1px dashed rgba(220, 53, 69, 0.3) !important;">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold text-danger mb-2">Need to change your mind?</h6>
                    <p class="small text-muted mb-3">You can cancel your order while it is still in pending status.</p>
                    <button type="button" class="btn btn-danger w-100 rounded-pill py-2 shadow-sm hover-up" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        Cancel My Order
                    </button>
                </div>
            </div>

            <!-- Cancel Order Modal -->
            <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                        <div class="modal-header border-0 p-4 pb-0">
                            <h5 class="modal-title fw-bold">Cancel Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body p-4">
                                <p class="text-muted">Are you sure you want to cancel order <strong>{{ $order->order_number }}</strong>? This action cannot be undone and your items will be returned to stock.</p>
                                <div class="mt-4">
                                    <label class="form-label fw-bold">Reason for cancellation <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control border-0 bg-light p-3" rows="4" required placeholder="Please let us know how we can improve..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep My Order</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm hover-up">Confirm Cancellation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled' && $order->cancellation_reason)
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 border-danger border-start-4" style="border-radius:12px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-danger mb-1 small"><i class="bi bi-info-circle me-1"></i> Cancellation Note:</h6>
                    <p class="small mb-0 fst-italic text-muted">"{{ $order->cancellation_reason }}"</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection