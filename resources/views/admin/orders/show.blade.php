{{--
    admin/orders/show.blade.php — Redesigned Admin order detail view
    =================================================================
    Redesigned from the ground up to utilize workspace width elegantly.
    Balances left and right columns beautifully with visual symmetry.
    Uses premium curved aesthetics, modern gradients, and micro-animations.
    Fully responsive & supports active light/dark themes natively.
--}}
@extends('layouts.admin_noble')
@section('title', 'Order ' . $order->order_number)

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('plugin-scripts')
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush

@section('content')
<div class="container-fluid px-0">
  {{-- Breadcrumbs --}}
  <nav class="page-breadcrumb mb-4">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
      <li class="breadcrumb-item active text-muted" aria-current="page">#{{ $order->order_number }}</li>
    </ol>
  </nav>

  {{-- Top Title & Actions Bar --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <span class="text-muted small text-uppercase fw-800 tracking-wider">Order Management</span>
      <h3 class="mb-0 mt-1" style="font-family: 'Outfit', sans-serif; font-weight: 800; letter-spacing: -0.5px;">
          Order Details <span class="text-primary font-weight-bold">#{{ $order->order_number }}</span>
      </h3>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-2">
      <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-outline-primary rounded-pill px-4 d-inline-flex align-items-center justify-content-center gap-2 mr-3" style="height: 40px; font-weight: 700; font-size: 0.82rem; letter-spacing: 0.3px;">
        <i class="bi bi-printer-fill" style="font-size: 0.95rem;"></i>
        Download Invoice PDF
      </a>
      <a href="{{ route('admin.orders.index') }}" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center justify-content-center gap-2 text-white" style="height: 40px; font-weight: 700; font-size: 0.82rem; letter-spacing: 0.3px;">
        <i class="bi bi-arrow-left-short" style="font-size: 1.3rem; line-height: 1;"></i>
        Back to List
      </a>
    </div>
  </div>

  {{-- Premium Stats & Overview Widgets --}}
  <div class="row mb-4">
      <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
          <div class="card border-0 shadow-sm p-4 text-white h-100 flex-column justify-content-between widget-grad-purple position-relative overflow-hidden" style="border-radius: 18px !important;">
              <div class="widget-decor">
                  <i class="bi bi-wallet2"></i>
              </div>
              <span class="text-white-50 small text-uppercase font-weight-bold" style="letter-spacing: 0.8px; font-size: 0.72rem;">Total Amount</span>
              <h2 class="mb-0 fw-800 mt-2" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">£{{ number_format($order->total, 2) }}</h2>
          </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
          <div class="card border-0 shadow-sm p-4 text-white h-100 flex-column justify-content-between widget-grad-blue position-relative overflow-hidden" style="border-radius: 18px !important;">
              <div class="widget-decor">
                  <i class="bi bi-calendar-event"></i>
              </div>
              <span class="text-white-50 small text-uppercase font-weight-bold" style="letter-spacing: 0.8px; font-size: 0.72rem;">Order Placed</span>
              <h5 class="mb-0 fw-700 mt-2" style="font-family: 'Outfit', sans-serif; line-height: 1.5;">{{ $order->created_at->format('M d, Y') }} <span class="d-block text-white-50 font-weight-normal" style="font-size: 0.75rem;">at {{ $order->created_at->format('H:i') }}</span></h5>
          </div>
      </div>
      <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
          <div class="card border-0 shadow-sm p-4 h-100 flex-column justify-content-between theme-card-bg" style="border-radius: 18px !important; border: 1px solid rgba(108,92,231,0.06) !important;">
              <span class="text-muted small text-uppercase font-weight-bold" style="letter-spacing: 0.8px; font-size: 0.72rem;">Order Status</span>
              <div class="mt-2">
                  @php
                      $statusColors = [
                          'pending' => 'bg-soft-warning text-warning',
                          'processing' => 'bg-soft-info text-info',
                          'shipped' => 'bg-soft-primary text-primary',
                          'delivered' => 'bg-soft-success text-success',
                          'cancelled' => 'bg-soft-danger text-danger',
                      ];
                      $colorClass = $statusColors[$order->status] ?? 'bg-soft-secondary text-secondary';
                  @endphp
                  <span class="badge rounded-pill {{ $colorClass }} px-3 py-2 fw-700 font-weight-bold" style="font-size: 0.82rem; letter-spacing: 0.3px;">
                      <span class="status-pulse-dot bg-current" style="background-color: currentColor;"></span>
                      {{ ucfirst($order->status) }}
                  </span>
              </div>
          </div>
      </div>
      <div class="col-sm-6 col-md-3">
          <div class="card border-0 shadow-sm p-4 h-100 flex-column justify-content-between theme-card-bg" style="border-radius: 18px !important; border: 1px solid rgba(108,92,231,0.06) !important;">
              <span class="text-muted small text-uppercase font-weight-bold" style="letter-spacing: 0.8px; font-size: 0.72rem;">Payment Details</span>
              <div class="mt-2">
                  @php
                      $paymentColors = [
                          'pending' => 'bg-soft-danger text-danger',
                          'completed' => 'bg-soft-success text-success',
                      ];
                      $payColorClass = $paymentColors[$order->payment_status] ?? 'bg-soft-secondary text-secondary';
                  @endphp
                  <span class="badge rounded-pill {{ $payColorClass }} px-3 py-2 fw-700 font-weight-bold" style="font-size: 0.82rem; letter-spacing: 0.3px;">
                      {{ $order->payment_status === 'completed' ? 'Paid' : 'Unpaid' }}
                  </span>
                  <span class="d-block text-muted small mt-1 font-weight-medium" style="font-size: 0.7rem;"><i class="bi bi-credit-card mr-1"></i> {{ $order->payment_method ?? 'Debit/Credit Card' }}</span>
              </div>
          </div>
      </div>
  </div>

  {{-- Two Column Layout --}}
  <div class="row">
    {{-- Left Column (col-lg-8): Order Items, Update Status Form, Driver Assignment --}}
    <div class="col-lg-8 flex-column d-flex gap-4">
      
      {{-- Card 1: Order Items Table --}}
      <div class="card border-0 shadow-sm overflow-hidden theme-card-bg w-100 mb-4" style="border-radius: 18px !important;">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
              <i class="bi bi-box-seam-fill text-primary mr-2" style="font-size: 1.25rem;"></i>
              Order Items
            </h5>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle table-custom-padding">
              <thead>
                <tr class="text-uppercase small text-muted font-weight-bold" style="border-bottom: 2px solid rgba(108,92,231,0.06); letter-spacing: 0.5px; font-size: 0.72rem;">
                  <th class="pl-0">Product</th>
                  <th class="text-center">Quantity</th>
                  <th class="text-right">Unit Price</th>
                  <th class="text-right pr-0">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $item)
                <tr class="table-row-hover-effect" style="border-bottom: 1px solid rgba(108,92,231,0.04);">
                  <td class="pl-0 py-3 align-middle">
                    <span class="fw-700 text-theme-dark-bold" style="font-size: 0.9rem;">{{ $item->product->name }}</span>
                  </td>
                  <td class="text-center py-3 font-weight-bold text-theme-dark-bold" style="font-size: 0.9rem;">{{ $item->quantity }}</td>
                  <td class="text-right py-3 text-muted font-weight-medium" style="font-size: 0.88rem;">£{{ number_format($item->price, 2) }}</td>
                  <td class="text-right pr-0 py-3 font-weight-bold text-primary" style="font-size: 0.92rem;">£{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Pricing Breakdown --}}
          <div class="row mt-4 pt-3 justify-content-end">
            <div class="col-md-7 col-lg-6">
              <div class="pricing-summary-card p-4 rounded-4" style="background: rgba(108,92,231,0.02); border: 1px solid rgba(108,92,231,0.05);">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted small">Cart Subtotal</span>
                  <span class="font-weight-bold text-theme-dark-bold">£{{ number_format($order->subtotal, 2) }}</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="d-flex justify-content-between mb-2 text-success font-weight-bold">
                  <span class="small d-flex align-items-center gap-1">
                    <i class="bi bi-tag-fill"></i> Coupon Code ({{ $order->coupon_code }})
                  </span>
                  <span>-£{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mb-3">
                  <span class="text-muted small">Shipping Rate</span>
                  <span class="font-weight-bold text-theme-dark-bold">£{{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                
                <hr class="my-2 border-dashed">
                
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <h6 class="font-weight-bold mb-0 text-theme-dark-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.05rem;">Grand Total</h6>
                  <h4 class="text-primary font-weight-extrabold mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.45rem; letter-spacing: -0.5px;">£{{ number_format($order->total, 2) }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Card 2: Redesigned Update Status & Tracking form (Horizontal Spacious Layout) --}}
      <div class="card border-0 shadow-sm theme-card-bg w-100 mb-4" style="border-radius: 18px !important; overflow: visible !important;">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
              <i class="bi bi-arrow-repeat text-info mr-3" style="font-size: 1.25rem;"></i>
              Update Status & Tracking
            </h5>
          </div>

          <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
            @csrf 
            @method('PATCH')
            
            {{-- Dropdowns Row --}}
            <div class="row mb-4">
              <div class="col-md-6 mb-3 mb-md-0">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-clock-history text-primary mr-2"></i> Order Status
                </label>
                <div class="dropdown custom-form-dropdown w-100">
                  <input type="hidden" id="orderStatusInput" name="status" value="{{ $order->status }}">
                  <button class="btn btn-outline-light custom-select-btn d-flex align-items-center justify-content-between w-100 px-3 rounded-3 text-left" type="button" id="orderStatusDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 42px;">
                    <span class="dropdown-selected-text text-dark-theme-aware font-weight-medium">{{ ucfirst($order->status) }}</span>
                    <i class="bi bi-chevron-down text-muted small ml-2"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-premium w-100 rounded-3 shadow border-0" aria-labelledby="orderStatusDropdownBtn" style="border-radius: 12px !important; margin-top: 4px; padding: 6px;">
                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                      <a class="dropdown-item d-flex align-items-center py-2 px-3 cursor-pointer rounded-2" data-value="{{ $s }}" onclick="updateCustomSelect('orderStatus', '{{ $s }}', '{{ ucfirst($s) }}')">
                        {{ ucfirst($s) }}
                      </a>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-cash-stack text-success mr-2"></i> Payment Status
                </label>
                <div class="dropdown custom-form-dropdown w-100">
                  <input type="hidden" id="paymentStatusInput" name="payment_status" value="{{ $order->payment_status }}">
                  <button class="btn btn-outline-light custom-select-btn d-flex align-items-center justify-content-between w-100 px-3 rounded-3 text-left" type="button" id="paymentStatusDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 42px;">
                    @php
                      $paymentLabels = [
                          'pending' => 'Pending (Unpaid)',
                          'completed' => 'Completed (Paid)'
                      ];
                    @endphp
                    <span class="dropdown-selected-text text-dark-theme-aware font-weight-medium">{{ $paymentLabels[$order->payment_status] ?? 'Select Payment' }}</span>
                    <i class="bi bi-chevron-down text-muted small ml-2"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-premium w-100 rounded-3 shadow border-0" aria-labelledby="paymentStatusDropdownBtn" style="border-radius: 12px !important; margin-top: 4px; padding: 6px;">
                    <a class="dropdown-item d-flex align-items-center py-2 px-3 cursor-pointer rounded-2" data-value="pending" onclick="updateCustomSelect('paymentStatus', 'pending', 'Pending (Unpaid)')">Pending (Unpaid)</a>
                    <a class="dropdown-item d-flex align-items-center py-2 px-3 cursor-pointer rounded-2" data-value="completed" onclick="updateCustomSelect('paymentStatus', 'completed', 'Completed (Paid)')">Completed (Paid)</a>
                  </div>
                </div>
              </div>
            </div>

            {{-- Date Timeline Inputs Row --}}
            <div class="row mb-4">
              <div class="col-md-4 mb-3 mb-md-0">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-calendar-check text-muted mr-2"></i> Processing Date
                </label>
                <div class="position-relative">
                  <input type="text" name="processing_date" class="form-control rounded-3 datetime-picker" value="{{ $order->processing_date ? $order->processing_date->format('Y-m-d H:i') : '' }}" style="height: 42px !important; padding-right: 40px !important;" placeholder="Select date & time...">
                  <i class="bi bi-calendar3 position-absolute datetime-picker-icon" style="right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 0.9rem;"></i>
                </div>
              </div>
              <div class="col-md-4 mb-3 mb-md-0">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-truck text-muted mr-2"></i> Shipped Date
                </label>
                <div class="position-relative">
                  <input type="text" name="shipped_date" class="form-control rounded-3 datetime-picker" value="{{ $order->shipped_date ? $order->shipped_date->format('Y-m-d H:i') : '' }}" style="height: 42px !important; padding-right: 40px !important;" placeholder="Select date & time...">
                  <i class="bi bi-calendar3 position-absolute datetime-picker-icon" style="right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 0.9rem;"></i>
                </div>
              </div>
              <div class="col-md-4">
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-house-check text-muted mr-2"></i> Delivered Date
                </label>
                <div class="position-relative">
                  <input type="text" name="delivered_date" class="form-control rounded-3 datetime-picker" value="{{ $order->delivered_date ? $order->delivered_date->format('Y-m-d H:i') : '' }}" style="height: 42px !important; padding-right: 40px !important;" placeholder="Select date & time...">
                  <i class="bi bi-calendar3 position-absolute datetime-picker-icon" style="right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 0.9rem;"></i>
                </div>
              </div>
            </div>

            {{-- Action & Notification Toggle --}}
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top-subtle flex-wrap gap-3">
              <div class="d-flex align-items-center">
                <div class="custom-control custom-switch custom-switch-premium">
                  <input type="checkbox" class="custom-control-input cursor-pointer" id="sendEmailSwitch" name="send_email" value="1" checked>
                  <label class="custom-control-label small text-muted font-weight-bold cursor-pointer" for="sendEmailSwitch" style="user-select: none;">
                    Email customer about this status change
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold d-inline-flex align-items-center justify-content-center" style="height: 40px;">
                <i class="bi bi-arrow-repeat mr-2" style="font-size: 0.95rem;"></i>
                <span>Update Order Status</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Card 3: Redesigned Driver Assignment (Side-by-Side Horizontal Layout) --}}
      <div class="card border-0 shadow-sm theme-card-bg w-100 mb-4" style="border-radius: 18px !important; overflow: visible !important;">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
              <i class="bi bi-person-badge-fill text-warning mr-2" style="font-size: 1.25rem;"></i>
              Driver Assignment
            </h5>
          </div>

          <div class="row align-items-center">
            {{-- Driver State Column --}}
            <div class="col-md-6 mb-4 mb-md-0">
              @if($order->driver)
                <div class="d-flex align-items-center p-4 rounded-4" style="background: rgba(108,92,231,0.03); border: 1.5px solid rgba(108,92,231,0.07); border-radius: 16px !important;">
                  <div class="wd-52 h-52 rounded-circle bg-warning text-white d-flex align-items-center justify-content-center font-weight-bold shadow-sm mr-3" style="font-size: 1.25rem; min-width: 52px; height: 52px;">
                    {{ substr($order->driver->name, 0, 1) }}
                  </div>
                  <div>
                    <span class="text-muted small d-block font-weight-bold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.65rem;">Currently Assigned</span>
                    <h6 class="mb-0 fw-800 text-theme-dark-bold mt-1" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">{{ $order->driver->name }}</h6>
                    <div class="d-flex align-items-center gap-1.5 mt-1">
                      <span class="driver-pulse"></span>
                      <span class="text-success small fw-700 font-weight-bold" style="font-size: 0.75rem;">Active & On Duty</span>
                    </div>
                  </div>
                </div>
              @else
                <div class="d-flex align-items-center p-4 rounded-4" style="background: rgba(245,158,11,0.04); border: 1.5px dashed rgba(245,158,11,0.2); border-radius: 16px !important;">
                  <div class="wd-52 h-52 rounded-circle bg-soft-warning text-warning d-flex align-items-center justify-content-center mr-3" style="min-width: 52px; height: 52px;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 1.35rem;"></i>
                  </div>
                  <div>
                    <span class="text-muted small d-block font-weight-bold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.65rem;">Assigned Driver</span>
                    <h6 class="mb-0 fw-800 text-warning mt-1" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">Pending Assignment</h6>
                    <span class="text-muted small" style="font-size: 0.72rem;">Assign a driver to enable mobile delivery updates</span>
                  </div>
                </div>
              @endif
            </div>

            {{-- Assign Driver form --}}
            <div class="col-md-6">
              <form action="{{ route('admin.orders.assignDriver', $order) }}" method="POST">
                @csrf
                <label class="small text-muted font-weight-bold mb-2 d-flex align-items-center">
                  <i class="bi bi-person-fill-add text-warning mr-2"></i> Select Driver & Assign
                </label>
                
                <div class="d-flex align-items-center gap-2">
                  <div class="dropdown custom-form-dropdown flex-grow-1" style="min-width: 0;">
                    <input type="hidden" id="driverAssignmentInput" name="driver_id" value="{{ $order->driver_id }}">
                    <button class="btn btn-outline-light custom-select-btn d-flex align-items-center justify-content-between w-100 px-3 rounded-3 text-left" type="button" id="driverAssignmentDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 42px;">
                      @php
                        $assignedDriverName = $order->driver ? $order->driver->name : 'Choose active driver...';
                      @endphp
                      <span class="dropdown-selected-text text-dark-theme-aware font-weight-medium truncate-text" style="font-size: 0.84rem;">{{ $assignedDriverName }}</span>
                      <i class="bi bi-chevron-down text-muted small ml-2"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-premium w-100 rounded-3 shadow border-0" aria-labelledby="driverAssignmentDropdownBtn" style="border-radius: 12px !important; margin-top: 4px; padding: 6px; max-height: 250px; overflow-y: auto;">
                      <a class="dropdown-item d-flex align-items-center py-2 px-3 cursor-pointer rounded-2 {{ !$order->driver_id ? 'active' : '' }}" data-value="" onclick="updateCustomSelect('driverAssignment', '', 'Choose active driver...')">
                        Choose active driver...
                      </a>
                      @foreach($drivers as $driver)
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 cursor-pointer rounded-2 {{ $order->driver_id == $driver->id ? 'active' : '' }}" data-value="{{ $driver->id }}" onclick="updateCustomSelect('driverAssignment', '{{ $driver->id }}', '{{ $driver->name }}')">
                          <span>{{ $driver->name }}</span>
                          <span class="text-muted small">({{ $driver->assigned_orders_count ?? 0 }} active)</span>
                        </a>
                      @endforeach
                    </div>
                  </div>
                  
                  <button type="submit" class="btn btn-primary px-4 d-flex align-items-center justify-content-center shadow-sm" style="height: 42px !important; border-radius: 12px !important; min-width: 100px; gap: 6px;">
                    <i class="bi bi-person-check-fill" style="font-size: 0.95rem;"></i>
                    <span>Assign</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Right Column (col-lg-4): Customer Info, Shipping Details, Delivery Proof --}}
    <div class="col-lg-4 flex-column d-flex gap-4">
      
      {{-- Card A: Customer Information --}}
      <div class="card border-0 shadow-sm overflow-hidden theme-card-bg mb-4" style="border-radius: 18px !important;">
        <div class="card-body p-4">
          <h6 class="card-title fw-bold text-primary mb-4 d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
            <i class="bi bi-person-fill mr-2"></i> Customer Information
          </h6>
          
          <div class="d-flex align-items-center p-3 rounded-4 mb-3" style="background: rgba(108,92,231,0.03); border: 1px solid rgba(108,92,231,0.06); border-radius: 15px !important;">
            <div class="wd-48 h-48 rounded-circle bg-light-info text-info font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm" style="font-size: 1.15rem; font-family: 'Outfit', sans-serif; min-width: 48px; height: 48px;">
              {{ substr($order->user->name, 0, 1) }}
            </div>
            <div>
              <h6 class="mb-0 fw-800 text-theme-dark-bold" style="font-size: 0.92rem; font-family: 'Outfit', sans-serif;">{{ $order->user->name }}</h6>
              <p class="text-muted small mb-0">{{ $order->user->email }}</p>
            </div>
          </div>

          <div class="d-flex flex-column gap-2 mt-2">
            @if($order->user->phone)
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 text-muted small" style="background: rgba(0,0,0,0.01);">
              <span class="d-flex align-items-center gap-2"><i class="bi bi-telephone-fill text-muted"></i> Phone</span>
              <span class="font-weight-bold text-theme-dark-bold">{{ $order->user->phone }}</span>
            </div>
            @endif

            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 text-muted small" style="background: rgba(0,0,0,0.01);">
              <span class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Customer Registered</span>
              <span class="font-weight-bold text-theme-dark-bold">{{ $order->user->created_at ? $order->user->created_at->format('M d, Y') : 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Card B: Shipping Details --}}
      <div class="card border-0 shadow-sm overflow-hidden theme-card-bg mb-4" style="border-radius: 18px !important;">
        <div class="card-body p-3.5">
          <h6 class="card-title fw-bold text-primary mb-3 d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
            <i class="bi bi-geo-alt-fill mr-2"></i> Shipping Details
          </h6>

          @if(isset($order->shipping_address['address_line']))
            <div class="d-flex align-items-start gap-2.5 p-3 rounded-4 mb-2.5" style="background: rgba(108,92,231,0.03); border: 1px solid rgba(108,92,231,0.06); border-radius: 15px !important;">
              <i class="bi bi-geo-alt-fill text-primary mr-3" style="font-size: 1.1rem; margin-top: 2px;"></i>
              <div>
                <h6 class="mb-1 text-theme-dark-bold fw-700" style="font-size: 0.88rem;">Delivery Address</h6>
                <p class="text-muted small mb-0 fw-600 leading-normal">{{ $order->shipping_address['address_line'] }}</p>
                <p class="text-muted small mb-0 leading-normal">{{ $order->shipping_address['city'] ?? '' }}</p>
              </div>
            </div>

            @if(isset($order->shipping_address['phone']))
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 text-muted small mb-2" style="background: rgba(0,0,0,0.01);">
              <span class="d-flex align-items-center gap-2"><i class="bi bi-telephone text-muted"></i> Shipping Phone</span>
              <span class="font-weight-bold text-theme-dark-bold">{{ $order->shipping_address['phone'] }}</span>
            </div>
            @endif

            <hr class="my-3 border-dashed">

            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 text-muted small" style="background: rgba(0,0,0,0.01);">
              <span class="d-flex align-items-center gap-2"><i class="bi bi-credit-card-2-front text-muted"></i> Payment Method</span>
              <span class="font-weight-bold text-theme-dark-bold">{{ $order->payment_method ?? 'Debit/Credit Card' }}</span>
            </div>
          @else
            <div class="alert alert-light border mb-0 p-3 rounded-3 d-flex align-items-center gap-2" style="border-radius: 12px !important;">
              <i class="bi bi-info-circle text-muted"></i>
              <p class="text-muted small mb-0">No shipping address recorded</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Card C: Delivery Proof --}}
      @if($order->status === 'delivered' && $order->delivery_proof)
        <div class="card border-0 shadow-sm overflow-hidden theme-card-bg mb-4" style="border-radius: 18px !important;">
          <div class="card-body p-4">
            <h6 class="card-title fw-bold text-primary mb-3 d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
              <i class="bi bi-image mr-2"></i> Delivery Proof
            </h6>
            
            <div class="position-relative overflow-hidden rounded-4 shadow-sm border-0 delivery-proof-container" style="border-radius: 14px !important;">
              <img src="{{ (str_starts_with($order->delivery_proof, 'data:image') || str_starts_with($order->delivery_proof, 'http')) ? $order->delivery_proof : asset('storage/' . $order->delivery_proof) }}" class="img-fluid rounded-4 border-0 w-100" alt="Delivery Proof" style="border-radius: 14px !important; object-fit: cover; max-height: 250px;">
              <div class="delivery-proof-overlay">
                <span class="badge badge-success rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5"><i class="bi bi-check-circle-fill"></i> Verified Delivery</span>
              </div>
            </div>
            
            <p class="text-muted small mt-3 text-center italic mb-0 font-weight-medium">
              Delivered: {{ $order->delivered_date ? $order->delivered_date->format('M d, Y H:i') : 'N/A' }}
            </p>
          </div>
        </div>
      @endif

      {{-- Card D: Compact Order Actions & Notes --}}
      <div class="card border-0 shadow-sm theme-card-bg mb-4" style="border-radius: 18px !important;">
        <div class="card-body p-3">
          <h6 class="card-title fw-bold text-primary mb-2 d-flex align-items-center" style="font-family: 'Outfit', sans-serif; font-size: 0.82rem;">
            <i class="bi bi-gear-fill mr-2" style="font-size: 0.85rem;"></i> Order Actions & Notes
          </h6>
          
          {{-- Admin Notes Textarea --}}
          <div class="position-relative">
            <textarea id="adminOrderNotes" class="form-control rounded-3 p-2 text-dark-theme-aware" rows="2" style="font-size: 0.76rem; resize: none; background: rgba(0,0,0,0.015); border: 1.5px solid rgba(0,0,0,0.05); height: 48px !important;" placeholder="Type internal admin notes here..."></textarea>
            <div id="notesSavedIndicator" class="position-absolute small text-success font-weight-bold" style="right: 8px; bottom: 6px; display: none; font-size: 0.7rem;">
              <i class="bi bi-check-circle-fill mr-1"></i> Saved!
            </div>
          </div>
          
          {{-- Notes action row --}}
          <div class="d-flex align-items-center justify-content-between mt-2 mb-2">
            <button type="button" onclick="deleteAdminNotes()" class="btn btn-outline-danger p-0 d-flex align-items-center justify-content-center" style="height: 25px !important; border-radius: 6px !important; font-size: 0.7rem !important; border-color: rgba(220, 53, 69, 0.15) !important; width: calc(50% - 4px); font-weight: 600;">
              <i class="bi bi-trash mr-1"></i> Clear Notes
            </button>
            <button type="button" onclick="saveAdminNotes()" class="btn btn-primary p-0 d-flex align-items-center justify-content-center" style="height: 25px !important; border-radius: 6px !important; font-size: 0.7rem !important; width: calc(50% - 4px); font-weight: 600;">
              <i class="bi bi-bookmark-check mr-1"></i> Save Notes
            </button>
          </div>

          <hr class="my-2 border-dashed" style="opacity: 0.6;">

          {{-- Order action row --}}
          <div class="d-flex align-items-center justify-content-between mt-2">
            <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-outline-primary p-0 d-flex align-items-center justify-content-center text-center" style="height: 28px !important; border-radius: 6px !important; font-size: 0.72rem !important; border-color: rgba(108,92,231,0.2) !important; width: calc(50% - 3px); font-weight: 600;">
              <i class="bi bi-printer-fill mr-1" style="font-size: 0.78rem;"></i> Download PDF
            </a>
            
            <button type="button" onclick="confirmOrderDelete()" class="btn btn-outline-danger p-0 d-flex align-items-center justify-content-center text-center" style="height: 28px !important; border-radius: 6px !important; font-size: 0.72rem !important; border-color: rgba(220, 53, 69, 0.2) !important; width: calc(50% - 3px); font-weight: 600;">
              <i class="bi bi-trash-fill mr-1" style="font-size: 0.78rem;"></i> Delete Order
            </button>
          </div>

          <form id="deleteOrderForm" action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Premium View Styles --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

