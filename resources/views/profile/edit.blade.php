@extends('layouts.app')

@section('title', 'My Account - Premier Shop')

@section('content')
{{-- Profile Header --}}
<div class="profile-header">
    <div class="profile-header-bg"></div>
    <div class="container position-relative">
        <div class="profile-header-content">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="profile-info">
                    <h2 class="profile-name">{{ $user->name }}</h2>
                    <div class="profile-meta">
                        <span><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                        @if($user->phone)
                            <span class="d-none d-sm-inline"><i class="bi bi-telephone me-1"></i>{{ $user->phone }}</span>
                        @endif
                    </div>
                    <div class="profile-stats-inline d-none d-md-flex">
                        <span><i class="bi bi-heart-fill text-danger me-1"></i>{{ $wishlistCount }} Wishlist</span>
                        <span class="mx-2">·</span>
                        <span><i class="bi bi-bag-fill me-1"></i>{{ $totalOrders }} Orders</span>
                        <span class="mx-2">·</span>
                        <span><i class="bi bi-calendar3 me-1"></i>Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            <a href="#profileSettings" class="profile-settings-btn" data-bs-toggle="collapse">
                <i class="bi bi-gear-fill"></i>
            </a>
        </div>
    </div>
</div>

<div class="container py-4">
    {{-- Mobile Stats Row --}}
    <div class="d-flex d-md-none justify-content-around mb-4 profile-mobile-stats">
        <div class="text-center">
            <div class="fw-bold fs-5">{{ $wishlistCount }}</div>
            <small class="text-muted">Wishlist</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-5">{{ $totalOrders }}</div>
            <small class="text-muted">Orders</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-5">{{ $user->created_at->format('M \'y') }}</div>
            <small class="text-muted">Joined</small>
        </div>
    </div>

    {{-- My Orders Section --}}
    <div class="profile-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="profile-section-title mb-0"><i class="bi bi-bag me-2"></i>My Orders</h5>
            <a href="{{ route('orders.index') }}" class="profile-view-all">View All Orders <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="order-status-grid">
            <a href="{{ route('orders.index') }}" class="order-status-item">
                <div class="order-status-icon" style="background: rgba(253, 203, 110, 0.15); color: #f39c12;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <span class="order-status-label">Processing</span>
                @if($orderCounts['processing'] > 0)
                    <span class="order-status-badge">{{ $orderCounts['processing'] }}</span>
                @endif
            </a>
            <a href="{{ route('orders.index') }}" class="order-status-item">
                <div class="order-status-icon" style="background: rgba(0, 206, 201, 0.15); color: #00cec9;">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="order-status-label">Shipped</span>
                @if($orderCounts['shipped'] > 0)
                    <span class="order-status-badge">{{ $orderCounts['shipped'] }}</span>
                @endif
            </a>
            <a href="{{ route('orders.index') }}" class="order-status-item">
                <div class="order-status-icon" style="background: rgba(0, 184, 148, 0.15); color: #00b894;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <span class="order-status-label">Delivered</span>
                @if($orderCounts['delivered'] > 0)
                    <span class="order-status-badge">{{ $orderCounts['delivered'] }}</span>
                @endif
            </a>
            <a href="{{ route('orders.index') }}" class="order-status-item">
                <div class="order-status-icon" style="background: rgba(225, 112, 85, 0.15); color: #e17055;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <span class="order-status-label">Cancelled</span>
                @if($orderCounts['cancelled'] > 0)
                    <span class="order-status-badge">{{ $orderCounts['cancelled'] }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Recent Orders --}}
    @if($recentOrders->count() > 0)
    <div class="profile-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="profile-section-title mb-0"><i class="bi bi-receipt me-2"></i>Recent Orders</h5>
            <a href="{{ route('orders.index') }}" class="profile-view-all">View More <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="recent-orders-list">
            @foreach($recentOrders as $order)
            <a href="{{ route('orders.show', $order) }}" class="recent-order-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="recent-order-icon">
                        <i class="bi bi-bag"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $order->order_number }}</div>
                        <small class="text-muted">{{ $order->created_at->format('d M Y') }} · {{ $order->items->count() }} items</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">£{{ number_format($order->total, 2) }}</div>
                        @php
                            $statusColors = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} rounded-pill">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="profile-section">
        <h5 class="profile-section-title mb-3"><i class="bi bi-grid me-2"></i>Quick Actions</h5>
        <div class="quick-action-grid">
            <a href="{{ route('orders.index') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-bag"></i>
                </div>
                <span>My Orders</span>
            </a>
            <a href="{{ route('wishlists.index') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-heart"></i>
                </div>
                <span>My Wishlist</span>
            </a>
            <a href="{{ route('cart.index') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-cart3"></i>
                </div>
                <span>My Cart</span>
            </a>
            <a href="#profileSettings" class="quick-action-card" data-bs-toggle="collapse">
                <div class="quick-action-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <span>Edit Profile</span>
            </a>
            <a href="{{ route('products.index') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <span>Browse Shop</span>
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();" class="quick-action-card quick-action-logout">
                <div class="quick-action-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <span>Logout</span>
            </a>
            <form id="logout-form-profile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>

    {{-- Profile Settings (Collapsible) --}}
    <div class="collapse profile-settings-collapse" id="profileSettings">
        <div class="profile-section">
            <h5 class="profile-section-title mb-4"><i class="bi bi-gear me-2"></i>Account Settings</h5>

            {{-- Update Profile Information --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card border-danger border-0 shadow-sm">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-expand settings if there's a validation error or status message
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() || session('status'))
            const settingsEl = document.getElementById('profileSettings');
            if (settingsEl) {
                const collapse = new bootstrap.Collapse(settingsEl, { show: true });
            }
        @endif
    });
</script>
@endpush
@endsection