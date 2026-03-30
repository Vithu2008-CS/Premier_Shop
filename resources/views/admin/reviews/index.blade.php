@extends('layouts.admin_noble')
@section('title', 'Customer Reviews Management')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Customer Reviews</h2>
        <p class="text-muted mb-0">Monitor, moderate, and reply to product reviews</p>
    </div>
</div>

<div class="card shadow rounded-4 border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Product</th>
                        <th>Customer</th>
                        <th>Rating & Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $review->product->images[0] ?? '' }}" class="rounded shadow-sm" style="width:40px;height:40px;object-fit:cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold truncate-1" style="max-width: 200px;" title="{{ $review->product->name }}">
                                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" class="text-dark text-decoration-none">
                                            {{ $review->product->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">ID: #{{ $review->product_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $review->user->name }}</div>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </td>
                        <td style="max-width:300px;">
                            <div class="text-warning mb-1" style="font-size: 0.8rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <h6 class="fw-bold mb-1 truncate-1 fs-6">{{ $review->title ?? 'No Title' }}</h6>
                            <p class="mb-1 text-muted small truncate-2">{{ $review->comment }}</p>
                            @if($review->photos)
                                <span class="badge bg-secondary rounded-pill"><i class="bi bi-images me-1"></i> {{ count($review->photos) }} img</span>
                            @endif
                            @if($review->admin_reply)
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mt-1 d-ib"><i class="bi bi-reply-fill"></i> Replied</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.reviews.toggleApproval', $review) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $review->is_approved ? 'success text-white' : 'secondary text-white' }} rounded-pill border-0 shadow-sm" style="width:100px;">
                                    {{ $review->is_approved ? 'Approved' : 'Hidden' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $review->created_at->format('M d, Y') }}</span>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $review->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-light btn-sm rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Reply">
                                <i class="bi bi-reply text-primary"></i>
                            </button>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm rounded-circle" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>

                            {{-- Reply Modal --}}
                            <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold">Reply Profile</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.reviews.reply', $review) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="p-3 bg-light rounded-3 mb-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <strong>{{ $review->user->name }}</strong>
                                                        <div class="text-warning small">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="mb-0 text-muted small fst-italic">"{{ $review->comment ?? 'No comment provided.' }}"</p>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Your Official Reply (Public)</label>
                                                    <textarea name="admin_reply" class="form-control" rows="4" placeholder="Write a response visible to all customers...">{{ $review->admin_reply }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4">Save Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-text fs-1 mb-3 d-block opacity-50"></i>
                            <p class="mb-0">No reviews found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-3 px-4">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
