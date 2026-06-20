{{--
    profile/edit.blade.php — Customer profile settings (Sleek Modern Redesign)
    ==========================================================================
    Aesthetics: Sweeping gradient wave header, centered circular avatar with 
    interactive camera badge, My Orders grid card, actionable list navigation, 
    and collapsible profile management settings.
--}}
@extends('layouts.app')

@section('title', 'My Account - Premier Shop')

@section('content')
<style>
    /* Premium Profile Swoosh Wave Header */
    .profile-card-header {
        position: relative;
        background: linear-gradient(135deg, #743089 0%, #A45FBF 100%);
        border-radius: 0 0 40px 40px;
        padding: 50px 20px 40px;
        color: #ffffff;
        text-align: center;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(116, 48, 137, 0.15);
    }
    
    .profile-card-header::before {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -10%;
        width: 120%;
        height: 100px;
        background: var(--bs-body-bg);
        border-radius: 50%;
        z-index: 1;
    }

    .profile-header-container {
        position: relative;
        z-index: 2;
    }

    /* Back Chevron Navigation */
    .profile-back-btn {
        position: absolute;
        top: 0px;
        left: 10px;
        color: #ffffff;
        font-size: 1.4rem;
        text-decoration: none;
        transition: transform 0.2s;
    }
    .profile-back-btn:hover {
        transform: translateX(-3px);
        color: rgba(255,255,255,0.8);
    }

    .profile-title-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 25px;
        letter-spacing: 0.5px;
    }

    /* Interactive Avatar & Camera Badge */
    .avatar-wrapper-profile {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 16px;
    }

    .avatar-circle-profile {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        background: #ffffff;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .avatar-circle-profile:hover {
        transform: scale(1.03);
    }

    .camera-badge-profile {
        position: absolute;
        bottom: 0px;
        right: 0px;
        width: 32px;
        height: 32px;
        background: #ffffff;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        color: #555555;
        transition: all 0.2s ease;
    }

    .camera-badge-profile:hover {
        background: #f0f0f0;
        transform: scale(1.1);
        color: var(--bs-primary, #743089);
    }

    .profile-name-bold {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.6rem;
        color: #2D3436;
        margin-bottom: 4px;
        margin-top: 15px;
    }

    .profile-subtitle-role {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        color: #888888;
        margin-bottom: 0;
    }

    /* Order State Widgets Card */
    .orders-card-profile {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(116, 48, 137, 0.04), 0 2px 10px rgba(0,0,0,0.01);
        padding: 24px;
        margin-top: 24px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    [data-bs-theme="dark"] .orders-card-profile {
        background: rgba(20, 19, 30, 0.5);
        border-color: rgba(255, 255, 255, 0.04);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .orders-card-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--bs-body-color);
        margin-bottom: 20px;
    }

    .orders-grid-profile {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .order-tile-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 12px 6px;
        border-radius: 16px;
        position: relative;
    }

    .order-tile-item:hover {
        transform: translateY(-3px);
        background: rgba(0,0,0,0.02);
    }

    [data-bs-theme="dark"] .order-tile-item:hover {
        background: rgba(255,255,255,0.03);
    }

    .order-tile-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .order-tile-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #7f8c8d;
        text-align: center;
        font-family: 'Inter', sans-serif;
    }

    [data-bs-theme="dark"] .order-tile-label {
        color: #a4b0be;
    }

    .order-tile-badge {
        position: absolute;
        top: 6px;
        right: 12px;
        background: #ff7675;
        color: #ffffff;
        font-size: 0.62rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 10px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 5px rgba(255,118,117,0.3);
    }

    [data-bs-theme="dark"] .order-tile-badge {
        border-color: #14131e;
    }

    /* Actions Navigation Rows */
    .actions-list-profile {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(116, 48, 137, 0.04), 0 2px 10px rgba(0,0,0,0.01);
        padding: 8px 16px;
        margin-top: 24px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    [data-bs-theme="dark"] .actions-list-profile {
        background: rgba(20, 19, 30, 0.5);
        border-color: rgba(255, 255, 255, 0.04);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .action-row-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 8px;
        text-decoration: none !important;
        color: var(--bs-body-color);
        transition: all 0.2s;
    }

    .action-row-item:not(:last-child) {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .action-row-item:not(:last-child) {
        border-bottom-color: rgba(255, 255, 255, 0.05);
    }

    .action-row-item:hover {
        color: var(--bs-primary, #743089);
    }

    .action-row-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .action-row-icon {
        font-size: 1.25rem;
        color: #888888;
        display: flex;
        align-items: center;
    }

    .action-row-item:hover .action-row-icon {
        color: var(--bs-primary, #743089);
    }

    .action-row-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .action-row-right {
        color: #cccccc;
        font-size: 0.9rem;
    }

    /* Centered Logout Button */
    .logout-btn-profile {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: none;
        color: #7f8c8d;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        transition: color 0.2s;
        margin: 35px auto 10px;
        padding: 10px 20px;
        text-decoration: none !important;
    }

    .logout-btn-profile:hover {
        color: #ff7675;
    }

    .profile-settings-collapse {
        margin-top: 24px;
    }

    [data-bs-theme="dark"] .avatar-circle-profile {
        border-color: var(--ps-surface-secondary);
        background: var(--ps-surface-secondary);
    }
    [data-bs-theme="dark"] .camera-badge-profile {
        background: var(--ps-surface-secondary);
        color: var(--ps-text);
    }
    [data-bs-theme="dark"] .camera-badge-profile:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    [data-bs-theme="dark"] .profile-name-bold {
        color: var(--ps-text);
    }
</style>

{{-- Direct Camera Photo Form --}}
<form id="avatar-direct-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="d-none">
    @csrf
    @method('patch')
    <input type="hidden" name="name" value="{{ $user->name }}">
    <input type="hidden" name="email" value="{{ $user->email }}">
    <input type="file" name="profile_photo" id="direct_profile_photo" accept="image/*" data-autosubmit>
</form>

<div class="container py-4" style="max-width: 600px;">
    {{-- Swoosh Wave Header Section --}}
    <div class="profile-card-header">
        <div class="profile-header-container">
            <a href="{{ route('home') }}" class="profile-back-btn"><i class="bi bi-chevron-left"></i></a>
            <div class="profile-title-text">Profile</div>
            
            <div class="avatar-wrapper-profile">
                <div class="avatar-circle-profile">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                </div>
                <button type="button" class="camera-badge-profile" data-trigger-click="direct_profile_photo" title="Change Profile Picture">
                    <i class="bi bi-camera"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- User Identity Subtitles --}}
    <div class="text-center mt-3">
        <h3 class="profile-name-bold">{{ $user->name }}</h3>
        <p class="profile-subtitle-role">
            @if($user->isAdmin())
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-bold"><i class="bi bi-shield-lock me-1"></i>Shop Administrator</span>
            @else
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold"><i class="bi bi-star me-1"></i>Premium Member</span>
            @endif
        </p>
    </div>

    {{-- My Orders Section --}}
    <div class="orders-card-profile">
        <h5 class="orders-card-title">My Orders</h5>
        <div class="orders-grid-profile">
            {{-- Pending --}}
            <a href="{{ route('orders.index') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(116, 48, 137, 0.1); color: #743089;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <span class="order-tile-label">Pending</span>
                @php $pendingCount = $user->orders()->where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="order-tile-badge">{{ $pendingCount }}</span>
                @endif
            </a>

            {{-- Delivered --}}
            <a href="{{ route('orders.index') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(241, 196, 15, 0.1); color: #f1c40f;">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="order-tile-label">Delivered</span>
                @if($orderCounts['delivered'] > 0)
                    <span class="order-tile-badge">{{ $orderCounts['delivered'] }}</span>
                @endif
            </a>

            {{-- Processing --}}
            <a href="{{ route('orders.index') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(230, 126, 34, 0.1); color: #e67e22;">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <span class="order-tile-label">Processing</span>
                @if($orderCounts['processing'] > 0)
                    <span class="order-tile-badge">{{ $orderCounts['processing'] }}</span>
                @endif
            </a>

            {{-- Cancelled --}}
            <a href="{{ route('orders.index') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                    <i class="bi bi-x-circle"></i>
                </div>
                <span class="order-tile-label">Cancelled</span>
                @if($orderCounts['cancelled'] > 0)
                    <span class="order-tile-badge">{{ $orderCounts['cancelled'] }}</span>
                @endif
            </a>

            {{-- Wishlist --}}
            <a href="{{ route('wishlists.index') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <span class="order-tile-label">Wishlist</span>
                @if($wishlistCount > 0)
                    <span class="order-tile-badge">{{ $wishlistCount }}</span>
                @endif
            </a>

            {{-- Customer Care --}}
            <a href="{{ route('contact') }}" class="order-tile-item">
                <div class="order-tile-icon" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6;">
                    <i class="bi bi-headset"></i>
                </div>
                <span class="order-tile-label">Care</span>
            </a>
        </div>
    </div>

    {{-- Actions Navigation List --}}
    <div class="actions-list-profile">
        {{-- Edit Profile Toggle --}}
        <a href="#profileSettings" class="action-row-item" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="profileSettings">
            <div class="action-row-left">
                <span class="action-row-icon"><i class="bi bi-person"></i></span>
                <span class="action-row-text">Edit Profile</span>
            </div>
            <span class="action-row-right"><i class="bi bi-chevron-right"></i></span>
        </a>

        {{-- Shipping Addresses --}}
        <a href="{{ route('addresses.index') }}" class="action-row-item">
            <div class="action-row-left">
                <span class="action-row-icon"><i class="bi bi-geo-alt"></i></span>
                <span class="action-row-text">Shipping Address</span>
            </div>
            <span class="action-row-right"><i class="bi bi-chevron-right"></i></span>
        </a>

        {{-- Loyalty points if enabled --}}
        @php $loyaltyEnabled = \App\Models\Setting::get('loyalty_enabled', false); @endphp
        @if($loyaltyEnabled)
        <a href="{{ route('profile.rewards') }}" class="action-row-item">
            <div class="action-row-left">
                <span class="action-row-icon"><i class="bi bi-star"></i></span>
                <span class="action-row-text">Loyalty Rewards ({{ auth()->user()->loyalty_points }} Pts)</span>
            </div>
            <span class="action-row-right"><i class="bi bi-chevron-right"></i></span>
        </a>
        @endif
    </div>

    {{-- Collapsible Account Settings Forms --}}
    <div class="collapse profile-settings-collapse" id="profileSettings">
        <div class="profile-section" style="margin-top: 0;">
            {{-- Update Profile Information --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Two-Factor Sign-In --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    @include('profile.partials.two-factor-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card border-danger border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Centered Logout --}}
    <div class="text-center">
        <a href="#" data-submit-form="logout-form-profile-main" class="logout-btn-profile">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <form id="logout-form-profile-main" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
    // Auto-expand settings if there's a validation error or status message
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() || session('status'))
            const settingsEl = document.getElementById('profileSettings');
            if (settingsEl) {
                const collapse = new bootstrap.Collapse(settingsEl, { show: true });
                settingsEl.scrollIntoView({ behavior: 'smooth' });
            }
        @endif
    });
</script>
@endpush
@endsection