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
    .order-card {
        background: var(--ps-surface-glass, #ffffff);
        border: 1px solid var(--ps-card-border, rgba(0, 0, 0, 0.05));
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        margin-bottom: 24px;
    }

    [data-bs-theme="dark"] .order-card {
        background: rgba(20, 19, 30, 0.6);
        border-color: rgba(255, 255, 255, 0.06);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(108, 92, 231, 0.1), 0 4px 12px rgba(0, 0, 0, 0.02);
        border-color: rgba(108, 92, 231, 0.25);
    }

    .order-card-header {
        background: rgba(108, 92, 231, 0.02);
        border-bottom: 1px solid rgba(108, 92, 231, 0.05);
        padding: 20px 24px;
    }

    [data-bs-theme="dark"] .order-card-header {
        background: rgba(162, 155, 254, 0.02);
        border-bottom-color: rgba(255, 255, 255, 0.05);
    }

    .order-card-body-left {
        border-right: 1px solid rgba(108, 92, 231, 0.08);
    }

    [data-bs-theme="dark"] .order-card-body-left {
        border-right-color: rgba(255, 255, 255, 0.06);
    }

    @media (max-width: 991.98px) {
        .order-card-body-left {
            border-right: none;
            border-bottom: 1px solid rgba(108, 92, 231, 0.08);
            padding-bottom: 20px;
        }
        [data-bs-theme="dark"] .order-card-body-left {
            border-bottom-color: rgba(255, 255, 255, 0.06);
        }
    }

    .order-product-thumbnail-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .order-product-thumbnail {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 16px;
        overflow: hidden;
        border: 2px solid var(--ps-card-border, rgba(0, 0, 0, 0.05));
        background: #fff;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    [data-bs-theme="dark"] .order-product-thumbnail {
        background: #15141e;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .order-product-thumbnail:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 16px rgba(108, 92, 231, 0.15);
        border-color: #6C5CE7;
    }

    .order-product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-remaining-badge {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: rgba(108, 92, 231, 0.08);
        border: 2px dashed rgba(108, 92, 231, 0.25);
        color: #6C5CE7;
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        font-family: 'Outfit', sans-serif;
        text-decoration: none;
    }

    [data-bs-theme="dark"] .order-remaining-badge {
        background: rgba(162, 155, 254, 0.1);
        border-color: rgba(162, 155, 254, 0.3);
        color: #A29BFE;
    }

    .order-remaining-badge:hover {
        background: #6C5CE7;
        border-color: #6C5CE7;
        color: #fff;
        transform: scale(1.05);
    }

    [data-bs-theme="dark"] .order-remaining-badge:hover {
        background: #A29BFE;
        border-color: #A29BFE;
        color: #0f0e17;
    }

    .order-info-pill {
        background: rgba(108, 92, 231, 0.04);
        border: 1px solid rgba(108, 92, 231, 0.08);
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ps-text-muted, #747d8c);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    [data-bs-theme="dark"] .order-info-pill {
        background: rgba(162, 155, 254, 0.05);
        border-color: rgba(162, 155, 254, 0.1);
        color: rgba(255, 255, 255, 0.7);
    }
    
    .order-info-pill:hover {
        background: rgba(108, 92, 231, 0.08);
        border-color: rgba(108, 92, 231, 0.15);
        color: #6C5CE7;
    }

    [data-bs-theme="dark"] .order-info-pill:hover {
        background: rgba(162, 155, 254, 0.1);
        border-color: rgba(162, 155, 254, 0.2);
        color: #A29BFE;
    }

    .order-total-price {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.35rem;
        background: linear-gradient(135deg, #6C5CE7 0%, #A29BFE 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    [data-bs-theme="dark"] .order-total-price {
        background: linear-gradient(135deg, #A29BFE 0%, #ffffff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="container section-padding">
    <div class="mb-5 reveal-3d">
        <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">My <span class="gradient-text">Orders</span></h2>
        <p class="text-muted mb-0">Track and manage your recent purchases</p>
    </div>

    @if($orders->count() > 0)
        <div class="row stagger-children">
            @foreach($orders as $order)
                <div class="col-12 fade-up">
                    <div class="order-card">
                        <div class="order-card-header">
                            <div class="row align-items-center g-2">
                                <div class="col-12 col-md-8">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="order-info-pill">
                                            <i class="bi bi-hash text-primary"></i>
                                            <span class="text-body fw-bold">#{{ $order->order_number }}</span>
                                        </span>
                                        <span class="order-info-pill">
                                            <i class="bi bi-calendar3 text-primary"></i>
                                            {{ $order->created_at->format('d M Y') }}
                                        </span>
                                        @if($order->payment_method)
                                            <span class="order-info-pill">
                                                <i class="bi bi-credit-card-2-front text-primary"></i>
                                                {{ $order->payment_method === 'bank_transfer' ? 'Bank Transfer' : 'Stripe Card' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 text-md-end d-flex align-items-center justify-content-between justify-content-md-end gap-3 mt-2 mt-md-0">
                                    <div>
                                        <span class="order-total-price">£{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    
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
                                    <span class="status-badge status-{{ $statusClass }} py-1 px-3">
                                        <i class="bi {{ $statusIcons[$order->status] ?? 'bi-info-circle' }}"></i>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-8 order-card-body-left">
                                    <div class="order-product-thumbnail-group flex-wrap">
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
                                    <div class="mt-3 text-muted small fw-semibold">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} in this order
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 text-start text-lg-end">
                                    <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end w-100">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-premium-gradient rounded-pill px-4 py-2.5 text-white d-inline-flex align-items-center gap-2 hover-up shadow-sm">
                                            <span>Track Order</span>
                                            <i class="bi bi-arrow-right-short fs-5 transition-transform"></i>
                                        </a>
                                        <a href="{{ route('orders.print', $order) }}" class="btn btn-outline-dark rounded-pill px-3 py-2.5 hover-up" title="Print Invoice">
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
