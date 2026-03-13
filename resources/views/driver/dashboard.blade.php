@extends('layouts.driver')
@section('title', 'Driver Dashboard — Premier Shop')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 reveal-3d">
        <div>
            <h2 class="fw-bold mb-0">Driver Dashboard</h2>
            <p class="text-muted">Welcome back, {{ $driver->name }}</p>
        </div>
        <form action="{{ route('driver.toggleDuty') }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $driver->is_on_duty ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-4 tilt-3d">
                <i class="bi bi-power me-2"></i>
                {{ $driver->is_on_duty ? 'On Duty' : 'Off Duty' }}
            </button>
        </form>
    </div>

    @if(!$driver->is_on_duty)
        <div class="alert alert-warning rounded-4 p-4 mb-5 reveal-3d delay-1">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                <div>
                    <h5 class="alert-heading mb-1 fw-bold">You are currently Off Duty</h5>
                    <p class="mb-0">You won't be assigned new orders until you toggle your status to On Duty.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 reveal-3d delay-2">
        <div class="col-12">
            <h4 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i>Assigned Deliveries</h4>
            <div class="row g-4 stagger-children">
                @forelse($assignedOrders as $order)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden tilt-3d">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill px-3 py-2" style="background:rgba(108,92,231,0.1);color:#6C5CE7;">
                                    #{{ $order->order_number }}
                                </span>
                                <span class="badge bg-{{ $order->status === 'shipped' ? 'info' : 'warning' }} text-dark rounded-pill px-3 py-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <h5 class="fw-bold mb-2">{{ $order->user->name }}</h5>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $order->shipping_address['address_line'] ?? 'No address' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <span class="fw-bold fs-5">£{{ number_format($order->total, 2) }}</span>
                                <a href="{{ route('driver.orders.show', $order) }}" class="btn btn-primary rounded-pill px-4">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 reveal-3d">
                    <div class="mb-3">
                        <i class="bi bi-inbox text-muted display-1"></i>
                    </div>
                    <h5 class="text-muted">No orders assigned to you yet.</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
