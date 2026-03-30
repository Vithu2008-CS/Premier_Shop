@extends('layouts.admin_noble')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Returns</a></li>
        <li class="breadcrumb-item active" aria-current="page">Request #{{ $return->id }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Return Details</h6>

                <div class="mb-4">
                    <h5 class="text-muted mb-2">Customer & Order Info</h5>
                    <p><strong>Customer:</strong> {{ $return->user->name }} ({{ $return->user->email }})</p>
                    <p><strong>Order Number:</strong> <a href="{{ route('admin.orders.show', $return->order) }}">{{ $return->order->order_number }}</a></p>
                    <p><strong>Date Requested:</strong> {{ $return->created_at->format('M d, Y H:iA') }}</p>
                </div>

                <div class="mb-4">
                    <h5 class="text-muted mb-2">Reason & Statement</h5>
                    <p><strong>Reason:</strong> {{ $return->reason }}</p>
                    <div class="p-3 bg-light border">
                        {{ $return->customer_note ?: 'No additional notes.' }}
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="text-muted mb-2">Items Being Returned</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty Returned</th>
                                    <th>Price Each</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalReturnVal = 0; @endphp
                                @foreach($return->items as $item)
                                    @php 
                                        $sub = $item->quantity * $item->orderItem->price;
                                        $totalReturnVal += $sub; 
                                    @endphp
                                    <tr>
                                        <td>{{ $item->orderItem->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>£{{ number_format($item->orderItem->price, 2) }}</td>
                                        <td>£{{ number_format($sub, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Max Value of Returned Items:</th>
                                    <th>£{{ number_format($totalReturnVal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($return->photo_path)
                <div>
                    <h5 class="text-muted mb-2">Customer Uploaded Evidence</h5>
                    <a href="{{ asset('storage/' . $return->photo_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $return->photo_path) }}" alt="Evidence" class="img-fluid" style="max-width: 300px;">
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Process Return</h6>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.returns.update', $return) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Update Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ $return->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $return->status == 'approved' ? 'selected' : '' }}>Approved (Restores Stock)</option>
                            <option value="rejected" {{ $return->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="refunded" {{ $return->status == 'refunded' ? 'selected' : '' }}>Refund Processed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Refund Amount (£)</label>
                        <input type="number" step="0.01" name="refund_amount" class="form-control" value="{{ old('refund_amount', $return->refund_amount > 0 ? $return->refund_amount : $totalReturnVal) }}">
                        <small class="form-text text-muted">Enter the exact amount to refund the customer.</small>
                    </div>

                    <div class="form-group">
                        <label>Admin Notes (Visible to Customer)</label>
                        <textarea name="admin_note" class="form-control" rows="4">{{ old('admin_note', $return->admin_note) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                    
                    @if($return->status == 'approved')
                    <div class="mt-3 text-success">
                        <i data-feather="check-circle" class="icon-sm"></i> Stock has been restored for these items.
                    </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
