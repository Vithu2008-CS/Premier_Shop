{{--
    orders/index.blade.php — Customer order history
    =================================================
    Paginated list of the auth user's orders, newest first.
    Each row: order number, date, status badge, total, view button.
    Empty state shown when user has no orders.
    Variable: $orders — paginated, from OrderController::index()
--}}
@extends('layouts.app')
@section('title', 'My Orders - Premier Shop')

@push('styles')
<style>
    /* Sleek order history style overrides */
    body {
        background-color: #f6f8fb !important; /* Soft premium background for stunning card separation */
    }
    [data-bs-theme="dark"] body {
        background-color: #09090d !important; /* Deep luxury dark background */
    }

    .order-card {
        background: #ffffff;
        border: 1px solid rgba(108, 92, 231, 0.1) !important;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(108, 92, 231, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        margin-bottom: 20px !important;
    }

    [data-bs-theme="dark"] .order-card {
        background: rgba(20, 19, 30, 0.85);
        border-color: rgba(255, 255, 255, 0.06) !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3) !important;
    }

    .order-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(108, 92, 231, 0.08) !important;
        border-color: rgba(108, 92, 231, 0.25) !important;
    }

    .order-card-header {
        background: rgba(108, 92, 231, 0.015);
        border-bottom: 1px solid rgba(108, 92, 231, 0.06);
        padding: 12px 20px;
    }

    [data-bs-theme="dark"] .order-card-header {
        background: rgba(255, 255, 255, 0.01);
        border-bottom-color: rgba(255, 255, 255, 0.05);
    }

    .order-id-badge {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.82rem;
        background: rgba(108, 92, 231, 0.08);
        color: #6C5CE7;
        padding: 4px 10px;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }

    [data-bs-theme="dark"] .order-id-badge {
        background: rgba(162, 155, 254, 0.15);
        color: #A29BFE;
    }

    .order-product-thumbnail-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .order-product-thumbnail {
        position: relative;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid var(--ps-card-border, rgba(0, 0, 0, 0.05));
        background: #fff;
        transition: all 0.2s ease;
    }

    [data-bs-theme="dark"] .order-product-thumbnail {
        background: #15141e;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .order-product-thumbnail:hover {
        transform: scale(1.06) translateY(-1px);
        border-color: #6C5CE7;
    }

    .order-product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-remaining-badge {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(108, 92, 231, 0.06);
        border: 1.5px dashed rgba(108, 92, 231, 0.2);
        color: #6C5CE7;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    [data-bs-theme="dark"] .order-remaining-badge {
        background: rgba(162, 155, 254, 0.08);
        border-color: rgba(162, 155, 254, 0.25);
        color: #A29BFE;
    }

    .order-remaining-badge:hover {
        background: #6C5CE7;
        border-color: #6C5CE7;
        color: #fff;
    }

    [data-bs-theme="dark"] .order-remaining-badge:hover {
        background: #A29BFE;
        border-color: #A29BFE;
        color: #0f0e17;
    }

    .order-total-price {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.25rem;
        color: #2D3436;
    }

    [data-bs-theme="dark"] .order-total-price {
        color: #ffffff;
    }

    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endpush

@section('content')
<div class="container section-padding">
    <div class="mb-4 reveal-3d">
        <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">My <span class="gradient-text">Orders</span></h2>
        <p class="text-muted mb-0">Track and manage your recent purchases</p>
    </div>

    @if($orders->count() > 0)
        <div class="row stagger-children">
            @foreach($orders as $order)
                <div class="col-12 fade-up">
                    <div class="order-card">
                        <!-- Slim Compact Header -->
                        <div class="order-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="order-id-badge">#{{ $order->order_number }}</span>
                                <span class="text-muted small font-outfit"><i class="bi bi-calendar3 me-1.5"></i>{{ $order->created_at->format('d M Y') }}</span>
                                @if($order->payment_method)
                                    <span class="badge bg-light-subtle text-secondary border px-2 py-1 small-caps fw-semibold" style="font-size: 0.68rem; border-radius: 6px;">
                                        <i class="bi bi-credit-card-2-front me-1"></i>{{ $order->payment_method === 'bank_transfer' ? 'Transfer' : 'Card' }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-center gap-3 ms-auto">
                                <span class="order-total-price">£{{ number_format($order->total, 2) }}</span>
                                
                                @php
                                    $statusIcons = [
                                        'pending' => 'bi-clock',
                                        'processing' => 'bi-gear',
                                        'shipped' => 'bi-truck',
                                        'delivered' => 'bi-check2',
                                        'cancelled' => 'bi-x-circle'
                                    ];
                                    $statusClass = $order->status;
                                @endphp
                                <span class="status-badge status-{{ $statusClass }} py-0.5 px-2.5" style="font-size: 0.7rem; border-radius: 8px;">
                                    <i class="bi {{ $statusIcons[$order->status] ?? 'bi-info-circle' }}"></i>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Slim Body -->
                        <div class="p-3">
                            <div class="row align-items-center g-3">
                                <div class="col-md-7 col-12">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <div class="order-product-thumbnail-group">
                                            @foreach($order->items->take(4) as $item)
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="order-product-thumbnail shadow-sm" title="{{ $item->product->name }}">
                                                    <img src="{{ $item->product->first_image }}" alt="" onerror="this.onerror=null; this.src='/images/placeholder-product.png'">
                                                </a>
                                            @endforeach
                                            @if($order->items->count() > 4)
                                                <a href="{{ route('orders.show', $order) }}" class="order-remaining-badge">
                                                    +{{ $order->items->count() - 4 }}
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-muted small fw-semibold font-outfit">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-5 col-12 text-md-end text-start">
                                    <div class="d-flex gap-2 justify-content-md-end justify-content-start align-items-center mt-2 mt-md-0">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-premium-gradient rounded-pill px-3.5 py-1.5 text-white d-inline-flex align-items-center gap-1 hover-up shadow-sm btn-sm font-outfit">
                                            <span>Track</span>
                                            <i class="bi bi-arrow-right-short fs-5 transition-transform"></i>
                                        </a>
                                        <a href="{{ route('orders.print', $order) }}" class="btn btn-outline-dark rounded-pill px-2.5 py-1.5 hover-up btn-sm" title="Print Invoice">
                                            <i class="bi bi-printer fs-6"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
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
