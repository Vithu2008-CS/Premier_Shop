@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number . ' — Admin')

@push('styles')
<style>
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
        }
        .admin-sidebar, .admin-topbar form, .btn-admin, .btn-admin-outline, .d-lg-none, .alert, .admin-card form, .card-title:contains('Update Status') {
            display: none !important;
        }
        .admin-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .admin-card {
            border: none !important;
            background: #fff !important;
            box-shadow: none !important;
            padding: 10px 0 !important;
        }
        .col-lg-4 .admin-card:last-child {
            display: none !important; /* Hide update status card completely */
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .print-header h1 { margin: 0; color: #000; font-size: 24px; }
        .print-header p { margin: 5px 0 0; color: #555; }
        
        /* Typography adjustments for print */
        .fw-bold { color: #000 !important; }
        .text-success { color: #000 !important; }
        small, p { color: #333 !important; }
        i.bi { display: none !important; } /* Hide icons in print for cleaner look */
        .admin-card .card-title {
            color: #000 !important;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
    }
    .print-header { display: none; }
</style>
@endpush

@section('content')
<div class="print-header">
    <h1>Premier Shop Invoice</h1>
    <p>Order #{{ $order->order_number }} | Date: {{ $order->created_at->format('M d, Y') }}</p>
</div>

<div class="admin-topbar">
    <div>
        <h2>Order {{ $order->order_number }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-admin"><i class="bi bi-printer me-1"></i> Print Order</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-admin-outline"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-title">Order Items</div>
            @foreach($order->items as $item)
            <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid var(--admin-border);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(108,92,231,0.1);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-box-seam" style="color:#A29BFE;"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $item->product->name }}</div>
                        <small style="color:var(--admin-muted);">{{ $item->quantity }} × £{{ number_format($item->price, 2) }}</small>
                    </div>
                </div>
                <strong>£{{ number_format($item->price * $item->quantity, 2) }}</strong>
            </div>
            @endforeach
            <div class="pt-3 mt-2">
                <div class="d-flex justify-content-between py-1"><span style="color:var(--admin-muted);">Subtotal</span><span>£{{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->discount_amount > 0)
                <div class="d-flex justify-content-between py-1 text-success"><span>Discount ({{ $order->coupon_code }})</span><span>-£{{ number_format($order->discount_amount, 2) }}</span></div>
                @endif
                <div class="d-flex justify-content-between py-1"><span style="color:var(--admin-muted);">Shipping</span><span>£{{ number_format($order->shipping_cost, 2) }}</span></div>
                <hr style="border-color:var(--admin-border);">
                <div class="d-flex justify-content-between fs-5 fw-bold"><span>Total</span><span style="color:#A29BFE;">£{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-title">Customer</div>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">{{ substr($order->user->name, 0, 1) }}</div>
                <div>
                    <div class="fw-bold">{{ $order->user->name }}</div>
                    <div style="color:var(--admin-muted);font-size:0.85rem;">{{ $order->user->email }}</div>
                </div>
            </div>
            @if($order->user->phone)
            <div style="color:var(--admin-muted);font-size:0.85rem;"><i class="bi bi-telephone me-2"></i>{{ $order->user->phone }}</div>
            @endif
        </div>

        <div class="admin-card mb-4">
            <div class="card-title">Shipping</div>
            <p style="color:var(--admin-muted);font-size:0.9rem;">{{ $order->shipping_address['address_line'] ?? '' }}<br>{{ $order->shipping_address['city'] ?? '' }}</p>
            <p style="color:var(--admin-muted);">No address provided</p>
        </div>

        <div class="admin-card">
            <div class="card-title">Update Status</div>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf @method('PATCH')
                <select name="status" class="form-select mb-3" style="background:rgba(255,255,255,0.05);border-color:var(--admin-border);color:#fff;">
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-admin w-100">Update Status</button>
            </form>
        </div>
    </div>
</div>
@endsection