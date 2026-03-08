@extends('layouts.app')
@section('title', 'My Orders - Premier Shop')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>My Orders</h2>

    @if($orders->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="fw-bold">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>{{ $order->items->count() }} items</td>
                                <td class="fw-bold">£{{ number_format($order->total, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td><a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $orders->links() }}</div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-bag-x display-1 text-muted"></i>
            <h4 class="mt-3">No orders yet</h4>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
