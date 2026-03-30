@extends('layouts.admin_noble')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Return Requests</h6>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Date Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $return)
                                <tr>
                                    <td>{{ $return->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $return->order) }}">
                                            {{ $return->order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $return->user->name }}</td>
                                    <td>
                                        @if($return->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($return->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($return->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($return->status == 'refunded')
                                            <span class="badge badge-info">Refunded</span>
                                        @endif
                                    </td>
                                    <td>{{ $return->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No return requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
