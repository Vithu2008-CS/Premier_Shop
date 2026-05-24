{{--
    driver/order_details.blade.php — Driver order detail view
    ===========================================================
    Customer info, delivery address, order items list, mark-as-delivered form.
    Variable: $order (with items.product, user, shipping_address)
--}}
@extends('layouts.driver')
@section('title', 'Order #{{ $order->order_number }} — Driver')

@push('styles')
<style>
    /* ── Order Detail ───────────────────────────────────────── */
    .od-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: rgba(255,255,255,0.4);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: color 0.2s;
        margin-bottom: 16px;
    }
    .od-back:hover { color: #A29BFE; }
    .od-back i { font-size: 0.9rem; }

    .od-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.7rem;
        color: #ffffff;
        margin-bottom: 4px;
    }
    .od-subtitle { color: rgba(255,255,255,0.35); font-size: 0.85rem; }

    /* dark cards */
    .od-card {
        background: rgba(255,255,255,0.028);
        border: 1px solid rgba(255,255,255,0.075);
        border-radius: 22px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .od-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid rgba(255,255,255,0.055);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .od-card-header-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
    }
    .hicon-purple { background: rgba(108,92,231,0.2); color: #A29BFE; }
    .hicon-teal   { background: rgba(0,184,148,0.15);  color: #00cec9; }
    .hicon-amber  { background: rgba(253,203,110,0.15); color: #fdcb6e; }
    .hicon-red    { background: rgba(255,118,117,0.15); color: #ff7675; }

    .od-card-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: #ffffff;
    }
    .od-card-body { padding: 20px 22px; }

    /* customer info */
    .customer-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6C5CE7, #A29BFE);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.3rem;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(108,92,231,0.4);
    }
    .customer-name {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: #ffffff;
        margin-bottom: 2px;
    }
    .customer-email { font-size: 0.8rem; color: rgba(255,255,255,0.35); }

    /* address block */
    .addr-block {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 14px;
        padding: 16px 18px;
        margin-top: 18px;
    }
    .addr-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.87rem;
        color: rgba(255,255,255,0.7);
        line-height: 1.6;
        margin-bottom: 10px;
    }
    .addr-row:last-child { margin-bottom: 0; }
    .addr-row i { color: rgba(255,255,255,0.3); margin-top: 3px; flex-shrink: 0; }

    /* items list */
    .item-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.045);
    }
    .item-row:last-child { border-bottom: none; padding-bottom: 0; }
    .item-thumb {
        width: 46px; height: 46px;
        border-radius: 11px;
        overflow: hidden;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .item-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .item-name {
        font-weight: 600;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.85);
        flex: 1;
    }
    .item-qty {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.35);
        margin-top: 2px;
    }
    .item-price {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        color: #A29BFE;
        flex-shrink: 0;
    }
    .order-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0 0;
        margin-top: 6px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .order-total-label { font-size: 0.85rem; color: rgba(255,255,255,0.4); font-weight: 600; }
    .order-total-val {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.35rem;
        color: #ffffff;
    }

    /* ── Action sidebar ─────────────────────────────────────── */
    .od-form-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
        font-family: 'Outfit', sans-serif;
    }
    .od-input {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: #ffffff;
        padding: 10px 14px;
        font-size: 0.88rem;
        transition: border-color 0.2s, background 0.2s;
        width: 100%;
    }
    .od-input:focus {
        outline: none;
        border-color: rgba(108,92,231,0.5);
        background: rgba(108,92,231,0.06);
        color: #fff;
    }
    .od-file-wrap {
        background: rgba(255,255,255,0.04);
        border: 2px dashed rgba(255,255,255,0.12);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .od-file-wrap:hover {
        border-color: rgba(108,92,231,0.4);
        background: rgba(108,92,231,0.05);
    }
    .od-file-wrap input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .od-file-icon { font-size: 1.8rem; color: rgba(255,255,255,0.2); margin-bottom: 8px; }
    .od-file-text { font-size: 0.82rem; color: rgba(255,255,255,0.35); }

    .btn-mark-delivered {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #00b894, #00cec9);
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
        box-shadow: 0 6px 24px rgba(0,184,148,0.35);
    }
    .btn-mark-delivered:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 32px rgba(0,184,148,0.45);
    }
    .btn-mark-delivered:disabled {
        background: rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.3);
        box-shadow: none;
        cursor: not-allowed;
    }

    /* delivery proof */
    .proof-img-wrap {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 12px;
    }
    .proof-img-wrap img { width: 100%; display: block; }
    .proof-meta {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.35);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* status badge */
    .od-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 100px;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
    }
    .osb-shipped    { background: rgba(108,92,231,0.15); color: #A29BFE; }
    .osb-processing { background: rgba(253,203,110,0.15); color: #fdcb6e; }
    .osb-delivered  { background: rgba(0,184,148,0.12); color: #55efc4; }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- ── Back + title ── --}}
    <div class="mb-4 reveal-3d">
        <a href="{{ route('driver.dashboard') }}" class="od-back">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        <div class="d-flex flex-wrap align-items-center gap-3 mt-1">
            <div class="od-title">#{{ $order->order_number }}</div>
            @php
                $sbClass = match($order->status) {
                    'shipped'    => 'osb-shipped',
                    'processing' => 'osb-processing',
                    'delivered'  => 'osb-delivered',
                    default      => 'osb-processing',
                };
                $sbIcon = match($order->status) {
                    'shipped'    => 'bi-truck',
                    'processing' => 'bi-gear',
                    'delivered'  => 'bi-check2-circle',
                    default      => 'bi-clock',
                };
            @endphp
            <span class="od-status-badge {{ $sbClass }}">
                <i class="bi {{ $sbIcon }}"></i>{{ ucfirst($order->status) }}
            </span>
        </div>
        <div class="od-subtitle">Placed {{ $order->created_at->format('d M Y · H:i') }}</div>
    </div>

    <div class="row g-4">

        {{-- ── LEFT: customer + items ── --}}
        <div class="col-lg-7 reveal-slide-left">

            {{-- Customer --}}
            <div class="od-card">
                <div class="od-card-header">
                    <span class="od-card-header-icon hicon-purple"><i class="bi bi-person-fill"></i></span>
                    <span class="od-card-title">Customer</span>
                </div>
                <div class="od-card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="customer-avatar">{{ strtoupper(substr($order->user->name, 0, 1)) }}</div>
                        <div>
                            <div class="customer-name">{{ $order->user->name }}</div>
                            <div class="customer-email">{{ $order->user->email }}</div>
                        </div>
                    </div>
                    <div class="addr-block">
                        <div class="addr-row">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>
                                {{ $order->shipping_address['address_line'] ?? 'No address' }}<br>
                                {{ $order->shipping_address['city'] ?? '' }}
                                @if(!empty($order->shipping_address['postcode']))
                                    · {{ $order->shipping_address['postcode'] }}
                                @endif
                            </span>
                        </div>
                        @if(!empty($order->shipping_address['phone']))
                        <div class="addr-row">
                            <i class="bi bi-telephone-fill"></i>
                            <a href="tel:{{ $order->shipping_address['phone'] }}" style="color:rgba(255,255,255,0.7);text-decoration:none;">
                                {{ $order->shipping_address['phone'] }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="od-card">
                <div class="od-card-header">
                    <span class="od-card-header-icon hicon-amber"><i class="bi bi-box-seam-fill"></i></span>
                    <span class="od-card-title">Order Items ({{ $order->items->count() }})</span>
                </div>
                <div class="od-card-body">
                    @foreach($order->items as $item)
                    <div class="item-row">
                        <div class="item-thumb">
                            @if($item->product->images && count($item->product->images) > 0)
                                <img src="{{ $item->product->images[0] }}" alt="" loading="lazy" onerror="this.style.display='none'">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:rgba(255,255,255,0.2);">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="item-name text-truncate">{{ $item->product->name }}</div>
                            <div class="item-qty">× {{ $item->quantity }}</div>
                        </div>
                        <div class="item-price">£{{ number_format($item->price * $item->quantity, 2) }}</div>
                    </div>
                    @endforeach
                    <div class="order-total-row">
                        <span class="order-total-label">Grand Total</span>
                        <span class="order-total-val">£{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: action + proof ── --}}
        <div class="col-lg-5 reveal-slide-right">

            {{-- Mark delivered --}}
            <div class="od-card">
                <div class="od-card-header">
                    <span class="od-card-header-icon hicon-teal"><i class="bi bi-check2-circle"></i></span>
                    <span class="od-card-title">Complete Delivery</span>
                </div>
                <div class="od-card-body">
                    @if($order->status === 'delivered')
                        <div style="text-align:center;padding:16px 0;color:rgba(0,184,148,0.9);">
                            <i class="bi bi-check-circle-fill" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                            <div style="font-family:'Outfit',sans-serif;font-weight:700;font-size:1rem;">Order Delivered!</div>
                            <div style="font-size:0.8rem;color:rgba(255,255,255,0.35);margin-top:4px;">
                                {{ $order->delivered_date->format('d M Y · H:i') }}
                            </div>
                        </div>
                    @else
                        <form action="{{ route('driver.orders.complete', $order) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <div class="od-form-label">Delivery Date & Time</div>
                                <input type="datetime-local"
                                       name="delivered_date"
                                       class="od-input"
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       required>
                            </div>
                            <div class="mb-5">
                                <div class="od-form-label">Proof of Delivery</div>
                                <div class="od-file-wrap">
                                    <input type="file" name="delivery_proof" accept="image/*" required>
                                    <div class="od-file-icon"><i class="bi bi-camera-fill"></i></div>
                                    <div class="od-file-text">Tap to take a photo or upload from gallery</div>
                                </div>
                            </div>
                            <button type="submit" class="btn-mark-delivered">
                                <i class="bi bi-check-circle-fill"></i>Mark as Delivered
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Delivery proof --}}
            @if($order->status === 'delivered' && $order->delivery_proof)
            <div class="od-card">
                <div class="od-card-header">
                    <span class="od-card-header-icon hicon-teal"><i class="bi bi-image-fill"></i></span>
                    <span class="od-card-title">Delivery Proof</span>
                </div>
                <div class="od-card-body">
                    <div class="proof-img-wrap">
                        <img src="{{ (str_starts_with($order->delivery_proof, 'data:image') || str_starts_with($order->delivery_proof, 'http'))
                            ? $order->delivery_proof
                            : asset('storage/' . $order->delivery_proof) }}"
                             alt="Delivery Proof">
                    </div>
                    <div class="proof-meta">
                        <i class="bi bi-calendar-check"></i>
                        Delivered {{ $order->delivered_date->format('d M Y · H:i') }}
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
