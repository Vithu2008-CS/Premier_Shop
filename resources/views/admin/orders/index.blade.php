{{--
    admin/orders/index.blade.php — Admin order management list
    ===========================================================
    Filterable by status, searchable by order number / customer name.
    Table: order number, customer, date, total, status badge, driver, actions.
    Quick status update dropdown per row.
    Variable: $orders (paginated with user, driver), $drivers (for assignment modal)
--}}
@extends('layouts.admin_noble')
@section('title', 'Orders')

@section('content')
<nav class="page-breadcrumb mb-4">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Orders</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h6 class="card-title fw-bold text-primary mb-1 d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-receipt mr-2"></i> Order Management
                </h6>
                <p class="text-muted small mb-0">Monitor payments, dispatch status, and driver assignments.</p>
            </div>
            <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.8rem;">Total: {{ $orders->total() }} Orders</span>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr style="border-bottom: 2px solid rgba(116, 48, 137,0.08);">
                <th class="font-weight-bold" style="font-family: 'Outfit', sans-serif;">Order #</th>
                <th class="font-weight-bold" style="font-family: 'Outfit', sans-serif;">Customer</th>
                <th class="font-weight-bold text-end" style="font-family: 'Outfit', sans-serif;">Total</th>
                <th class="font-weight-bold" style="font-family: 'Outfit', sans-serif;">Status</th>
                <th class="font-weight-bold" style="font-family: 'Outfit', sans-serif;">Date</th>
                <th class="text-right font-weight-bold" style="font-family: 'Outfit', sans-serif;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                  <td class="align-middle">
                      <a href="{{ route('admin.orders.show', $order) }}" class="font-weight-bold text-primary d-inline-flex align-items-center gap-1" style="font-size: 0.85rem;">
                          <i class="bi bi-hash"></i>{{ $order->order_number }}
                      </a>
                      @if($order->returnRequest)
                      <a href="{{ route('admin.returns.show', $order->returnRequest) }}" class="badge bg-soft-danger font-weight-bold ml-1" style="font-size: 0.62rem; border-radius: 8px; padding: 2px 6px; text-decoration: none;" title="View Return Request for this order">
                          Return
                      </a>
                      @endif
                  </td>
                  <td class="align-middle">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <div class="wd-30 h-30 rounded-circle bg-light-primary d-flex align-items-center justify-content-center text-primary font-weight-bold text-uppercase" style="font-size: 0.75rem; min-width: 30px; min-height: 30px; line-height: 1; margin-right: 4px;">
                            {{ substr($order->user->name, 0, 1) }}
                        </div>
                        <span class="fw-bold text-nowrap" style="line-height: 1.2;">{{ $order->user->name }}</span>
                    </div>
                  </td>
                  <td class="font-weight-bold align-middle text-end" style="font-size: 0.88rem;">£{{ number_format($order->total, 2) }}</td>
                  <td class="align-middle">
                    <div class="dropdown d-inline-block">
                        @php
                            $statusClass = 'bg-light-secondary';
                            if($order->status === 'pending') $statusClass = 'bg-soft-warning text-warning';
                            elseif($order->status === 'processing') $statusClass = 'bg-soft-info text-info';
                            elseif($order->status === 'shipped') $statusClass = 'bg-soft-primary text-primary';
                            elseif($order->status === 'delivered') $statusClass = 'bg-soft-success text-success';
                            elseif($order->status === 'cancelled') $statusClass = 'bg-soft-danger text-danger';
                        @endphp
                        <button class="btn btn-sm rounded-pill fw-bold border-0 px-3 py-1 dropdown-toggle status-dropdown-btn {{ $statusClass }}" type="button" id="statusDropdown-{{ $order->id }}" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.8rem !important; height: 30px; min-width: 95px; letter-spacing: 0.3px; transition: all 0.2s ease;">
                            {{ ucfirst($order->status) }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-status rounded-3 border-0 shadow py-1" aria-labelledby="statusDropdown-{{ $order->id }}" style="border-radius: 12px !important; font-size: 0.8rem; min-width: 110px; z-index: 1050;">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-none" id="update-status-{{ $order->id }}-{{ $s }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $s }}">
                                </form>
                                <a class="dropdown-item fw-bold py-2 px-3 text-capitalize {{ $order->status === $s ? 'active-status-item' : '' }}" href="#" data-submit-form="update-status-{{ $order->id }}-{{ $s }}">
                                    {{ $s }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                  </td>
                  <td class="text-muted align-middle" style="font-size: 0.8rem;">{{ $order->created_at->format('d M Y') }}</td>
                  <td class="text-right align-middle">
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to delete this order?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-icon btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding: 0;" title="Delete Order">
                            <i class="bi bi-trash-fill" style="font-size: 0.85rem;"></i>
                        </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="bi bi-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No orders found in the system.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
            </div>
            <div>
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.bg-soft-primary { background: rgba(116, 48, 137,0.1); color: #743089; }
.bg-soft-success { background: rgba(16,185,129,0.1); color: #10b981; }
.bg-soft-warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
.bg-soft-danger { background: rgba(239,68,68,0.1); color: #ef4444; }
.bg-soft-info { background: rgba(6,182,212,0.1); color: #06b6d4; }

html[data-admin-theme="dark"] .bg-soft-primary { background: rgba(167, 139, 250, 0.15); color: #a78bfa; }
html[data-admin-theme="dark"] .bg-soft-success { background: rgba(52, 211, 153, 0.15); color: #34d399; }
html[data-admin-theme="dark"] .bg-soft-warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
html[data-admin-theme="dark"] .bg-soft-danger { background: rgba(248, 113, 113, 0.15); color: #f87171; }
html[data-admin-theme="dark"] .bg-soft-info { background: rgba(34, 211, 238, 0.15); color: #22d3ee; }

.status-dropdown-btn {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.status-dropdown-btn::after {
    display: none !important;
}
.status-dropdown-btn:hover {
    transform: translateY(-1px);
    filter: brightness(0.96);
}
html[data-admin-theme="dark"] .status-dropdown-btn:hover {
    filter: brightness(1.1);
}

.dropdown-menu-status {
    border-radius: 12px !important;
    background: #ffffff !important;
    border: 1.5px solid rgba(0, 0, 0, 0.05) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    padding: 6px !important;
}
.dropdown-menu-status .dropdown-item {
    border-radius: 8px !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    transition: all 0.15s ease !important;
    margin: 2px 0 !important;
    padding: 6px 12px !important;
}
.dropdown-menu-status .dropdown-item:hover {
    background: rgba(116, 48, 137, 0.08) !important;
    color: #743089 !important;
}
.dropdown-menu-status .active-status-item {
    background: rgba(116, 48, 137, 0.12) !important;
    color: #743089 !important;
}

/* Dark Theme Adaptation for dropdown menu list box */
html[data-admin-theme="dark"] .dropdown-menu-status {
    background: #0c1427 !important;
    border-color: rgba(255, 255, 255, 0.07) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.28) !important;
}
html[data-admin-theme="dark"] .dropdown-menu-status .dropdown-item {
    color: #cbd5e1 !important;
}
html[data-admin-theme="dark"] .dropdown-menu-status .dropdown-item:hover {
    background: rgba(167, 139, 250, 0.12) !important;
    color: #a78bfa !important;
}
html[data-admin-theme="dark"] .dropdown-menu-status .active-status-item {
    background: rgba(167, 139, 250, 0.18) !important;
    color: #a78bfa !important;
}
.rounded-4 {
    border-radius: 18px !important;
}
.page-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
}
.page-breadcrumb .breadcrumb-item a {
    color: #743089;
    font-weight: 500;
}
html[data-admin-theme="dark"] .page-breadcrumb .breadcrumb-item a {
    color: #a78bfa;
}
.page-breadcrumb .breadcrumb-item.active {
    color: #64748b;
}
html[data-admin-theme="dark"] .page-breadcrumb .breadcrumb-item.active {
    color: #94a3b8;
}
</style>
@endsection
