@extends('layouts.admin_noble')
@section('title', 'Customer Reviews Management')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h3 mb-0 text-gray-800 fw-bold" style="font-family: 'Outfit', sans-serif;">Customer Reviews</h2>
        <p class="text-muted mb-0">Monitor, moderate, and reply to product reviews</p>
    </div>
    <div class="col-md-6 text-right d-none d-md-block">
        <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.8rem;">Total: {{ $reviews->total() }} Reviews</span>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="border-radius: 18px !important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(0, 0, 0, 0.04);">
                        <th class="ps-4 py-3 pl-4 align-middle">Review ID</th>
                        <th class="py-3 align-middle">Product</th>
                        <th class="py-3 align-middle">Customer</th>
                        <th class="py-3 align-middle">Rating</th>
                        <th class="py-3 align-middle">Status</th>
                        <th class="py-3 align-middle text-right pr-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.02); transition: all 0.2s ease;">
                        <td class="ps-4 pl-4 align-middle font-weight-bold">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="font-weight-bold text-primary" style="font-size: 0.85rem;" title="Moderate Review">
                                REV-{{ str_pad($review->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td class="align-middle">
                            <h6 class="mb-0 fw-bold truncate-1" style="max-width: 220px;" title="{{ $review->product->name }}">
                                <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-dark-theme-aware text-hover-primary text-decoration-none font-weight-bold" style="font-size: 0.88rem;">
                                    {{ $review->product->name }}
                                </a>
                            </h6>
                        </td>
                        <td class="align-middle fw-bold text-theme-dark-bold" style="font-size: 0.85rem;">
                            {{ $review->user->name }}
                        </td>
                        <td class="align-middle">
                            <div class="text-warning d-flex gap-0.5" style="font-size: 0.85rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }} mr-0.5"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="align-middle">
                            @if($review->is_approved)
                                <span class="badge px-3 py-1.5 bg-soft-success font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Approved</span>
                            @else
                                <span class="badge px-3 py-1.5 bg-soft-secondary font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">Hidden</span>
                            @endif
                        </td>
                        <td class="text-right pr-4 align-middle">
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline-block" data-confirm="Are you sure you want to permanently delete this customer review?">
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
                                <i class="bi bi-chat-square-text text-muted mb-3" style="font-size: 2.5rem;"></i>
                                <p class="text-muted font-weight-bold" style="font-size: 0.9rem;">No reviews found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2 px-4 pb-4">
            <div class="text-muted small" style="font-size: 0.8rem;">
                Showing {{ $reviews->firstItem() ?? 0 }} to {{ $reviews->lastItem() ?? 0 }} of {{ $reviews->total() }} entries
            </div>
            <div>
                {{ $reviews->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Soft-colored badges for light mode */
.bg-soft-primary { background: rgba(116, 48, 137,0.1) !important; color: #743089 !important; }
.bg-soft-success { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-warning { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-danger { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }

/* Soft-colored badges for dark mode */
html[data-admin-theme="dark"] .bg-soft-primary { background: rgba(164, 95, 191, 0.15) !important; color: #A45FBF !important; }
html[data-admin-theme="dark"] .bg-soft-success { background: rgba(52, 211, 153, 0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-warning { background: rgba(251, 191, 36, 0.15) !important; color: #fbbf24 !important; }
html[data-admin-theme="dark"] .bg-soft-danger { background: rgba(248, 113, 113, 0.15) !important; color: #f87171 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148, 163, 184, 0.15) !important; color: #94a3b8 !important; }

/* Hover links */
.text-hover-primary:hover {
    color: #743089 !important;
    text-decoration: none !important;
}
html[data-admin-theme="dark"] .text-hover-primary:hover {
    color: #A45FBF !important;
    text-decoration: none !important;
}

html[data-admin-theme="light"] .text-dark-theme-aware {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .text-dark-theme-aware {
    color: #cbd5e1 !important;
}

tbody tr:hover {
    background-color: rgba(116, 48, 137, 0.015) !important;
}
html[data-admin-theme="dark"] tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.01) !important;
}
html[data-admin-theme="dark"] td {
    color: #cbd5e1 !important;
}

/* Premium Pill Delete Action Button styles (Mirroring Reference Screenshot!) */
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

.mr-0.5 { margin-right: 0.12rem; }
</style>
@endsection
