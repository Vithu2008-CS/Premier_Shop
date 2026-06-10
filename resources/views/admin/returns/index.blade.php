{{--
    admin/returns/index.blade.php — Return request list
    =====================================================
    Table of all return requests: order number, customer, status, date.
    Pill status badges, clickable ID show view, circular delete actions.
    Variable: $returns (paginated, with order.user)
--}}
@extends('layouts.admin_noble')
@section('title', 'Return Requests')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm" style="border-radius: 18px !important; overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 700; letter-spacing: 0.3px;">Return Requests</h6>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill mr-2" style="font-size: 1.1rem;"></i>
                            <span class="font-weight-bold" style="font-size: 0.88rem;">{{ session('success') }}</span>
                        </div>
                        <button type="button" class="close text-success" data-dismiss="alert" aria-label="Close" style="padding: 1rem 1.25rem;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(0, 0, 0, 0.04);">
                                <th class="pl-4 py-3 align-middle">Request ID</th>
                                <th class="py-3 align-middle">Order #</th>
                                <th class="py-3 align-middle">Customer</th>
                                <th class="py-3 align-middle">Status</th>
                                <th class="py-3 align-middle">Date Requested</th>
                                <th class="pr-4 py-3 align-middle text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returns as $return)
                                <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.02); transition: all 0.2s ease;">
                                    <td class="pl-4 align-middle font-weight-bold">
                                        <a href="{{ route('admin.returns.show', $return) }}" class="font-weight-bold text-primary" style="font-size: 0.85rem;" title="View Return Request">
                                            REQ-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        @if($return->order)
                                            <a href="{{ route('admin.orders.show', $return->order) }}" class="font-weight-bold text-muted text-hover-primary" style="font-size: 0.85rem;">
                                                {{ $return->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="font-weight: 600; font-size: 0.85rem; color: #475569;">
                                        {{ $return->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="align-middle">
                                        @if($return->status == 'pending')
                                            <span class="badge px-3 py-1.5 bg-soft-warning font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Pending</span>
                                        @elseif($return->status == 'approved')
                                            <span class="badge px-3 py-1.5 bg-soft-success font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Approved</span>
                                        @elseif($return->status == 'rejected')
                                            <span class="badge px-3 py-1.5 bg-soft-danger font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Rejected</span>
                                        @elseif($return->status == 'refunded')
                                            <span class="badge px-3 py-1.5 bg-soft-info font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Refunded</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted" style="font-size: 0.8rem;">
                                        {{ $return->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="pr-4 align-middle text-right">
                                        <form action="{{ route('admin.returns.destroy', $return) }}" method="POST" class="d-inline-block" data-confirm="Are you sure you want to delete this return request?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-premium-delete">
                                                <i class="bi bi-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                            <i class="bi bi-box-arrow-in-left text-muted mb-3" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted font-weight-bold" style="font-size: 0.9rem;">No return requests found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                    <div class="text-muted small" style="font-size: 0.8rem;">
                        Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} entries
                    </div>
                    <div>
                        {{ $returns->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Soft-colored badges for light mode */
.bg-soft-success { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-warning { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-danger { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
.bg-soft-info { background: rgba(6,182,212,0.1) !important; color: #06b6d4 !important; }

/* Soft-colored badges for dark mode */
html[data-admin-theme="dark"] .bg-soft-success { background: rgba(52, 211, 153, 0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-warning { background: rgba(251, 191, 36, 0.15) !important; color: #fbbf24 !important; }
html[data-admin-theme="dark"] .bg-soft-danger { background: rgba(248, 113, 113, 0.15) !important; color: #f87171 !important; }
html[data-admin-theme="dark"] .bg-soft-info { background: rgba(34, 211, 238, 0.15) !important; color: #22d3ee !important; }

/* Hover effects for primary text link */
.text-hover-primary:hover {
    color: #6c5ce7 !important;
    text-decoration: none !important;
}
html[data-admin-theme="dark"] .text-hover-primary:hover {
    color: #a78bfa !important;
    text-decoration: none !important;
}

/* Premium Pill Delete Action Button styles */
.btn-premium-delete {
    background-color: transparent !important;
    border: 1.5px solid #ff3366 !important;
    color: #ff3366 !important;
    border-radius: 50px !important;
    padding: 5px 16px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    font-family: 'Outfit', sans-serif !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
    outline: none !important;
    box-shadow: none !important;
    line-height: 1 !important;
}
.btn-premium-delete i {
    font-size: 0.88rem !important;
    line-height: 1 !important;
}
.btn-premium-delete span {
    line-height: 1 !important;
    font-weight: 700 !important;
}
.btn-premium-delete:hover {
    background-color: rgba(255, 51, 102, 0.05) !important;
    box-shadow: 0 4px 10px rgba(255, 51, 102, 0.12) !important;
    transform: translateY(-0.5px) !important;
}
.btn-premium-delete:active {
    transform: scale(0.97) !important;
}

/* Table rows dynamic hover theme styling */
tbody tr:hover {
    background-color: rgba(108, 92, 231, 0.02) !important;
}
html[data-admin-theme="dark"] tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.015) !important;
}
html[data-admin-theme="dark"] .table-hover tbody tr:hover {
    color: inherit !important;
}
html[data-admin-theme="dark"] td {
    color: #cbd5e1 !important;
}
</style>
@endsection
