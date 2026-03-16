@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@section('content')
@push('styles')
<style>
    .stat-card-link {
        text-decoration: none;
        display: block;
        transition: transform 0.2s ease, filter 0.2s ease;
    }
    .stat-card-link:hover {
        transform: translateY(-5px) scale(1.02);
        filter: brightness(1.1);
    }
</style>
@endpush
<div class="admin-topbar">
    <div>
        <h2>Dashboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.products.index') }}" class="stat-card-link">
            <div class="stat-card bg-gradient-primary h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-number fs-4 fs-md-2">{{ $stats['totalProducts'] }}</div>
                        <div class="stat-label small">Total Products</div>
                    </div>
                    <div class="stat-icon d-none d-md-block"><i class="bi bi-box-seam"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.orders.index') }}" class="stat-card-link">
            <div class="stat-card h-100 p-3 p-md-4" style="background:linear-gradient(135deg,#00B894,#00CEC9);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-number fs-4 fs-md-2">{{ $stats['totalOrders'] }}</div>
                        <div class="stat-label small">Total Orders</div>
                    </div>
                    <div class="stat-icon d-none d-md-block"><i class="bi bi-receipt"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.reports.index') }}" class="stat-card-link">
            <div class="stat-card h-100 p-3 p-md-4" style="background:linear-gradient(135deg,#0984E3,#74B9FF);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-number fs-4 fs-md-2">£{{ number_format($stats['totalRevenue'], 0) }}</div>
                        <div class="stat-label small">Revenue</div>
                    </div>
                    <div class="stat-icon d-none d-md-block"><i class="bi bi-currency-pound"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.customers.index') }}" class="stat-card-link">
            <div class="stat-card h-100 p-3 p-md-4" style="background:linear-gradient(135deg,#E17055,#FDCB6E);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-number fs-4 fs-md-2">{{ $stats['totalCustomers'] }}</div>
                        <div class="stat-label small">Customers</div>
                    </div>
                    <div class="stat-icon d-none d-md-block"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>

@if($stats['lowStock'] > 0)
    <div class="alert alert-warning" style="border-radius:12px;">
        <i class="bi bi-exclamation-triangle me-2"></i><strong>{{ $stats['lowStock'] }}</strong> products have low stock (< 10 units).
    </div>
@endif

<div class="row g-4">
    {{-- Recent Orders --}}
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="card-title mb-0">Recent Orders</div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-admin-outline btn-sm">View All</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recentOrders'] as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" style="color:#A29BFE;text-decoration:none;font-weight:600;">{{ $order->order_number }}</a></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6C5CE7,#A29BFE);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.7rem;font-weight:700;">{{ substr($order->user->name, 0, 1) }}</div>
                                        {{ $order->user->name }}
                                    </div>
                                </td>
                                <td class="fw-bold">£{{ number_format($order->total, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                                    @endphp
                                    <span class="badge badge-status bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td style="color:var(--admin-muted);font-size:0.85rem;">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5" style="color:var(--admin-muted);">
                                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                No orders yet
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-title">Quick Actions</div>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-admin"><i class="bi bi-plus-lg me-2"></i>Add Product</a>
                <a href="{{ route('admin.scanner') }}" class="btn btn-admin-outline"><i class="bi bi-qr-code-scan me-2"></i>Scan QR Code</a>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin-outline"><i class="bi bi-tag me-2"></i>Create Coupon</a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-admin-outline"><i class="bi bi-receipt me-2"></i>Manage Orders</a>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-title">Store Info</div>
            <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                <span style="color:var(--admin-muted);">Active Products</span>
                <span class="fw-bold">{{ \App\Models\Product::where('is_active', true)->count() }}</span>
            </div>
            <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                <span style="color:var(--admin-muted);">Active Offers</span>
                <span class="fw-bold">{{ \App\Models\Product::withActiveOffers()->count() }}</span>
            </div>
            <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                <span style="color:var(--admin-muted);">Categories</span>
                <span class="fw-bold">{{ \App\Models\Category::count() }}</span>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span style="color:var(--admin-muted);">Low Stock Items</span>
                <span class="fw-bold text-warning">{{ $stats['lowStock'] }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
