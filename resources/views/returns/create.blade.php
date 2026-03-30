@extends('layouts.app')

@section('title', 'Request Return - Order ' . $order->order_number)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}" class="text-decoration-none">#{{ $order->order_number }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Request Return</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h4 class="mb-0 fw-bold">Request a Return</h4>
                    <p class="text-muted mb-0 small">Please select the items you wish to return and tell us why.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('returns.store', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h6 class="fw-bold mb-3 text-muted">SELECT ITEMS TO RETURN</h6>
                        <div class="list-group mb-4">
                            @foreach($order->items as $item)
                                <div class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3">
                                    <div class="rounded-3 overflow-hidden" style="width: 60px; height: 60px; background: #f8f9fa;">
                                        @if($item->product->images && count($item->product->images) > 0)
                                            <img src="{{ $item->product->images[0] }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <i class="bi bi-image text-muted d-flex justify-content-center align-items-center h-100"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                        <div class="text-muted small">Purchased: {{ $item->quantity }} x £{{ number_format($item->price, 2) }}</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small fw-bold text-muted">Return Qty:</span>
                                        <select name="items[{{ $item->id }}]" class="form-select form-select-sm" style="width: 70px;">
                                            <option value="0">0</option>
                                            @for($i = 1; $i <= $item->quantity; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">REASON FOR RETURN</label>
                            <select name="reason" class="form-select form-select-lg rounded-3" required>
                                <option value="" selected disabled>Select a reason...</option>
                                <option value="Defective or Damaged">Defective or Damaged</option>
                                <option value="Wrong Item Sent">Wrong Item Sent</option>
                                <option value="Item not as described">Item not as described</option>
                                <option value="Changed my mind">Changed my mind</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">ADDITIONAL DETAILS <span class="fw-normal text-muted">(Optional)</span></label>
                            <textarea name="customer_note" class="form-control rounded-3" rows="3" placeholder="Please provide any additional information..."></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-muted small">PHOTO EVIDENCE <span class="fw-normal text-muted">(Optional)</span></label>
                            <input type="file" name="photo" class="form-control form-control-lg rounded-3" accept="image/*">
                            <small class="text-muted mt-1 d-block">If the item is damaged or incorrect, uploading a photo helps speed up the process. Max 5MB.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                            Submit Return Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
