@extends('layouts.admin_noble')
@section('title', 'Customer Management')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h3 mb-0 text-gray-800 fw-bold" style="font-family: 'Outfit', sans-serif;">Customer Management</h2>
        <p class="text-muted mb-0">View registered customers, apply filters, manage user profiles and grant personalized offers</p>
    </div>
    <div class="col-md-6 text-right d-none d-md-block">
        <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.8rem;">Total: {{ $customers->total() }} Customers</span>
    </div>
</div>

{{-- Spacious Curved Filtering Panel --}}
<div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden" style="border-radius: 18px !important;">
    <div class="card-body p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="row align-items-end g-3">
            {{-- Keep Sort By state --}}
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">

            <div class="col-lg-4 col-md-12 filter-col-mobile">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                    <i class="bi bi-search text-primary mr-2"></i> Search Customer
                </label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Search name, email, phone...">
                </div>
            </div>
            
            <div class="col-6 col-lg-3 col-md-6 filter-col-mobile">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                    <i class="bi bi-receipt text-success mr-2"></i> Min Orders
                </label>
                <input type="number" name="min_orders" class="form-control" value="{{ $minOrders }}" placeholder="e.g. 3" min="0">
            </div>

            <div class="col-6 col-lg-3 col-md-6 filter-col-mobile">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                    <i class="bi bi-cash-coin text-warning mr-2"></i> Min Spent (£)
                </label>
                <input type="number" step="0.01" name="min_spent" class="form-control" value="{{ $minSpent }}" placeholder="e.g. 50.00" min="0">
            </div>

            <div class="col-lg-2 col-md-12 text-right d-flex gap-2 mt-2 mt-lg-0">
                <button type="submit" class="btn btn-primary flex-grow-1 d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-search mr-2"></i> Search
                </button>
                @if($search || $minOrders || $minSpent || $sortBy !== 'newest')
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Reset Filters" style="border-radius: 30px !important;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Customer Table Container --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="border-radius: 18px !important;">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
            <i class="bi bi-people text-primary mr-2" style="font-size: 1.15rem;"></i>
            Customer Directory
        </h5>
        
        {{-- Sort By Dropdown --}}
        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex align-items-center gap-2" id="sort-form">
            @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
            @if($minOrders)<input type="hidden" name="min_orders" value="{{ $minOrders }}">@endif
            @if($minSpent)<input type="hidden" name="min_spent" value="{{ $minSpent }}">@endif
            <input type="hidden" name="sort_by" id="sort_by_input" value="{{ $sortBy }}">

            <label class="small text-muted font-weight-bold mb-0 mr-2 d-none d-sm-block">Sort By:</label>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-sort-down text-info mr-2" style="font-size: 0.88rem;"></i>
                    <span>
                        @switch($sortBy)
                            @case('oldest') Joined Date: Oldest @break
                            @case('orders_desc') Most Orders @break
                            @case('orders_asc') Least Orders @break
                            @case('spent_desc') Most Spent @break
                            @case('spent_asc') Least Spent @break
                            @default Joined Date: Newest
                        @endswitch
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 p-1" aria-labelledby="sortDropdown" style="border-radius: 12px !important; border: 1.5px solid rgba(0,0,0,0.05) !important; font-family: 'Inter', sans-serif;">
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'newest' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;newest&quot;]" style="border-radius: 8px !important; font-weight: 600;">Joined Date: Newest</a>
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'oldest' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;oldest&quot;]" style="border-radius: 8px !important; font-weight: 600;">Joined Date: Oldest</a>
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'orders_desc' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;orders_desc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Most Orders</a>
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'orders_asc' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;orders_asc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Least Orders</a>
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'spent_desc' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;spent_desc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Most Spent</a>
                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ $sortBy === 'spent_asc' ? 'active' : '' }}" href="#" data-prevent data-call="submitSort" data-args="[&quot;spent_asc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Least Spent</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(0, 0, 0, 0.04);">
                        <th class="ps-4 py-3 pl-4 align-middle">Customer</th>
                        <th class="py-3 align-middle d-none d-md-table-cell">Email</th>
                        <th class="py-3 align-middle d-none d-lg-table-cell">Phone</th>
                        <th class="py-3 align-middle d-none d-sm-table-cell">Age</th>
                        <th class="py-3 align-middle">Orders Count</th>
                        <th class="py-3 align-middle">Total Spent</th>
                        <th class="py-3 align-middle d-none d-sm-table-cell">Joined Date</th>
                        <th class="py-3 align-middle text-right pr-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.02); transition: all 0.2s ease;">
                        <td class="ps-4 pl-4 py-3 align-middle font-weight-bold">
                            <div class="d-flex align-items-center">
                                <div class="wd-42 h-42 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm" style="font-size: 1rem; min-width: 42px; height: 42px;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-dark-theme-aware text-hover-primary text-decoration-none font-weight-bold" style="font-size: 0.88rem;" title="View Customer Details">
                                        {{ $customer->name }}
                                    </a>
                                    @if($customer->isUnder16()) 
                                        <span class="badge bg-soft-danger py-1 px-2 ml-2" style="font-size: 0.65rem; border-radius: 20px;">UNDER 16</span>
                                    @endif
                                    @if($customer->offer_discount_percentage > 0)
                                        <span class="badge bg-soft-success py-1 px-2 ml-2" style="font-size: 0.65rem; border-radius: 20px;" title="Scope: {{ ucfirst($customer->offer_scope) }}">
                                            <i class="bi bi-gift-fill mr-1"></i> {{ round($customer->offer_discount_percentage) }}% Offer
                                        </span>
                                    @endif
                                    {{-- Mobile-only compact customer info --}}
                                    <div class="d-block d-md-none mt-1" style="font-size: 0.76rem; font-weight: normal; line-height: 1.3;">
                                        <span class="text-muted d-block">{{ $customer->email }}</span>
                                        @if($customer->phone)
                                            <span class="text-muted d-block mt-0.5"><i class="bi bi-phone mr-1"></i> {{ $customer->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-muted small d-none d-md-table-cell" style="font-size: 0.82rem;">
                            {{ $customer->email }}
                        </td>
                        <td class="align-middle text-theme-dark-bold small d-none d-lg-table-cell" style="font-size: 0.82rem;">
                            {{ $customer->phone ?? '—' }}
                        </td>
                        <td class="align-middle d-none d-sm-table-cell">
                            @if($customer->dob)
                                <span class="font-weight-bold" style="font-size: 0.85rem;">{{ $customer->age }}</span> <small class="text-muted">yrs</small>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <span class="badge px-3 py-1.5 bg-soft-secondary font-weight-bold" style="font-size: 0.72rem; border-radius: 20px;">
                                {{ $customer->orders_count }} Orders
                            </span>
                        </td>
                        <td class="align-middle font-weight-bold text-primary" style="font-size: 0.88rem;">
                            £{{ number_format($customer->total_spent ?? 0, 2) }}
                        </td>
                        <td class="align-middle text-muted small d-none d-sm-table-cell" style="font-size: 0.82rem;">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                        <td class="text-right pr-4 align-middle">
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline-block" data-confirm="Are you sure you want to permanently delete this customer? This will also remove their associated profile records. This action cannot be undone.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-premium-delete">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline">Delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-users text-muted mb-3" style="font-size: 2.5rem;"></i>
                                <p class="text-muted font-weight-bold" style="font-size: 0.9rem;">No matching customers registered.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2 px-4 pb-4">
            <div class="text-muted small" style="font-size: 0.8rem;">
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} entries
            </div>
            <div>
                {{ $customers->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Curve styles */
.rounded-4 {
    border-radius: 18px !important;
}

/* Soft-colored badges for light mode */
.bg-soft-primary { background: rgba(116, 48, 137,0.1) !important; color: #743089 !important; }
.bg-soft-success { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-warning { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-danger { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }

/* Soft-colored badges for dark mode */
html[data-admin-theme="dark"] .bg-soft-primary { background: rgba(167, 139, 250, 0.15) !important; color: #a78bfa !important; }
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
    color: #a78bfa !important;
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

/* Premium Outlined Delete Action Button (Mirroring Reference Screenshot) */
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

/* Forms & curved controls */
.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
}
.form-control:focus {
    border-color: #743089 !important;
    box-shadow: 0 0 0 3.5px rgba(116, 48, 137, 0.15) !important;
}
html[data-admin-theme="dark"] .form-control {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167, 139, 250, 0.2) !important;
}

/* Premium Buttons */
.btn {
    border-radius: 30px !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #743089, #a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(116, 48, 137, 0.2) !important;
    color: #ffffff !important;
}
.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(116, 48, 137, 0.3) !important;
}

.gap-2 {
    gap: 8px !important;
}

/* Custom Dropdown Toggle Button with rounded-pill style */
#sortDropdown {
    font-size: 0.76rem !important;
    padding: 6px 16px !important;
    border-radius: 30px !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    border: 1.5px solid rgba(0, 0, 0, 0.08) !important;
    background-color: #ffffff !important;
    color: #475569 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: none !important;
}
#sortDropdown:hover {
    background-color: #f8fafc !important;
    border-color: rgba(0, 0, 0, 0.15) !important;
    color: #1e293b !important;
}

/* Dark theme overrides for sortDropdown toggle button */
html[data-admin-theme="dark"] #sortDropdown {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] #sortDropdown:hover {
    background-color: rgba(255, 255, 255, 0.04) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
}

/* Premium Dropdown listbox and option items with curved edges */
.dropdown-menu {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08) !important;
    border-radius: 12px !important;
    border: 1.5px solid rgba(116, 48, 137, 0.06) !important;
    padding: 6px !important;
}
.dropdown-menu .dropdown-item {
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    color: #475569 !important;
    padding: 7px 14px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}
