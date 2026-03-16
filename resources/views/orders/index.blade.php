@extends('layouts.app')
@section('title', 'My Orders - Premier Shop')

@section('content')
<div class="container section-padding">
    <div class="d-flex align-items-center mb-5 reveal-3d">
        <div>
            <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">My <span class="gradient-text">Orders</span></h2>
            <p class="text-muted mb-0">Track and manage your recent purchases</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm hover-up">
                <i class="bi bi-plus-lg me-2"></i>New Order
            </a>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row g-4 stagger-children">
            @foreach($orders as $order)
                <div class="col-12 fade-up">
                    <div class="order-card h-100">
                        <div class="order-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                                    <i class="bi bi-hash text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $order->order_number }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3 ms-md-auto">
                                <div class="text-md-end text-start order-1 order-md-0">
                                    <div class="text-muted small">Total Amount</div>
                                    <h5 class="fw-bold text-primary mb-0">£{{ number_format($order->total, 2) }}</h5>
                                </div>
                                
                                @php
                                    $statusIcons = [
                                        'pending' => 'bi-clock-history',
                                        'processing' => 'bi-gear-wide-connected',
                                        'shipped' => 'bi-truck',
                                        'delivered' => 'bi-check2-circle',
                                        'cancelled' => 'bi-x-circle'
                                    ];
                                    $statusClass = $order->status;
                                @endphp
                                <span class="status-badge status-{{ $statusClass }}">
                                    <i class="bi {{ $statusIcons[$order->status] ?? 'bi-info-circle' }}"></i>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-8 border-end-lg mb-4 mb-lg-0">
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="order-item-mini" title="{{ $item->product->name }}">
                                                <img src="{{ $item->product->first_image }}" alt="" class="mini-img shadow-sm" onerror="this.src='/images/placeholder-product.png'">
                                                @if($loop->last && $order->items->count() > 4)
                                                    <div class="bg-light rounded-circle ms-1 d-flex align-items-center justify-content-center" style="width:44px; height:44px; font-size: 0.8rem; font-weight: 700; border: 1px dashed #ddd;">
                                                        +{{ $order->items->count() - 4 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-muted small">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} in this order
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 text-center">
                                    <div class="d-grid gap-2 d-md-flex justify-content-center">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary rounded-pill px-4 shadow-sm hover-up">
                                            Track Order <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                        <a href="{{ route('orders.print', $order) }}" class="btn btn-outline-dark rounded-pill px-4 hover-up">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 d-flex justify-content-center">{{ $orders->links() }}</div>
    @else
        <div class="text-center py-5 reveal-3d">
            <div class="display-1 text-muted opacity-25 mb-4">
                <i class="bi bi-bag-heart"></i>
            </div>
            <h3 class="fw-bold" style="font-family: 'Outfit', sans-serif;">Build Your First Order!</h3>
            <p class="text-muted mx-auto mb-4" style="max-width: 400px;">Explore our premium collection and start your shopping journey today. We have amazing deals waiting for you!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg hover-up">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
