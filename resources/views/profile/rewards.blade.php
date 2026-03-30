@extends('layouts.app')

@section('title', 'Loyalty Rewards - My Profile')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}" class="text-decoration-none">Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Loyalty Rewards</li>
        </ol>
    </nav>

    <div class="row mb-5">
        <div class="col-12 text-center reveal-fade">
            <h2 class="fw-bold display-5 mb-2" style="font-family: 'Outfit', sans-serif;">Loyalty Rewards</h2>
            <p class="text-muted fs-5">Earn points with every purchase and save on your next order!</p>
        </div>
    </div>

    @php 
        $redemptionValue = \App\Models\Setting::get('points_redemption_value', 0.01);
        $financialValue = $user->loyalty_points * $redemptionValue;
        $totalSavedFinancial = abs($totalSaved) * $redemptionValue;
    @endphp

    <div class="row g-4 mb-5 reveal-slide-up">
        <!-- Balance Card -->
        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow-lg rounded-4 h-100 overflow-hidden position-relative p-2">
                <div class="position-absolute opacity-25" style="top:-30px; right:-30px; transform: rotate(15deg);">
                    <i class="bi bi-star-fill text-white" style="font-size: 15rem;"></i>
                </div>
                <div class="card-body p-4 position-relative z-index-1">
                    <h6 class="text-uppercase fw-bold letter-spacing-1 mb-4 opacity-75">Available Balance</h6>
                    <h1 class="display-3 fw-bold mb-0 text-white">{{ number_format($user->loyalty_points) }}</h1>
                    <p class="fs-5 opacity-75 mb-0">Pts</p>
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <span class="fs-6 opacity-75">Cash Value: <strong class="text-white">£{{ number_format($financialValue, 2) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lifetime Stats -->
        <div class="col-md-8">
            <div class="row g-4 h-100">
                <div class="col-sm-6">
                    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                                    <i class="bi bi-graph-up-arrow fs-4"></i>
                                </div>
                                <h6 class="text-muted fw-bold mb-0 text-uppercase letter-spacing-1">Lifetime Earned</h6>
                            </div>
                            <h2 class="fw-bold mb-0">{{ number_format($totalEarned) }} <span class="fs-6 text-muted fw-normal">Pts</span></h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                                    <i class="bi bi-piggy-bank fs-4"></i>
                                </div>
                                <h6 class="text-muted fw-bold mb-0 text-uppercase letter-spacing-1">Lifetime Saved</h6>
                            </div>
                            <h2 class="fw-bold mb-0 text-success">£{{ number_format($totalSavedFinancial, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card border-0 shadow-sm rounded-4 reveal-slide-up" style="transition-delay: 0.1s;">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Points History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4 py-3">Date</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Order #</th>
                            <th class="text-end pe-4 py-3">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="ps-4 py-3 text-muted small">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td class="py-3">
                                <strong class="d-block">{{ $tx->description }}</strong>
                                @if($tx->type == 'earned' || $tx->type == 'bonus')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Earned</span>
                                @elseif($tx->type == 'redeemed')
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small">Redeemed</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small">Refunded/Adjusted</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($tx->order)
                                    <a href="{{ route('orders.show', $tx->order) }}" class="text-decoration-none fw-bold">{{ $tx->order->order_number }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                @if($tx->amount > 0)
                                    <span class="fw-bold text-success fs-5">+{{ number_format($tx->amount) }}</span>
                                @else
                                    <span class="fw-bold text-danger fs-5">{{ number_format($tx->amount) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-star mb-3 d-block fs-1" style="opacity: 0.3;"></i>
                                No point transactions found yet. Place your first order to start earning!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
            <div class="p-4 border-top">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.z-index-1 { z-index: 1; }
</style>
@endsection