.dropdown-menu .dropdown-item:hover {
    background-color: rgba(116, 48, 137, 0.06) !important;
    color: #743089 !important;
}
.dropdown-menu .dropdown-item.active {
    background: linear-gradient(135deg, #743089, #a78bfa) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}

/* Dark theme overrides for custom dropdown */
html[data-admin-theme="dark"] .dropdown-menu {
    background-color: #0c1427 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
html[data-admin-theme="dark"] .dropdown-menu .dropdown-item {
    color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] .dropdown-menu .dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.04) !important;
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .dropdown-menu .dropdown-item.active {
    background: linear-gradient(135deg, #a78bfa, #8b5cf6) !important;
    color: #0c1427 !important;
}

/* Mobile responsive adjustments for Customer Management */
@media (max-width: 991px) {
    /* Filter drawer spacing - vertical gaps between columns on mobile */
    .filter-col-mobile {
        margin-bottom: 16px !important;
    }
    
    /* Ensure proper spacing between label text and text box */
    .card-body form label {
        margin-bottom: 8px !important;
    }
    
    /* Spacing between header title and sorting option */
    .card-header.d-flex {
        padding-bottom: 12px !important;
        gap: 12px !important;
    }
}

@media (max-width: 767px) {
    .card-header.d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
        padding-bottom: 16px !important;
    }
    
    #sort-form {
        width: 100% !important;
        justify-content: space-between !important;
    }
}
</style>

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
    function submitSort(val) {
        document.getElementById('sort_by_input').value = val;
        document.getElementById('sort-form').submit();
    }
</script>
@endpush
@endsection
