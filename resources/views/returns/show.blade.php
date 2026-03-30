@extends('layouts.app')

@section('title', 'Return Details - #' . $return->id)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $return->order) }}" class="text-decoration-none">#{{ $return->order->order_number }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Return #{{ $return->id }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-4 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1">Return Request</h4>
                            <p class="text-muted mb-0">Submitted on {{ $return->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            @if($return->status == 'pending')
                                <span class="badge bg-warning rounded-pill px-3 py-2 fs-6 text-dark"><i class="bi bi-hourglass-top me-1"></i> Under Review</span>
                            @elseif($return->status == 'approved')
                                <span class="badge bg-success rounded-pill px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i> Approved</span>
                            @elseif($return->status == 'rejected')
                                <span class="badge bg-danger rounded-pill px-3 py-2 fs-6"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                            @elseif($return->status == 'refunded')
                                <span class="badge bg-info rounded-pill px-3 py-2 fs-6"><i class="bi bi-cash me-1"></i> Refunded</span>
                            @endif
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-muted">RETURN REASON</h6>
                    <div class="p-4 bg-light rounded-4 mb-4">
                        <h6 class="fw-bold mb-2">{{ $return->reason }}</h6>
                        <p class="mb-0 text-muted">{{ $return->customer_note ?: 'No additional notes provided.' }}</p>
                    </div>

                    @if($return->admin_note)
                    <h6 class="fw-bold mb-3 text-muted">ADMIN RESPONSE</h6>
                    <div class="p-4 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-4 mb-4">
                        <p class="mb-0">{{ $return->admin_note }}</p>
                    </div>
                    @endif

                    <h6 class="fw-bold mb-3 text-muted">ITEMS RETURNED</h6>
                    <div class="list-group list-group-flush border-top">
                        @foreach($return->items as $reqItem)
                            <div class="list-group-item d-flex align-items-center gap-3 py-3 px-0 border-bottom">
                                <div class="rounded-3 overflow-hidden bg-light" style="width: 50px; height: 50px;">
                                    @if($reqItem->orderItem->product->images && count($reqItem->orderItem->product->images) > 0)
                                        <img src="{{ $reqItem->orderItem->product->images[0] }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <i class="bi bi-image text-muted d-flex justify-content-center align-items-center h-100"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $reqItem->orderItem->product->name }}</h6>
                                    <span class="text-muted small">Qty Returned: {{ $reqItem->quantity }}</span>
                                </div>
                                <div class="fw-bold">
                                    £{{ number_format($reqItem->orderItem->price * $reqItem->quantity, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Refund Details</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Status</span>
                        <span class="fw-bold">{{ ucfirst($return->status) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                        <span class="fw-bold">Expected/Issued Refund</span>
                        <span class="fs-4 fw-bold text-primary">£{{ number_format($return->refund_amount, 2) }}</span>
                    </div>
                    
                    @if($return->status == 'pending')
                    <div class="alert alert-info border-0 rounded-4 mt-4 d-flex gap-2">
                        <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
                        <small>Your refund amount may change after our team reviews your return request and inspects the items.</small>
                    </div>
                    @endif
                </div>
            </div>

            @if($return->photo_path)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Uploaded Evidence</h6>
                    <img src="{{ asset('storage/' . $return->photo_path) }}" class="img-fluid rounded-3" alt="Evidence">
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