/* Explicit Curve Classes to support Bootstrap 4/5 uniformly */
.rounded-3 {
    border-radius: 12px !important;
}
.rounded-4 {
    border-radius: 18px !important;
}

/* Mobile Friendly Responsive Utility Layouts purely via CSS (keeps desktop view pristine!) */
@media (max-width: 576px) {
    .card-body {
        padding: 1.25rem !important; /* Premium tighter padding to save space on mobile */
    }
    .p-md-5, .p-4 {
        padding: 1.25rem !important;
    }
    
    /* Stack top title actions container cleanly on phone */
    .page-breadcrumb + div {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .page-breadcrumb + div > div:last-child {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        margin-top: 12px !important;
    }
    .page-breadcrumb + div > div:last-child a {
        width: 100% !important;
        margin-right: 0 !important;
        margin-bottom: 8px !important;
    }
    
    /* Make Update Order Status button full-width on mobile */
    form[action*="updateStatus"] button[type="submit"] {
        width: 100% !important;
        margin-top: 12px !important;
    }
    form[action*="updateStatus"] .d-flex.justify-content-between {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    /* Stack driver assignment elements vertically on mobile */
    form[action*="assign-driver"] .d-flex.align-items-center {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    form[action*="assign-driver"] .custom-form-dropdown {
        width: 100% !important;
        margin-bottom: 8px !important;
    }
    form[action*="assign-driver"] button[type="submit"] {
        width: 100% !important;
        min-width: 0 !important;
    }
}

/* Typography override */
.container-fluid {
    font-family: 'Inter', sans-serif;
}

/* Table Row Curved Hover Highlights */
.table-row-hover-effect {
    transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.table-row-hover-effect td {
    border-top: none !important;
}
.table-row-hover-effect td:first-child {
    border-top-left-radius: 12px !important;
    border-bottom-left-radius: 12px !important;
}
.table-row-hover-effect td:last-child {
    border-top-right-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
}

/* Gradients */
.widget-grad-purple {
    background: linear-gradient(135deg, #6c5ce7 0%, #8555e3 100%) !important;
}
.widget-grad-blue {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
}

/* Badge colors */
.bg-soft-success { background: rgba(16,185,129,0.1); color: #10b981; }
.bg-soft-primary { background: rgba(108,92,231,0.1); color: #6c5ce7; }
.bg-soft-warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
.bg-soft-danger { background: rgba(239,68,68,0.1); color: #ef4444; }
.bg-soft-info { background: rgba(6,182,212,0.1); color: #06b6d4; }
.bg-soft-secondary { background: rgba(100,116,139,0.1); color: #64748b; }

.status-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
    vertical-align: middle;
    animation: blinkIndicator 1.8s infinite ease-in-out;
}

/* Theme Adaptive Styles */
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

html[data-admin-theme="light"] .text-dark-theme-aware {
    color: #0f172a !important;
}
html[data-admin-theme="dark"] .text-dark-theme-aware {
    color: #cbd5e1 !important;
}

/* Form Styles */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
}
html[data-admin-theme="dark"] .form-control,
html[data-admin-theme="dark"] select.form-control {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108, 92, 231, 0.15) !important;
    background-color: var(--input-bg-focus, #ffffff) !important;
}
html[data-admin-theme="dark"] .form-control:focus,
html[data-admin-theme="dark"] select.form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167, 139, 250, 0.2) !important;
    background-color: #080f1d !important;
}

/* Custom switch Track & Toggle Alignment */
.custom-switch-premium {
    padding-left: 2.75rem !important;
    display: inline-flex !important;
    align-items: center !important;
    min-height: 24px !important;
}
.custom-switch-premium .custom-control-label {
    margin-bottom: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    padding-top: 0 !important;
    line-height: 1 !important;
    cursor: pointer !important;
}
.custom-switch-premium .custom-control-label::before {
    height: 1.25rem !important;
    width: 2.25rem !important;
    border-radius: 1rem !important;
    background-color: #cbd5e1 !important;
    border: none !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    left: -2.75rem !important;
}
.custom-switch-premium .custom-control-label::after {
    width: calc(1.25rem - 4px) !important;
    height: calc(1.25rem - 4px) !important;
    border-radius: 50% !important;
    background-color: white !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    left: calc(-2.75rem + 2px) !important;
    transition: transform .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
}
.custom-switch-premium .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #6c5ce7 !important;
}
.custom-switch-premium .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateY(-50%) translateX(1rem) !important;
}
html[data-admin-theme="dark"] .custom-switch-premium .custom-control-label::before {
    background-color: #334155 !important;
}

/* Buttons */
.btn {
    border-radius: 30px !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    font-size: 0.85rem !important;
    padding: 0.45rem 1.4rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 14px rgba(108, 92, 231, 0.25) !important;
    color: #ffffff !important;
}
.btn-primary:hover {
    transform: translateY(-1.5px);
    box-shadow: 0 6px 20px rgba(108, 92, 231, 0.35) !important;
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

/* Decorative widgets */
.widget-decor {
    position: absolute;
    right: -15px;
    bottom: -15px;
    font-size: 5rem;
    opacity: 0.08;
    transform: rotate(-15deg);
    pointer-events: none;
}

/* Border styles */
.border-bottom-subtle {
    border-bottom: 1.5px solid rgba(108, 92, 231, 0.06) !important;
}
html[data-admin-theme="dark"] .border-bottom-subtle {
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.05) !important;
}

.border-dashed {
    border-top: 1px dashed rgba(108, 92, 231, 0.12) !important;
}
html[data-admin-theme="dark"] .border-dashed {
    border-top: 1px dashed rgba(255, 255, 255, 0.08) !important;
}

/* Table layout styling */
.table-custom-padding th, 
.table-custom-padding td {
    padding: 1rem 0.75rem !important;
    border-top: none !important;
}

.table-row-hover-effect {
    transition: background-color 0.2s ease;
}
.table-row-hover-effect:hover {
    background-color: rgba(108, 92, 231, 0.015);
}
html[data-admin-theme="dark"] .table-row-hover-effect:hover {
    background-color: rgba(255, 255, 255, 0.01);
}

/* Driver Assignment Elements */
.driver-pulse {
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: blinkIndicator 1.5s infinite ease-in-out;
}

@keyframes blinkIndicator {
    0%, 100% { opacity: 0.35; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.2); }
}

/* Breadcrumb styles */
.page-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
}
.page-breadcrumb .breadcrumb-item a {
    color: #6c5ce7;
    font-weight: 600;
}
html[data-admin-theme="dark"] .page-breadcrumb .breadcrumb-item a {
    color: #a78bfa;
}

/* Delivery Proof overlay */
.delivery-proof-container {
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,0.06);
}
.delivery-proof-overlay {
    position: absolute;
    bottom: 12px;
    left: 12px;
    z-index: 10;
}

/* General typography helpers */
.fw-700 { font-weight: 700; }
.fw-800 { font-weight: 800; }
.font-weight-extrabold { font-weight: 800; }
.font-weight-medium { font-weight: 500; }

.gap-1.5 { gap: 0.35rem; }
.gap-2.5 { gap: 0.6rem; }
.leading-normal { line-height: 1.5; }
.cursor-pointer { cursor: pointer; }

/* Custom Form Dropdowns */
.custom-select-btn {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.08) !important;
    background-color: var(--input-bg, #ffffff) !important;
    color: var(--input-color, #1e293b) !important;
    text-align: left !important;
    font-size: 0.84rem !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
html[data-admin-theme="dark"] .custom-select-btn {
    background-color: #080f1d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
}
.custom-select-btn:hover, .custom-select-btn:focus, .custom-select-btn:active {
    border-color: #6c5ce7 !important;
    background-color: var(--input-bg, #ffffff) !important;
    box-shadow: 0 0 0 3.5px rgba(108, 92, 231, 0.15) !important;
}
html[data-admin-theme="dark"] .custom-select-btn:hover,
html[data-admin-theme="dark"] .custom-select-btn:focus,
html[data-admin-theme="dark"] .custom-select-btn:active {
    border-color: #a78bfa !important;
    background-color: #080f1d !important;
    box-shadow: 0 0 0 3.5px rgba(167, 139, 250, 0.2) !important;
}

/* Premium Dropdown Menu List Box */
.dropdown-menu-premium {
    border-radius: 12px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    border: 1.5px solid rgba(0, 0, 0, 0.05) !important;
    background-color: #ffffff !important;
}
html[data-admin-theme="dark"] .dropdown-menu-premium {
    background-color: #0c1427 !important;
    border-color: rgba(255, 255, 255, 0.06) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25) !important;
}

/* Custom list items with curves */
.dropdown-menu-premium .dropdown-item {
    border-radius: 8px !important;
    color: #475569 !important;
    font-weight: 500 !important;
    font-size: 0.82rem !important;
    transition: all 0.15s ease !important;
}
html[data-admin-theme="dark"] .dropdown-menu-premium .dropdown-item {
    color: #cbd5e1 !important;
}
.dropdown-menu-premium .dropdown-item:hover {
    background-color: rgba(108, 92, 231, 0.08) !important;
    color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .dropdown-menu-premium .dropdown-item:hover {
    background-color: rgba(167, 139, 250, 0.12) !important;
    color: #a78bfa !important;
}

/* Custom active dropdown item state with curved borders and matched violet theme background */
.dropdown-menu-premium .dropdown-item.active,
.dropdown-menu-premium .dropdown-item.active:hover {
    background-color: rgba(108, 92, 231, 0.12) !important;
    color: #6c5ce7 !important;
    font-weight: 700 !important;
}
html[data-admin-theme="dark"] .dropdown-menu-premium .dropdown-item.active,
html[data-admin-theme="dark"] .dropdown-menu-premium .dropdown-item.active:hover {
    background-color: rgba(167, 139, 250, 0.18) !important;
    color: #a78bfa !important;
    font-weight: 700 !important;
}

/* Flatpickr Custom Premium Curves & Theme Styles */
.flatpickr-calendar {
    border-radius: 14px !important;
    border: 1.5px solid rgba(0, 0, 0, 0.06) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
    background-color: #ffffff !important;
    font-family: 'Inter', sans-serif !important;
    margin-top: 5px !important;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease !important;
}
html[data-admin-theme="dark"] .flatpickr-calendar {
    background-color: #0c1427 !important;
    border-color: rgba(255, 255, 255, 0.07) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.28) !important;
}

/* Header Months & Years */
.flatpickr-months {
    padding-top: 6px !important;
}
.flatpickr-months .flatpickr-month {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .flatpickr-months .flatpickr-month {
    color: #f1f5f9 !important;
}
.flatpickr-current-month .numInputWrapper span.arrowUp::after { border-bottom-color: #6c5ce7 !important; }
.flatpickr-current-month .numInputWrapper span.arrowDown::after { border-top-color: #6c5ce7 !important; }
html[data-admin-theme="dark"] .flatpickr-current-month .numInputWrapper span.arrowUp::after { border-bottom-color: #a78bfa !important; }
html[data-admin-theme="dark"] .flatpickr-current-month .numInputWrapper span.arrowDown::after { border-top-color: #a78bfa !important; }

/* Fully hide native Flatpickr month select to render our premium curved dropdown */
.flatpickr-monthDropdown-months,
.flatpickr-current-month .flatpickr-monthDropdown-months {
    display: none !important;
}

/* Curved Year Selector */
.flatpickr-current-month input.cur-year {
    color: inherit !important;
    font-weight: 700 !important;
    border-radius: 6px !important;
    border: none !important;
    padding: 2px 6px !important;
    transition: background-color 0.15s ease !important;
}
.flatpickr-current-month input.cur-year:hover,
.flatpickr-current-month input.cur-year:focus {
    background-color: rgba(108, 92, 231, 0.06) !important;
}
html[data-admin-theme="dark"] .flatpickr-current-month input.cur-year:hover,
html[data-admin-theme="dark"] .flatpickr-current-month input.cur-year:focus {
    background-color: rgba(167, 139, 250, 0.1) !important;
}

/* Arrow buttons */
.flatpickr-months .flatpickr-prev-month, 
.flatpickr-months .flatpickr-next-month {
    color: #6c5ce7 !important;
    fill: #6c5ce7 !important;
    padding: 8px !important;
    border-radius: 50% !important;
}
html[data-admin-theme="dark"] .flatpickr-months .flatpickr-prev-month, 
html[data-admin-theme="dark"] .flatpickr-months .flatpickr-next-month {
    color: #a78bfa !important;
    fill: #a78bfa !important;
}
.flatpickr-months .flatpickr-prev-month:hover, 
.flatpickr-months .flatpickr-next-month:hover {
    background-color: rgba(108, 92, 231, 0.08) !important;
}
html[data-admin-theme="dark"] .flatpickr-months .flatpickr-prev-month:hover, 
html[data-admin-theme="dark"] .flatpickr-months .flatpickr-next-month:hover {
    background-color: rgba(167, 139, 250, 0.12) !important;
}

/* Weekday header */
span.flatpickr-weekday {
    color: #94a3b8 !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
}

/* Day boxes with curves */
.flatpickr-day {
    border-radius: 8px !important; /* Curve inside day elements! */
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    transition: all 0.15s ease !important;
    margin: 2px !important;
    max-width: 35px !important;
    height: 35px !important;
    line-height: 33px !important;
}
html[data-admin-theme="dark"] .flatpickr-day {
    color: #cbd5e1 !important;
}
.flatpickr-day:hover, 
.flatpickr-day:focus {
    background-color: rgba(108, 92, 231, 0.08) !important;
    color: #6c5ce7 !important;
    border-color: transparent !important;
}
html[data-admin-theme="dark"] .flatpickr-day:hover, 
html[data-admin-theme="dark"] .flatpickr-day:focus {
    background-color: rgba(167, 139, 250, 0.12) !important;
    color: #a78bfa !important;
}

/* Selected Days */
.flatpickr-day.selected, 
.flatpickr-day.startRange, 
.flatpickr-day.endRange {
    background: linear-gradient(135deg, #6c5ce7, #8555e3) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 10px rgba(108, 92, 231, 0.25) !important;
}
html[data-admin-theme="dark"] .flatpickr-day.selected, 
html[data-admin-theme="dark"] .flatpickr-day.startRange, 
html[data-admin-theme="dark"] .flatpickr-day.endRange {
    background: linear-gradient(135deg, #a78bfa, #c084fc) !important;
    color: #0c1427 !important;
    box-shadow: 0 4px 10px rgba(167, 139, 250, 0.25) !important;
}

/* Disabled Days */
.flatpickr-day.flatpickr-disabled, 
.flatpickr-day.flatpickr-disabled:hover {
    color: #cbd5e1 !important;
    background: transparent !important;
    opacity: 0.35 !important;
}
html[data-admin-theme="dark"] .flatpickr-day.flatpickr-disabled, 
html[data-admin-theme="dark"] .flatpickr-day.flatpickr-disabled:hover {
    color: #475569 !important;
}

/* Time Picker Section */
.flatpickr-time {
    border-top: 1.5px solid rgba(108, 92, 231, 0.08) !important;
    border-radius: 0 0 14px 14px !important;
}
html[data-admin-theme="dark"] .flatpickr-time {
    border-top-color: rgba(255, 255, 255, 0.06) !important;
}
.flatpickr-time input:hover, 
.flatpickr-time .flatpickr-am-pm:hover, 
.flatpickr-time input:focus, 
.flatpickr-time .flatpickr-am-pm:focus {
    background: rgba(108, 92, 231, 0.08) !important;
}
html[data-admin-theme="dark"] .flatpickr-time input:hover, 
html[data-admin-theme="dark"] .flatpickr-time .flatpickr-am-pm:hover, 
html[data-admin-theme="dark"] .flatpickr-time input:focus, 
html[data-admin-theme="dark"] .flatpickr-time .flatpickr-am-pm:focus {
    background: rgba(167, 139, 250, 0.12) !important;
}
.flatpickr-time input, 
.flatpickr-time .flatpickr-am-pm {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .flatpickr-time input, 
html[data-admin-theme="dark"] .flatpickr-time .flatpickr-am-pm {
    color: #cbd5e1 !important;
}

/* Custom Calendar Icon visibility & glowing effect on focus */
.datetime-picker-icon {
    color: #64748b !important;
    transition: color 0.25s ease !important;
}
html[data-admin-theme="dark"] .datetime-picker-icon {
    color: #94a3b8 !important;
}
.position-relative:focus-within .datetime-picker-icon {
    color: #6c5ce7 !important;
}
html[data-admin-theme="dark"] .position-relative:focus-within .datetime-picker-icon {
    color: #a78bfa !important;
}

/* Additional premium curved styling for Flatpickr clock inputs */
.flatpickr-time input.flatpickr-hour,
.flatpickr-time input.flatpickr-minute,
.flatpickr-time .flatpickr-am-pm {
    border-radius: 6px !important;
    font-weight: 700 !important;
}

/* Custom month picker container */
.flatpickr-custom-month-container {
    position: relative;
    display: inline-block;
    vertical-align: middle;
}

/* Custom month picker button (matches the year size and font exactly) */
.flatpickr-custom-month-btn {
    border-radius: 20px !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important; /* MATCH THE YEAR SIZE EXACTLY */
    height: 30px !important;
    padding: 0 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border: 1.5px solid #6c5ce7 !important;
    background-color: rgba(108, 92, 231, 0.04) !important;
    color: #1e293b !important;
    outline: none !important;
    line-height: 1 !important;
    vertical-align: middle !important;
}

html[data-admin-theme="dark"] .flatpickr-custom-month-btn {
    border-color: #a78bfa !important;
    background-color: rgba(167, 139, 250, 0.08) !important;
    color: #cbd5e1 !important;
}

.flatpickr-custom-month-btn:hover {
    background-color: rgba(108, 92, 231, 0.08) !important;
    transform: translateY(-0.5px);
}

html[data-admin-theme="dark"] .flatpickr-custom-month-btn:hover {
    background-color: rgba(167, 139, 250, 0.15) !important;
}

.flatpickr-custom-month-btn i {
    font-size: 0.75rem !important;
    transition: transform 0.2s ease !important;
}

/* Rotate chevron when dropdown is open */
.flatpickr-custom-month-container.open .flatpickr-custom-month-btn i {
    transform: rotate(180deg);
}

/* Custom month picker list popup with premium curves and compact 3-column grid structure */
.flatpickr-custom-month-menu {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(8px);
    z-index: 1050;
    width: 200px !important; /* Extremely compact width */
    background-color: rgba(255, 255, 255, 0.96) !important; /* High opacity solid backdrop */
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1.5px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 14px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    padding: 8px !important;
    display: none; /* Hidden by default */
    grid-template-columns: repeat(3, 1fr) !important; /* 3-columns grid! */
    gap: 5px !important;
    opacity: 0;
    transition: opacity 0.15s ease, transform 0.15s ease;
}

.flatpickr-custom-month-container.open .flatpickr-custom-month-menu {
    display: grid !important; /* Render as grid when open! */
    opacity: 1;
}

html[data-admin-theme="dark"] .flatpickr-custom-month-menu {
    background-color: rgba(12, 20, 39, 0.98) !important; /* Rich solid dark background to fully block numbers */
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
}

/* Premium micro-interactions: blur calendar days and time inputs when month grid is open */
.flatpickr-innerContainer,
.flatpickr-time {
    transition: filter 0.22s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.22s ease !important;
}

.flatpickr-calendar.month-picker-open .flatpickr-innerContainer,
.flatpickr-calendar.month-picker-open .flatpickr-time {
    filter: blur(4px) !important;
    opacity: 0.25 !important;
    pointer-events: none !important;
}

/* Custom month list items inside grid with curves */
.flatpickr-custom-month-item {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 30px !important;
    color: #475569 !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    background-color: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease !important;
    padding: 0 !important;
}

html[data-admin-theme="dark"] .flatpickr-custom-month-item {
    color: #cbd5e1 !important;
}

.flatpickr-custom-month-item:hover {
    background-color: rgba(108, 92, 231, 0.08) !important;
    color: #6c5ce7 !important;
}

html[data-admin-theme="dark"] .flatpickr-custom-month-item:hover {
    background-color: rgba(167, 139, 250, 0.12) !important;
    color: #a78bfa !important;
}

/* Selected state active styling */
.flatpickr-custom-month-item.active {
    background-color: rgba(108, 92, 231, 0.12) !important;
    color: #6c5ce7 !important;
}

html[data-admin-theme="dark"] .flatpickr-custom-month-item.active {
    background-color: rgba(167, 139, 250, 0.18) !important;
    color: #a78bfa !important;
}

/* Force absolute visibility on Flatpickr calendar containers to prevent clipping */
.flatpickr-calendar,
.flatpickr-months,
.flatpickr-month,
.flatpickr-current-month {
    overflow: visible !important;
}

/* Redesign calendar header: place month & year in same size and align center on flex baseline */
.flatpickr-current-month {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 2px !important;
    height: 35px !important;
    width: auto !important;
    position: static !important;
}

.flatpickr-current-month .numInputWrapper {
    display: none !important;
}
</style>

<script>
/** Updates both the hidden form input and the custom select dropdown's display label. */
function updateCustomSelect(fieldId, value, text) {
    document.getElementById(fieldId + 'Input').value = value;
    const btn = document.getElementById(fieldId + 'DropdownBtn');
    if (btn) {
        btn.querySelector('.dropdown-selected-text').innerText = text;
    }
}

// Initialize premium curved Flatpickr calendars when the document is fully ready
document.addEventListener('DOMContentLoaded', function() {
    flatpickr(".datetime-picker", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        disableMobile: "true",
        onReady: function(selectedDates, dateStr, instance) {
            // Locate the Flatpickr native month select container
            const currentMonthContainer = instance.calendarContainer.querySelector('.flatpickr-current-month');
            if (!currentMonthContainer) return;
            
            const nativeSelect = currentMonthContainer.querySelector('.flatpickr-monthDropdown-months');
            if (!nativeSelect) return;
            
            // Explicitly hide native select in DOM
            nativeSelect.style.setProperty('display', 'none', 'important');
            
            // Check if our custom dropdown is already built to prevent duplicates
            if (currentMonthContainer.querySelector('.flatpickr-custom-month-container')) return;
            
            // Create bespoke HTML elements using our premium CSS classes
            const containerDiv = document.createElement('div');
            containerDiv.className = 'flatpickr-custom-month-container';
            
            const btn = document.createElement('button');
            btn.className = 'flatpickr-custom-month-btn';
            btn.type = 'button';
            
            const activeMonthIndex = instance.currentMonth;
            const fullMonthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const shortMonthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            
            btn.innerHTML = `<span class="flatpickr-custom-month-text">${fullMonthNames[activeMonthIndex]}</span> <i class="bi bi-chevron-down"></i>`;
            
            const menu = document.createElement('div');
            menu.className = 'flatpickr-custom-month-menu';
            
            shortMonthNames.forEach((monthAbbrev, index) => {
                const item = document.createElement('button');
                item.className = 'flatpickr-custom-month-item';
                item.type = 'button';
                if (index === activeMonthIndex) {
                    item.classList.add('active');
                }
                item.innerText = monthAbbrev;
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    instance.changeMonth(index);
                    
                    // Remove blur class on calendar container
                    instance.calendarContainer.classList.remove('month-picker-open');
                    
                    // Update button label with full name
                    containerDiv.querySelector('.flatpickr-custom-month-text').innerText = fullMonthNames[index];
                    
                    // Update active classes
                    menu.querySelectorAll('.flatpickr-custom-month-item').forEach((opt, idx) => {
                        if (idx === index) {
                            opt.classList.add('active');
                        } else {
                            opt.classList.remove('active');
                        }
                    });
                    
                    // Close menu
                    containerDiv.classList.remove('open');
                });
                
                menu.appendChild(item);
            });
            
            containerDiv.appendChild(btn);
            containerDiv.appendChild(menu);
            
            // Toggle dropdown open state and handle event propagation
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close any other open custom month dropdowns
                document.querySelectorAll('.flatpickr-custom-month-container').forEach(c => {
                    if (c !== containerDiv) {
                        c.classList.remove('open');
                    }
                });
                
                const isOpen = containerDiv.classList.toggle('open');
                if (isOpen) {
                    instance.calendarContainer.classList.add('month-picker-open');
                } else {
                    instance.calendarContainer.classList.remove('month-picker-open');
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                containerDiv.classList.remove('open');
                instance.calendarContainer.classList.remove('month-picker-open');
            });
            
            nativeSelect.parentNode.insertBefore(containerDiv, nativeSelect);
        },
        onMonthChange: function(selectedDates, dateStr, instance) {
            // Keep custom month display synced when month changes via prev/next arrow clicks
            const fullMonthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const container = instance.calendarContainer.querySelector('.flatpickr-custom-month-container');
            if (container) {
                // Update button text
                const textSpan = container.querySelector('.flatpickr-custom-month-text');
                if (textSpan) {
                    textSpan.innerText = fullMonthNames[instance.currentMonth];
                }
                
                // Update active item class
                const items = container.querySelectorAll('.flatpickr-custom-month-item');
                items.forEach((item, idx) => {
                    if (idx === instance.currentMonth) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
        }
    });
});

// Internal Admin Notes Functionality
function saveAdminNotes() {
    const textarea = document.getElementById('adminOrderNotes');
    const indicator = document.getElementById('notesSavedIndicator');
    const orderId = "{{ $order->id }}";
    
    localStorage.setItem('admin_order_notes_' + orderId, textarea.value);
    
    // Show visual confirmation indicator
    if (indicator) {
        indicator.style.display = 'inline-flex';
        indicator.style.opacity = '1';
        
        setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 300);
        }, 1500);
    }
}

function deleteAdminNotes() {
    const textarea = document.getElementById('adminOrderNotes');
    const orderId = "{{ $order->id }}";
    
    if (textarea && confirm("Are you sure you want to clear these internal notes?")) {
        textarea.value = '';
        localStorage.removeItem('admin_order_notes_' + orderId);
    }
}

// Load saved notes when page is fully ready
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('adminOrderNotes');
    if (textarea) {
        const orderId = "{{ $order->id }}";
        const savedNotes = localStorage.getItem('admin_order_notes_' + orderId);
        if (savedNotes) {
            textarea.value = savedNotes;
        }
    }
});

// Confirmation dialog for permanent order deletion
function confirmOrderDelete() {
    if (confirm("WARNING: Are you sure you want to permanently delete this order record? This action is completely irreversible and will remove all associated database entries.")) {
        const form = document.getElementById('deleteOrderForm');
        if (form) form.submit();
    }
}
</script>
@endsection