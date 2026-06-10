@extends('layouts.admin_noble')
@section('title', 'Moderate Customer - ' . $customer->name)

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;"> {{-- Leaves space for the floating action bar --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
        </ol>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left mr-2" style="font-size: 0.85rem;"></i> Back to List
        </a>
    </nav>

    <div class="row">
        {{-- Left Column: Customer details, Saved Addresses, and Purchased Items --}}
        <div class="col-lg-8 mb-4">
            <div class="d-flex flex-column gap-4 w-100">
                
                {{-- Core Metadata Card --}}
                <div class="card border-0 shadow-sm theme-card-bg mb-4" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-person-bounding-box text-primary mr-2" style="font-size: 1.25rem;"></i>
                                Customer Profile Details
                            </h5>
                        </div>

                        <div class="customer-profile-banner d-flex align-items-center p-4 rounded-4 mb-4" style="background: rgba(108,92,231,0.03); border: 1.5px solid rgba(108,92,231,0.06);">
                            <div class="customer-profile-avatar wd-64 h-64 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-4 shadow-sm" style="font-size: 1.5rem; min-width: 64px; height: 64px;">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="mb-1 text-theme-dark-bold fw-700" style="font-size: 1.15rem; font-family: 'Outfit', sans-serif;">{{ $customer->name }}</h4>
                                <p class="text-muted small mb-0">{{ $customer->email }}</p>
                                <div class="d-flex gap-2 mt-2 customer-profile-badges">
                                    <span class="badge bg-soft-secondary font-weight-bold" style="border-radius: 10px;">{{ $customer->role?->display_name ?? 'Customer' }}</span>
                                    @if($customer->isUnder16()) 
                                        <span class="badge bg-soft-danger font-weight-bold" style="border-radius: 10px;">UNDER 16</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Metadata Grid --}}
                        <div class="row g-4 mb-4">
                            <div class="col-6 col-md-3">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Phone</span>
                                <span class="text-theme-dark-bold font-weight-semibold" style="font-size: 0.88rem;">{{ $customer->phone ?? '—' }}</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Date of Birth</span>
                                <span class="text-theme-dark-bold font-weight-semibold" style="font-size: 0.88rem;">
                                    {{ $customer->dob ? $customer->dob->format('d M Y') : '—' }}
                                    @if($customer->dob) <small class="text-muted">({{ $customer->age }} yrs)</small> @endif
                                </span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Orders Count</span>
                                <span class="badge bg-soft-success font-weight-extrabold mt-1" style="font-size: 0.82rem; border-radius: 20px;">
                                    {{ $customer->orders_count }} Orders
                                </span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Total Spent</span>
                                <span class="text-primary font-weight-extrabold" style="font-size: 1.1rem; font-family: 'Outfit', sans-serif;">£{{ number_format($totalSpent, 2) }}</span>
                            </div>
                        </div>

                        <div class="row g-4 mt-1 border-top pt-3" style="opacity: 0.85;">
                            <div class="col-6">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Account Created</span>
                                <span class="text-muted small">{{ $customer->created_at->format('d F Y \a\t H:i') }} ({{ $customer->created_at->diffForHumans() }})</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small d-block mb-1 font-weight-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.68rem;">Loyalty Points Balance</span>
                                <span class="text-success font-weight-extrabold d-flex align-items-center" style="font-size: 0.95rem;">
                                    <i class="bi bi-award-fill mr-1.5" style="font-size: 1.05rem;"></i>
                                    {{ $customer->loyalty_points }} pts
                                </span>
                            </div>
                        </div>

                        {{-- Saved Shipping Addresses Section --}}
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                <i class="bi bi-geo-alt-fill text-danger mr-2" style="font-size: 1.05rem;"></i>
                                Saved Shipping Addresses ({{ $customer->addresses->count() }})
                            </h6>
                            <div class="row g-3">
                                @forelse($customer->addresses as $address)
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 border h-100 d-flex flex-column justify-content-between position-relative" style="background: rgba(0,0,0,0.008); border-color: rgba(0,0,0,0.05) !important;">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge bg-soft-primary px-2.5 py-1 font-weight-bold" style="font-size: 0.68rem; border-radius: 8px;">{{ ucfirst($address->address_type ?? 'Home') }}</span>
                                                    @if($address->is_default)
                                                        <span class="badge bg-soft-success px-2.5 py-1 font-weight-bold" style="font-size: 0.68rem; border-radius: 8px;"><i class="bi bi-check-circle mr-1"></i> Default</span>
                                                    @endif
                                                </div>
                                                <h6 class="mb-1 fw-bold text-theme-dark-bold" style="font-size: 0.85rem;">{{ $address->first_name }} {{ $address->last_name }}</h6>
                                                <p class="text-muted small mb-0 lh-base">{{ $address->address_line1 }}</p>
                                                @if($address->address_line2)<p class="text-muted small mb-0">{{ $address->address_line2 }}</p>@endif
                                                <p class="text-muted small mb-0">{{ $address->city }}, {{ $address->postcode }}</p>
                                            </div>
                                            <div class="mt-2.5 border-top pt-2" style="font-size: 0.72rem;">
                                                <span class="text-muted"><i class="bi bi-phone mr-1"></i> {{ $address->phone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="p-3 text-center text-muted border rounded-3" style="font-size: 0.8rem; background: rgba(0, 0, 0, 0.02); border-color: rgba(0,0,0,0.05) !important;">
                                            <i class="bi bi-info-circle mr-2"></i> No saved shipping addresses found for this customer.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Purchased Items History Table Card --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif; font-size: 1.05rem;">
                                <i class="bi bi-bag-check-fill text-success mr-2" style="font-size: 1.15rem;"></i>
                                Purchase History ({{ $purchasedItems->total() }} items)
                            </h5>
                            
                            {{-- Purchase Sorting Dropdown --}}
                            <form action="{{ route('admin.customers.show', $customer) }}" method="GET" class="d-flex align-items-center gap-2" id="purchase-sort-form">
                                <input type="hidden" name="purchase_sort" id="purchase_sort_input" value="{{ $purchaseSort }}">

                                <label class="small text-muted font-weight-bold mb-0 mr-2 d-none d-sm-block">Sort By:</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center" type="button" id="purchaseSortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.76rem; padding: 5px 14px; border-radius: 30px !important; font-family: 'Outfit', sans-serif; font-weight: 700; border: 1.5px solid rgba(0, 0, 0, 0.08) !important; background-color: var(--input-bg, #ffffff);">
                                        <i class="bi bi-sort-down text-success mr-1.5" style="font-size: 0.88rem;"></i>
                                        <span>
                                            @switch($purchaseSort)
                                                @case('oldest') Date: Oldest @break
                                                @case('qty_desc') Most Quantity @break
                                                @case('qty_asc') Least Quantity @break
                                                @case('total_desc') Most Line Total @break
                                                @case('total_asc') Least Line Total @break
                                                @default Date: Newest
                                            @endswitch
                                        </span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 p-1" aria-labelledby="purchaseSortDropdown" style="border-radius: 12px !important; border: 1.5px solid rgba(0,0,0,0.05) !important; font-family: 'Inter', sans-serif;">
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'newest' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;newest&quot;]" style="border-radius: 8px !important; font-weight: 600;">Date: Newest</a>
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'oldest' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;oldest&quot;]" style="border-radius: 8px !important; font-weight: 600;">Date: Oldest</a>
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'qty_desc' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;qty_desc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Most Quantity</a>
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'qty_asc' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;qty_asc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Least Quantity</a>
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'total_desc' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;total_desc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Most Line Total</a>
                                        <a class="dropdown-item rounded-3 py-2 px-3 small {{ $purchaseSort === 'total_asc' ? 'active' : '' }}" href="#" data-prevent data-call="submitPurchaseSort" data-args="[&quot;total_asc&quot;]" style="border-radius: 8px !important; font-weight: 600;">Least Line Total</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive rounded-4 border overflow-hidden" style="border-color: rgba(0,0,0,0.06) !important;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-uppercase small text-muted font-weight-bold" style="background: rgba(0,0,0,0.015); border-bottom: 2px solid rgba(0,0,0,0.04); letter-spacing: 0.5px; font-size: 0.72rem;">
                                        <th class="pl-4 py-3">Product Name</th>
                                        <th class="py-3 d-none d-sm-table-cell">Category</th>
                                        <th class="text-center py-3">Quantity</th>
                                        <th class="text-right py-3 d-none d-md-table-cell">Unit Price</th>
                                        <th class="text-right pr-4 py-3">Line Total</th>
                                        <th class="py-3 d-none d-sm-table-cell">Purchase Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchasedItems as $item)
                                        <tr class="table-row-hover-effect" style="border-bottom: 1px solid rgba(0,0,0,0.025);">
                                            <td class="pl-4 py-3 align-middle font-weight-bold text-theme-dark-bold" style="font-size: 0.88rem;">
                                                @if($item->product)
                                                    <a href="{{ route('products.show', $item->product->slug) }}" target="_blank" class="text-dark-theme-aware text-hover-primary text-decoration-none">
                                                        {{ $item->product->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted italic">[Deleted Product]</span>
                                                @endif
                                                {{-- Mobile-only compact Purchased Item info --}}
                                                <div class="d-block d-sm-none mt-1" style="font-size: 0.74rem; font-weight: normal; line-height: 1.35;">
                                                    <span class="text-muted d-block">Category: {{ $item->product?->category?->name ?? '—' }}</span>
                                                    <span class="text-muted d-block mt-0.5">Date: {{ $item->created_at->format('d M Y') }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 align-middle text-muted small d-none d-sm-table-cell" style="font-size: 0.82rem;">
                                                {{ $item->product?->category?->name ?? '—' }}
                                            </td>
                                            <td class="text-center py-3 font-weight-bold text-theme-dark-bold" style="font-size: 0.88rem;">{{ $item->quantity }}</td>
                                            <td class="text-right py-3 text-muted d-none d-md-table-cell" style="font-size: 0.82rem;">£{{ number_format($item->price, 2) }}</td>
                                            <td class="text-right pr-4 py-3 font-weight-bold text-primary" style="font-size: 0.88rem;">£{{ number_format($item->quantity * $item->price, 2) }}</td>
                                            <td class="py-3 align-middle text-muted small d-none d-sm-table-cell" style="font-size: 0.82rem;">
                                                <a href="{{ route('admin.orders.show', $item->order) }}" class="font-weight-bold text-primary">
                                                    {{ $item->order->order_number }}
                                                </a>
                                                <span class="d-block mt-0.5" style="font-size: 0.72rem;">{{ $item->created_at->format('d M Y') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted small">
                                                <i class="bi bi-info-circle mr-2"></i> No purchased items matching this category filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($purchasedItems->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2 px-2">
                                <div class="text-muted small" style="font-size: 0.76rem;">
                                    Showing {{ $purchasedItems->firstItem() ?? 0 }} to {{ $purchasedItems->lastItem() ?? 0 }} of {{ $purchasedItems->total() }} items
                                </div>
                                <div>
                                    {{ $purchasedItems->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Right Column: Admin Moderation Form (Role & Personalized Offer) --}}
        <div class="col-lg-4">
            
            <form action="{{ route('admin.customers.update', $customer) }}" method="POST" id="customer-update-form" class="d-flex flex-column gap-4 w-100">
                @csrf
                @method('PUT')

                {{-- Hidden input to protect role validation --}}
                <input type="hidden" name="role_id" value="{{ $customer->role_id }}">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-0" role="alert" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.12); color: #10b981; padding: 0.75rem 1rem;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill mr-2" style="font-size: 1rem;"></i>
                            <span class="font-weight-bold" style="font-size: 0.8rem;">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-0" role="alert" style="border-radius: 12px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; padding: 0.75rem 1rem;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill mr-2" style="font-size: 1rem;"></i>
                            <span class="font-weight-bold" style="font-size: 0.8rem;">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Card Section: Give Personalized Offer --}}
                <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-gift-fill text-success mr-2" style="font-size: 1.25rem;"></i>
                                Give Personalized Offer
                            </h5>
                        </div>

                        <p class="text-muted small mb-4" style="line-height: 1.45;">
                            Configure a dynamic, customer-specific discount rate automatically applied to eligible products in their cart upon checkout.
                        </p>

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2">Offer Discount Percentage (%)</label>
                            <input type="number" step="0.01" name="offer_discount_percentage" class="form-control rounded-3" value="{{ old('offer_discount_percentage', $customer->offer_discount_percentage) }}" placeholder="e.g. 15.00" min="0" max="100" style="height: 42px !important;">
                            <small class="form-text text-muted" style="font-size: 0.7rem;">Leave blank or enter 0 to deactivate the discount.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-muted font-weight-bold mb-2">Offer Scope</label>
                            <input type="hidden" name="offer_scope" id="offerScopeInput" value="{{ old('offer_scope', $customer->offer_scope ?? 'all') }}">
                            <div class="dropdown w-100">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-between" type="button" id="scopeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 42px !important; border-radius: 12px !important; text-align: left; font-size: 0.84rem; font-weight: 550; border: 1.5px solid rgba(0, 0, 0, 0.08) !important; background-color: var(--input-bg, #ffffff); color: var(--input-color, #1e293b);">
                                    <span id="selectedScopeLabel">
                                        @if(($customer->offer_scope ?? 'all') === 'selected') Selected Products Only @else Entire Product Catalog @endif
                                    </span>
                                </button>
                                <div class="dropdown-menu w-100 shadow-sm border-0 p-1" aria-labelledby="scopeDropdown" style="border-radius: 12px !important; border: 1.5px solid rgba(0,0,0,0.05) !important;">
                                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ ($customer->offer_scope ?? 'all') === 'all' ? 'active' : '' }}" href="#" data-prevent data-call="selectScope" data-args="[&quot;all&quot;]" style="border-radius: 8px !important; font-weight: 600;">Entire Product Catalog</a>
                                    <a class="dropdown-item rounded-3 py-2 px-3 small {{ ($customer->offer_scope ?? 'all') === 'selected' ? 'active' : '' }}" href="#" data-prevent data-call="selectScope" data-args="[&quot;selected&quot;]" style="border-radius: 8px !important; font-weight: 600;">Selected Products Only</a>
                                </div>
                            </div>
                        </div>

                        {{-- Selected Products scrollable list container --}}
                        <div class="form-group mb-0" id="productListContainer" style="display: none;">
                            <label class="small text-muted font-weight-bold mb-2">Choose Selected Products</label>
                            <input type="text" id="productSearch" class="form-control mb-2" placeholder="Search product name..." style="height: 36px !important; font-size: 0.8rem;">
                            
                            <div class="p-3 rounded-3 border overflow-auto custom-scrollbar" style="max-height: 250px; background: rgba(0,0,0,0.01);">
                                @foreach($products as $p)
                                    <div class="product-checkbox-item mb-2.5" data-name="{{ strtolower($p->name) }}">
                                        <label class="premium-custom-checkbox text-theme-dark-bold small mb-0" style="font-weight: 500; cursor: pointer; user-select: none; width: 100%;">
                                            <input type="checkbox" name="offer_product_ids[]" value="{{ $p->id }}" id="prod-{{ $p->id }}" {{ is_array($customer->offer_product_ids) && in_array($p->id, $customer->offer_product_ids) ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                            <span class="ml-1">{{ $p->name }} <span class="text-muted" style="font-size: 0.76rem;">(£{{ number_format($p->price, 2) }})</span></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loyalty & Account Insights Card --}}
                <div class="card border-0 shadow-sm theme-card-bg w-100" style="border-radius: 18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-award-fill text-warning mr-2" style="font-size: 1.25rem;"></i>
                                Loyalty & Account Insights
                            </h5>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(217, 119, 6, 0.08)); border: 1.5px solid rgba(245, 158, 11, 0.15);">
                                <div>
                                    <span class="text-uppercase small font-weight-bold d-block text-warning mb-0.5" style="letter-spacing: 0.5px; font-size: 0.65rem;">Loyalty Tier</span>
                                    <h6 class="mb-0 font-weight-bold text-theme-dark-bold" style="font-size: 0.9rem;">
                                        @if($customer->loyalty_points >= 500)
                                            VIP Platinum Member
                                        @elseif($customer->loyalty_points >= 200)
                                            VIP Gold Member
                                        @elseif($customer->loyalty_points >= 50)
                                            Active Silver Member
                                        @else
                                            Bronze Member
                                        @endif
                                    </h6>
                                </div>
                                <div class="text-warning mr-2" style="font-size: 1.4rem; line-height: 1;">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                            </div>

                            {{-- Metrics Grid --}}
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-3 border h-100" style="background: rgba(0,0,0,0.005); border-color: rgba(0,0,0,0.04) !important;">
                                        <span class="text-muted small d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.3px;">Points Balance</span>
                                        <h4 class="text-warning fw-800 mb-0" style="font-family: 'Outfit', sans-serif;">{{ $customer->loyalty_points }} <small class="small text-muted font-weight-semibold" style="font-size: 0.68rem;">pts</small></h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3 border h-100" style="background: rgba(0,0,0,0.005); border-color: rgba(0,0,0,0.04) !important;">
                                        <span class="text-muted small d-block mb-1 text-uppercase font-weight-bold" style="font-size: 0.62rem; letter-spacing: 0.3px;">Avg Order Value</span>
                                        <h4 class="text-primary fw-800 mb-0" style="font-family: 'Outfit', sans-serif;">
                                            £{{ ($customer->orders_count ?? $customer->orders()->count()) > 0 ? number_format($totalSpent / ($customer->orders_count ?? $customer->orders()->count()), 2) : '0.00' }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            {{-- Engagement Timeline --}}
                            <div class="mt-4 border-top pt-4">
                                <span class="text-muted small d-block mb-3 font-weight-bold text-uppercase" style="letter-spacing: 0.6px; font-size: 0.68rem; opacity: 0.85;">Engagement Timeline</span>
                                <div class="d-flex flex-column" style="font-size: 0.82rem;">
                                    
                                    {{-- Customer Since --}}
                                    <div class="d-flex align-items-center justify-content-between py-3 timeline-divider">
                                        <span class="text-muted d-flex align-items-center">
                                            <i class="bi bi-clock-history text-muted mr-2" style="font-size: 1rem;"></i> 
                                            Customer Since
                                        </span>
                                        <span class="text-theme-dark-bold font-weight-bold">{{ $customer->created_at->format('F Y') }}</span>
                                    </div>

                                    {{-- Lifetime Volume --}}
                                    <div class="d-flex align-items-center justify-content-between py-3 timeline-divider">
                                        <span class="text-muted d-flex align-items-center">
                                            <i class="bi bi-cart3 text-success mr-2" style="font-size: 1rem;"></i> 
                                            Lifetime Volume
                                        </span>
                                        <span class="text-theme-dark-bold font-weight-bold">{{ $customer->orders_count ?? $customer->orders()->count() }} Orders</span>
                                    </div>

                                    {{-- Security Status --}}
                                    <div class="d-flex align-items-center justify-content-between py-3">
                                        <span class="text-muted d-flex align-items-center">
                                            <i class="bi bi-shield-check text-info mr-2" style="font-size: 1rem;"></i> 
                                            Security Status
                                        </span>
                                        <span class="badge bg-soft-success font-weight-bold px-2.5 py-1" style="border-radius: 8px; font-size: 0.72rem;">Active Profile</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

{{-- Modern Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3 border shadow-lg rounded-pill">
    <div class="d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
        <span class="live-indicator me-1"></span>
        <div class="d-flex align-items-baseline gap-2">
            <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size: 0.68rem; letter-spacing: 0.5px; font-weight: 600; white-space: nowrap; line-height: 1;">Customer Profile:</span>
            <span class="fw-bold text-nowrap floating-bar-title" style="font-size: 0.85rem; line-height: 1;">{{ $customer->name }}</span>
        </div>
    </div>
    <div class="button-group">
        <button type="button" class="btn btn-danger" data-confirm="Are you sure you want to permanently delete this customer? This action cannot be undone." data-submit-form="delete-customer-form">
            <i class="bi bi-trash mr-2"></i> Delete
        </button>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-light">
            Cancel
        </a>
        <button type="submit" form="customer-update-form" class="btn btn-primary">
            <i class="bi bi-check2-circle mr-2"></i> Save
        </button>
    </div>
</div>

{{-- Hidden Deletion Form --}}
<form id="delete-customer-form" action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid {
    font-family: 'Inter', sans-serif;
}

/* Explicit curve utilities */
.rounded-3 { border-radius: 12px !important; }
.rounded-4 { border-radius: 18px !important; }
.p-3.5 { padding: 1.15rem !important; }
.gap-2 { gap: 8px !important; }
.gap-4 { gap: 24px !important; }
.timeline-divider {
    border-bottom: 1.5px solid rgba(0, 0, 0, 0.04) !important;
}
html[data-admin-theme="dark"] .timeline-divider {
    border-color: rgba(255, 255, 255, 0.06) !important;
}
.mr-1.5 { margin-right: 0.35rem; }
.mr-2 { margin-right: 0.5rem; }
.p-2.5 { padding: 0.6rem !important; }
.leading-normal { line-height: 1.5; }
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }

/* Soft background styles */
.bg-soft-primary { background: rgba(108, 92, 231, 0.1) !important; color: #6c5ce7 !important; }
.bg-soft-secondary { background: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; }
.bg-soft-success { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
.bg-soft-warning { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
.bg-soft-danger { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

/* Table interactions */
.table-row-hover-effect {
    transition: background-color 0.2s ease;
}
.table-row-hover-effect:hover {
    background-color: rgba(108, 92, 231, 0.015) !important;
}

/* Forms & curved inputs */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108, 92, 231, 0.15) !important;
}

/* Theme adaptation */
html[data-admin-theme="light"] .theme-card-bg {
    background-color: #ffffff !important;
}
html[data-admin-theme="dark"] .theme-card-bg {
    background-color: #0c1427 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}
html[data-admin-theme="light"] .text-theme-dark-bold {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .text-theme-dark-bold {
    color: #f1f5f9 !important;
}
html[data-admin-theme="dark"] .table-row-hover-effect:hover {
    background-color: rgba(255, 255, 255, 0.01) !important;
}
html[data-admin-theme="dark"] td {
    color: #cbd5e1 !important;
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

/* Border styles */
.border-bottom-subtle {
    border-bottom: 1.5px solid rgba(108, 92, 231, 0.06) !important;
}
html[data-admin-theme="dark"] .border-bottom-subtle {
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.05) !important;
}

/* Premium Buttons */
.btn {
    border-radius: 30px !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
    color: #ffffff !important;
}
.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
}
.btn-outline-primary {
    border: 1.5px solid #6c5ce7 !important;
    color: #6c5ce7 !important;
    background: transparent !important;
}
.btn-outline-primary:hover {
    background-color: #6c5ce7 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
}
html[data-admin-theme="dark"] .btn-outline-primary {
    border-color: #a78bfa !important;
    color: #a78bfa !important;
}
html[data-admin-theme="dark"] .btn-outline-primary:hover {
    background-color: #a78bfa !important;
    color: #0c1427 !important;
}

/* Floating Action Bar styling */
.floating-save-bar {
    position: fixed;
    bottom: 24px;
    left: calc(50% + 120px);
    transform: translateX(-50%);
    z-index: 1000;
    width: calc(100% - 32px - 240px);
    max-width: 920px;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15, 23, 42, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}
.floating-save-bar .button-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.floating-save-bar .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px !important;
    min-width: 110px !important;
    padding: 0 24px !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
    border-radius: 30px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0, 0, 0, 0.15) !important;
    background: transparent !important;
    color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.25) !important;
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
    background: transparent !important;
    color: #ffffff !important;
}
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    color: #ffffff !important;
}
.floating-save-bar .btn-primary {
    border: 1.5px solid transparent !important;
    background: var(--ps-gradient, linear-gradient(135deg, #6c5ce7, #a78bfa)) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
}
.floating-save-bar .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
    color: #ffffff !important;
}

/* Floating Custom Pink-Rose Delete Button (Mirroring Reference Screenshot) */
.floating-save-bar .btn-danger {
    background-color: transparent !important;
    border: 1.8px solid #ff3366 !important;
    color: #ff3366 !important;
    box-shadow: none !important;
}
.floating-save-bar .btn-danger:hover {
    background-color: rgba(255, 51, 102, 0.05) !important;
    border-color: #ff3366 !important;
    box-shadow: 0 4px 12px rgba(255, 51, 102, 0.15) !important;
    transform: translateY(-1px) !important;
    color: #ff3366 !important;
}
.floating-save-bar .btn-danger:active {
    transform: scale(0.97) !important;
}

.floating-save-bar .floating-bar-title {
    color: #0f172a !important;
}
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title {
    color: #ffffff !important;
}
.floating-save-bar .text-muted {
    color: #64748b !important;
}
html[data-admin-theme="dark"] .floating-save-bar .text-muted {
    color: #94a3b8 !important;
}

.live-indicator {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
}
@keyframes blinkIndicator {
    0%, 100% { opacity: 0.3; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.15); }
}

@media (max-width: 991px) {
    .floating-save-bar {
        left: 50% !important;
        width: calc(100% - 32px) !important;
    }
}
@media (max-width: 575px) {
    /* Responsive Profile Banner Stacking */
    .customer-profile-banner {
        flex-direction: column !important;
        text-align: center !important;
        padding: 1.25rem !important;
    }
    .customer-profile-avatar {
        margin-right: 0 !important;
        margin-bottom: 12px !important;
        width: 56px !important;
        height: 56px !important;
        min-width: 56px !important;
        font-size: 1.25rem !important;
    }
    .customer-profile-badges {
        justify-content: center !important;
    }

    /* Reduce Card Padding on mobile to prevent squishing */
    .card-body.p-4,
    .card-body.p-md-5 {
        padding: 1.25rem !important;
    }

    /* Action bar sizing & layout */
    .floating-save-bar {
        border-radius: 20px !important;
        padding: 12px 16px !important;
        bottom: 16px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: calc(100% - 24px) !important;
        flex-direction: column !important;
        gap: 10px !important;
        align-items: stretch !important;
        text-align: center !important;
        margin: 0 !important;
    }
    .floating-save-bar .d-flex {
        justify-content: center !important;
    }
    .floating-save-bar .button-group {
        display: flex !important;
        width: 100% !important;
        gap: 8px !important;
    }
    .floating-save-bar .btn {
        min-width: 0 !important;
        padding: 0 8px !important;
        font-size: 0.76rem !important;
        flex: 1 !important;
        height: 38px !important;
    }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(108, 92, 231, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(108, 92, 231, 0.4);
}

/* Premium Dropdown listbox and option items with curved edges */
.dropdown-menu {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08) !important;
    border-radius: 12px !important;
    border: 1.5px solid rgba(108, 92, 231, 0.06) !important;
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
    background-color: rgba(108, 92, 231, 0.06) !important;
    color: #6c5ce7 !important;
}
.dropdown-menu .dropdown-item.active {
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}

/* Dark theme overrides for custom dropdown menus */
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

/* Dark mode overrides for toggle inputs */
html[data-admin-theme="dark"] #scopeDropdown,
html[data-admin-theme="dark"] #purchaseSortDropdown {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] #scopeDropdown:hover,
html[data-admin-theme="dark"] #purchaseSortDropdown:hover {
    background-color: rgba(255, 255, 255, 0.04) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
}

/* Premium custom checkbox with curved edges and dark/light adaptations */
.premium-custom-checkbox {
    position: relative;
    display: flex;
    align-items: center;
    padding-left: 1.8rem;
    min-height: 1.5rem;
}
.premium-custom-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.premium-custom-checkbox .checkmark {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    height: 18px;
    width: 18px;
    background-color: #ffffff;
    border: 1.5px solid rgba(0, 0, 0, 0.15);
    border-radius: 6px !important; /* Perfect curved edges for checkboxes! */
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.premium-custom-checkbox:hover input ~ .checkmark {
    border-color: #6c5ce7;
    background-color: rgba(108, 92, 231, 0.03);
}
.premium-custom-checkbox input:checked ~ .checkmark {
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    border-color: transparent !important;
}
.premium-custom-checkbox .checkmark:after {
    content: "";
    position: absolute;
    display: none;
}
.premium-custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}
.premium-custom-checkbox .checkmark:after {
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Dark mode compatibility for curved checkboxes */
html[data-admin-theme="dark"] .premium-custom-checkbox .checkmark {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}
html[data-admin-theme="dark"] .premium-custom-checkbox:hover input ~ .checkmark {
    border-color: #a78bfa !important;
    background-color: rgba(255, 255, 255, 0.02) !important;
}
html[data-admin-theme="dark"] .premium-custom-checkbox input:checked ~ .checkmark {
    background: linear-gradient(135deg, #a78bfa, #8b5cf6) !important;
    border-color: transparent !important;
}

/* Mobile responsive alignments for Purchase History headers */
@media (max-width: 575px) {
    .d-flex.justify-content-between.align-items-center.flex-wrap.gap-3.mb-4.pb-2.border-bottom-subtle {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
        padding-bottom: 12px !important;
    }
    
    #purchase-sort-form {
        width: 100% !important;
        justify-content: space-between !important;
    }
}
</style>

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
$(function() {
    // Toggle Selected Products checkbox container based on active hidden scope input
    function toggleProductList() {
        var scope = $('#offerScopeInput').val();
        if (scope === 'selected') {
            $('#productListContainer').show();
        } else {
            $('#productListContainer').hide();
        }
    }
    toggleProductList(); // Trigger immediately on load

    // Client-side search within the selected products checkboxes list
    $('#productSearch').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.product-checkbox-item').each(function() {
            var name = $(this).data('name');
            if (name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

// Custom Scope Selector trigger
function selectScope(val) {
    // Update hidden field value
    $('#offerScopeInput').val(val);
    
    // Update active dropdown label text
    $('#selectedScopeLabel').text(val === 'selected' ? 'Selected Products Only' : 'Entire Product Catalog');
    
    // Manage active visual classes inside dropdown
    var dropdownMenu = $('#scopeDropdown').next('.dropdown-menu');
    dropdownMenu.find('.dropdown-item').removeClass('active');
    if (val === 'selected') {
        dropdownMenu.find('.dropdown-item').eq(1).addClass('active');
        $('#productListContainer').slideDown(250);
    } else {
        dropdownMenu.find('.dropdown-item').eq(0).addClass('active');
        $('#productListContainer').slideUp(250);
    }
}

// Purchase sorting trigger
function submitPurchaseSort(val) {
    document.getElementById('purchase_sort_input').value = val;
    document.getElementById('purchase-sort-form').submit();
}
</script>
@endpush
@endsection