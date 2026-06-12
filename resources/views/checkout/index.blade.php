{{--
    checkout/index.blade.php — Checkout page
    =========================================
    Order summary (items, subtotals) on the right.
    Left: delivery address form (address_line, city, phone),
    saved addresses selector, coupon code input (AJAX),
    loyalty points checkbox, real-time shipping cost preview (AJAX),
    total breakdown, place order button.

    AJAX endpoints called:
     - checkout.applyCoupon  (POST)  — validates and stores coupon in session
     - checkout.removeCoupon (DELETE) — clears coupon from session
     - checkout.calculateShippingDynamic (POST) — zone-based delivery quote as address is typed
    Final submit → checkout.process (POST) → CheckoutController::process()

    Variables: $items, $savedAddresses, $settings (flat_rate_fee etc.),
    $coupon (from session), $loyaltyEnabled, $userPoints
--}}
@extends('layouts.app')
@section('title', 'Checkout - Premier Shop')

@push('styles')
<style>
    .x-small { font-size: 0.72rem; line-height: 1.4; }
    @media (max-width: 767px) {
        .checkout-item-list { max-height: 180px !important; }
    }
    
    /* Stepper Styling */
    .stepper-step {
        transition: all 0.3s ease;
    }
    .stepper-line {
        transition: background 0.3s ease;
    }
    
    /* Timer Pulse */
    @keyframes pulseTimer {
        0% { transform: scale(1); box-shadow: 0 0 0 rgba(225,112,85,0); }
        100% { transform: scale(1.02); box-shadow: 0 0 8px rgba(225,112,85,0.15); }
    }
    
    /* Coupon Tickets */
    .coupon-ticket-stub {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .coupon-ticket-stub:hover {
        transform: translateY(-2px);
        background: rgba(108, 92, 231, 0.05) !important;
        border-color: var(--ps-primary) !important;
        box-shadow: 0 4px 10px rgba(108, 92, 231, 0.08);
    }
    .custom-scrollbar::-webkit-scrollbar {
        height: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--ps-border);
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
@php
    $checkoutActiveCoupons = \App\Models\Coupon::where('is_active', true)
        ->where(function ($query) {
            $query->whereNull('valid_until')->orWhere('valid_until', '>', now());
        })
        ->get(['code', 'discount_type', 'discount_value', 'min_order_amount']);

    // Stripe is "enabled" only when a real (non-placeholder) publishable key is set.
    $stripeKey = config('services.stripe.key');
    $stripeEnabled = $stripeKey && ! str_contains($stripeKey, 'placeholder');
@endphp
<section class="section-padding">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d"><i class="bi bi-bag-check me-2"></i>Check<span class="gradient-text">out</span></h2>
        
        <!-- Glassmorphic Progress Stepper & Live Stock Reservation Widget -->
        <div class="card border-0 mb-4 shadow-sm" style="border-radius: 20px; background: var(--ps-surface-glass); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--ps-border) !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <!-- Stepper Progress Phase -->
                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-center">
                    <div class="stepper-step active d-flex align-items-center gap-2">
                        <span class="step-num rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:28px; height:28px; background: var(--ps-gradient); color:#fff; font-size:0.85rem;">1</span>
                        <span class="step-label fw-bold small">Details</span>
                    </div>
                    <div class="stepper-line" style="width: 40px; height: 2px; background: var(--ps-border);"></div>
                    <div class="stepper-step active d-flex align-items-center gap-2">
                        <span class="step-num rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:28px; height:28px; background: rgba(108,92,231,0.15); color:var(--ps-primary); border: 1px solid var(--ps-primary); font-size:0.85rem;">2</span>
                        <span class="step-label fw-bold small text-primary">Secure Pay</span>
                    </div>
                    <div class="stepper-line" style="width: 40px; height: 2px; background: var(--ps-border);"></div>
                    <div class="stepper-step text-muted d-flex align-items-center gap-2">
                        <span class="step-num rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:28px; height:28px; background: var(--ps-border); color:var(--ps-text-muted); font-size:0.85rem;">3</span>
                        <span class="step-label fw-bold small">Success</span>
                    </div>
                </div>
                
                <!-- Countdown Timer -->
                <div class="stock-reservation-widget p-2.5 px-4 rounded-pill d-flex align-items-center gap-2 bg-danger bg-opacity-10 text-danger" style="border: 1px solid rgba(225,112,85,0.2); animation: pulseTimer 2s infinite alternate;" id="stockTimerWidget">
                    <i class="bi bi-hourglass-split fs-5"></i>
                    <span class="fw-bold small" style="font-family: 'Outfit', sans-serif;">
                        Stock Reserved for <span id="checkoutCountdown" class="font-monospace">15:00</span> mins
                    </span>
                </div>
            </div>
        </div>

    <div class="row g-4">
        <div class="col-lg-7 reveal-slide-left">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="fw-bold mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-3" style="width:32px;height:32px;font-size:0.9rem;">1</span>
                            Shipping Address
                        </div>
                        @if(auth()->user()->addresses->isNotEmpty())
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-geo-alt me-1"></i> Saved Addresses
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 12px; min-width: 250px;">
                                @foreach(auth()->user()->addresses as $address)
                                <li>
                                    <a class="dropdown-item py-2 address-selector" href="#" 
                                       data-line="{{ $address->address_line }}" 
                                       data-city="{{ $address->city }}" 
                                       data-phone="{{ $address->phone }}">
                                        <div class="fw-bold small {{ $address->is_default ? 'text-primary' : '' }}">{{ $address->label }} {!! $address->is_default ? '<i class="bi bi-check-circle-fill"></i>' : '' !!}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $address->address_line }}, {{ $address->city }}</div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </h5>
                    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                        @csrf
                        @foreach($items as $item)
                            <input type="hidden" name="items[]" value="{{ $item->id }}">
                        @endforeach
                        {{-- Populated by Stripe.js after a successful card payment, then verified server-side --}}
                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                        @php
                            $defaultAddress = auth()->user()->defaultAddress ?? auth()->user()->addresses->first();
                        @endphp
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small px-1">STREET ADDRESS</label>
                            <input type="text" name="address_line" id="address_line" class="form-control form-control-lg @error('address_line') is-invalid @enderror" value="{{ old('address_line', $defaultAddress->address_line ?? auth()->user()->address) }}" placeholder="e.g. 123 High Street" required style="border-radius: 12px; padding: 14px 20px;">
                            @error('address_line') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small px-1">CITY / TOWN</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg @error('city') is-invalid @enderror" value="{{ old('city', $defaultAddress->city ?? auth()->user()->city) }}" placeholder="e.g. London" required style="border-radius: 12px; padding: 14px 20px;">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small px-1">PHONE NUMBER</label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" value="{{ old('phone', $defaultAddress->phone ?? auth()->user()->phone) }}" placeholder="e.g. 07123456789" required style="border-radius: 12px; padding: 14px 20px;">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Payment Method Section -->
                        <div class="mt-5 mb-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-3" style="width:32px;height:32px;font-size:0.9rem;">2</span>
                                Payment Method
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_card" value="Debit/Credit Card" checked required>
                                    <label class="btn btn-outline-primary w-100 py-3 px-3 d-flex flex-column align-items-center gap-2" for="pay_card" style="border-radius: 16px; border-width: 2px;">
                                        <i class="bi bi-credit-card-2-front fs-2"></i>
                                        <span class="fw-bold small">Debit/Credit Card</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_bank" value="Bank Transfer" required>
                                    <label class="btn btn-outline-primary w-100 py-3 px-3 d-flex flex-column align-items-center gap-2" for="pay_bank" style="border-radius: 16px; border-width: 2px;">
                                        <i class="bi bi-bank fs-2"></i>
                                        <span class="fw-bold small">Bank Transfer</span>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method') <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                             <!-- Secure Card Details Drawer (Stripe Payment Element) -->
                             <div id="cardDetailsContainer" class="mt-4 p-4 border rounded-4 shadow-sm" style="border-radius: 16px; transition: all 0.3s ease; background: var(--ps-surface-secondary);">
                                 <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                     <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                                     Secure Card Details
                                 </h6>
                                 @if($stripeEnabled)
                                     {{-- Card data is entered inside Stripe's iframe and never touches this server (PCI-safe). --}}
                                     <div id="payment-element"></div>
                                     <div id="payment-element-errors" class="text-danger small mt-2" role="alert"></div>
                                     <div class="d-flex align-items-center gap-2 text-muted x-small mt-3">
                                         <i class="bi bi-lock-fill text-success"></i>
                                         Card details are encrypted and processed securely by Stripe.
                                     </div>
                                 @else
                                     <div class="alert alert-warning small mb-0 d-flex align-items-center gap-2">
                                         <i class="bi bi-exclamation-triangle-fill"></i>
                                         <span>Card payments aren’t set up yet. Please choose <strong>Bank Transfer</strong> to place your order.</span>
                                     </div>
                                 @endif
                             </div>

                             <!-- Bank Transfer Details Drawer (Collapsible) -->
                             <div id="bankDetailsContainer" class="mt-4 p-4 border rounded-4 shadow-sm d-none" style="border-radius: 16px; transition: all 0.3s ease; background: var(--ps-surface-secondary);">
                                 <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                     <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                     Bank Transfer Instructions
                                 </h6>
                                 <p class="small text-muted mb-3">Please transfer the total amount to the bank account below. Your order will be processed once the funds clear.</p>
                                 <div class="p-3 rounded-3 mb-2" style="background: var(--ps-surface-bg);">
                                     <div class="d-flex justify-content-between mb-1 small"><span class="text-muted">Bank Name:</span> <strong class="text-body">Premier Retail Bank</strong></div>
                                     <div class="d-flex justify-content-between mb-1 small"><span class="text-muted">Account Name:</span> <strong class="text-body">Premier Shop Ltd</strong></div>
                                     <div class="d-flex justify-content-between mb-1 small"><span class="text-muted">Sort Code:</span> <strong class="text-body">12-34-56</strong></div>
                                     <div class="d-flex justify-content-between small"><span class="text-muted">Account Number:</span> <strong class="text-body">98765432</strong></div>
                                 </div>
                                 <div class="x-small text-warning bg-warning bg-opacity-10 p-2 rounded text-center">
                                     <i class="bi bi-exclamation-circle-fill me-1"></i> Please use your Order Number as the transfer reference!
                                 </div>
                             </div>
                        </div>

                        <div class="d-none d-lg-block">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mt-4" style="border-radius: 50px; background: var(--ps-gradient); border: none; font-weight: 700; box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2);">
                                <i class="bi bi-shield-lock-fill me-2"></i> Place Order Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 reveal-slide-right">
            {{-- Coupon --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-tag-fill text-primary me-2"></i>Got a Coupon?</h5>
                    @if(session('coupon'))
                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-0" id="couponInfoAlert" style="border-radius: 12px; border: none; background: rgba(0, 184, 148, 0.1); color: #00b894;">
                        <span id="couponText" class="fw-bold"><strong>{{ session('coupon.code') }}</strong> applied!</span>
                        <form action="{{ route('checkout.removeCoupon') }}" method="POST" class="ajax-form" id="removeCouponForm">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash-fill fs-5"></i></button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('checkout.applyCoupon') }}" method="POST" class="d-flex gap-2 ajax-form" id="applyCouponForm" data-clear-on-success="true">
                        @csrf
                        @foreach($items as $item)
                            <input type="hidden" name="items[]" value="{{ $item->id }}">
                        @endforeach
                        <input type="text" name="coupon_code" class="form-control" placeholder="Code (e.g. SUMMER10)" style="border-radius: 12px; padding: 10px 15px;">
                        <button type="submit" class="btn btn-outline-primary px-4" style="border-radius: 12px; font-weight: 600;">Apply</button>
                    </form>
                    @endif

                    @if(!session('coupon') && isset($checkoutActiveCoupons) && $checkoutActiveCoupons->isNotEmpty())
                    <div class="mt-3" id="availableCouponsContainer">
                        <small class="text-muted fw-bold d-block mb-2">Available Coupons (Click to Apply):</small>
                        <div class="d-flex gap-2 overflow-auto pb-2 custom-scrollbar" style="white-space: nowrap;">
                            @foreach($checkoutActiveCoupons as $cp)
                                @php
                                    $valStr = $cp->discount_type === 'percentage' ? ((float)$cp->discount_value).'%' : '£'.((float)$cp->discount_value);
                                    $minStr = (float)$cp->min_order_amount > 0 ? 'Min spend £'.((float)$cp->min_order_amount) : 'No min spend';
                                @endphp
                                <div class="coupon-ticket-stub d-inline-flex flex-column align-items-start p-2.5 border rounded-3 position-relative" style="background: var(--ps-surface-secondary); cursor: pointer; min-width: 140px; border: 1px dashed var(--ps-border) !important;" data-call="applyCouponDirect" data-args="[&quot;{{ $cp->code }}&quot;, &quot;$el&quot;]">
                                    <div class="fw-bold text-primary font-monospace small mb-1">{{ $cp->code }}</div>
                                    <div class="fw-bold text-success" style="font-size:0.75rem;">{{ $valStr }} OFF</div>
                                    <div class="text-muted x-small" style="font-size: 0.65rem;">{{ $minStr }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Loyalty Points --}}
            @php 
                $loyaltyEnabled = \App\Models\Setting::get('loyalty_enabled', false);
                $userPoints = auth()->user()->loyalty_points ?? 0;
                $redemptionValue = \App\Models\Setting::get('points_redemption_value', 0.01);
                $maxDiscount = $userPoints * $redemptionValue;
            @endphp
            
            @if($loyaltyEnabled && $userPoints > 0)
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px; background: rgba(253, 203, 110, 0.1); border-left: 4px solid #fdcb6e !important;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Loyalty Points</h5>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="d-block fw-bold text-dark fs-5">{{ number_format($userPoints) }} Pts</span>
                            <span class="small text-muted">Value: £{{ number_format($maxDiscount, 2) }}</span>
                        </div>
                        <div class="form-check form-switch form-switch-lg mt-0">
                            <input class="form-check-input" type="checkbox" id="usePointsToggle" form="checkoutForm" name="use_points" value="1" style="transform: scale(1.3);">
                            <label class="form-check-label ms-2 fw-bold text-primary" for="usePointsToggle">Apply Discount</label>
                        </div>
                    </div>
                    <small class="text-muted fst-italic"><i class="bi bi-info-circle me-1"></i>Points are applied dynamically against your remaining pre-shipping subtotal.</small>
                </div>
            </div>
            @endif

            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm" style="position:sticky;top:100px; border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Order Summary</h5>
                        <a href="{{ route('cart.index') }}" class="small text-decoration-none text-muted d-inline-flex align-items-center gap-1">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                    <div class="checkout-item-list mb-4 overflow-auto" style="max-height: 250px;">
                        @foreach($items as $item)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center" style="min-width:0;">
                                <div class="position-relative me-3 flex-shrink-0">
                                    <img src="{{ $item->product->first_image }}" alt="{{ $item->product->name }}" class="rounded-3" style="width:48px;height:48px;object-fit:cover;border:1px solid var(--ps-border);" loading="lazy" decoding="async">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: var(--ps-gradient); font-size:0.62rem;">{{ $item->quantity }}</span>
                                </div>
                                <div style="min-width:0;">
                                    <div class="small fw-600 truncate-1" style="max-width: 170px;">{{ $item->product->name }}</div>
                                    <div class="text-muted x-small">£{{ number_format($item->product->active_price, 2) }} × {{ $item->quantity }}</div>
                                </div>
                            </div>
                            <span class="small fw-bold ms-2 flex-shrink-0">£{{ number_format($item->line_total, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Subtotal</span>
                        <span class="small fw-bold">£{{ number_format($items->sum('line_total'), 2) }}</span>
                    </div>
                    @if(session('coupon'))
                    <div class="d-flex justify-content-between mb-2 text-success" id="discountRow">
                        <span class="small">Discount</span>
                        <span id="discountValueDisplay" class="small fw-bold">-£{{ number_format(session('coupon.discount'), 2) }}</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mb-2 text-success d-none" id="discountRow">
                        <span class="small">Discount</span>
                        <span id="discountValueDisplay" class="small fw-bold">-£0.00</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2 text-warning d-none" id="pointsRow">
                        <span class="small">Loyalty Points</span>
                        <span id="pointsValueDisplay" class="small fw-bold">-£0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Shipping</span>
                        <span id="shippingCostDisplay" class="small fw-bold text-primary">—</span>
                    </div>
                    <div id="shippingMessageDisplay" class="text-muted x-small text-end fst-italic mb-3">Enter your address to calculate delivery</div>
                    <hr class="my-3 opacity-10">
                    @php
                    $subtotal = $items->sum('line_total');
                    $subtotalMinusDiscount = $subtotal - (session('coupon.discount') ?? 0);
                    @endphp
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-4 gradient-text" id="cartTotalDisplay" data-base-total="{{ $subtotalMinusDiscount }}">£{{ number_format($subtotalMinusDiscount, 2) }}</span>
                    </div>

                    <div class="d-lg-none">
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-lg w-100 py-3" style="border-radius: 50px; background: var(--ps-gradient); border: none; font-weight: 700; box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2);">
                            <i class="bi bi-shield-lock-fill me-2"></i> Place Order
                        </button>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <div class="d-inline-flex align-items-center gap-2 text-muted x-small mb-2">
                            <i class="bi bi-shield-fill-check text-success"></i>
                            Secure SSL Checkout · Encrypted
                        </div>
                        <div class="d-flex justify-content-center align-items-center gap-3 fs-5 text-muted opacity-75">
                            <i class="bi bi-credit-card-2-front" title="Card"></i>
                            <i class="bi bi-bank" title="Bank transfer"></i>
                            <i class="bi bi-lock-fill" title="Encrypted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stripeEnabled)
<script src="https://js.stripe.com/v3/"></script>
@endif
<script nonce="{{ Vite::cspNonce() }}">
    document.addEventListener('DOMContentLoaded', function() {
        // ⏳ LocalStorage-backed Live Stock Reservation Countdown Widget
        const COUNTDOWN_KEY = 'checkout_reservation_timer';
        const DURATION = 15 * 60 * 1000; // 15 minutes
        
        let timerEnd = localStorage.getItem(COUNTDOWN_KEY);
        const now = Date.now();
        
        if (!timerEnd || parseInt(timerEnd) < now) {
            timerEnd = now + DURATION;
            localStorage.setItem(COUNTDOWN_KEY, timerEnd);
        } else {
            timerEnd = parseInt(timerEnd);
        }
        
        const countdownEl = document.getElementById('checkoutCountdown');
        const timerWidget = document.getElementById('stockTimerWidget');
        
        function updateTimer() {
            const timeLeft = timerEnd - Date.now();
            
            if (timeLeft <= 0) {
                localStorage.setItem(COUNTDOWN_KEY, Date.now() + DURATION);
                timerEnd = Date.now() + DURATION;
                updateTimer();
                return;
            }
            
            const minutes = Math.floor(timeLeft / (60 * 1000));
            const seconds = Math.floor((timeLeft % (60 * 1000)) / 1000);
            
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft < 2 * 60 * 1000) {
                timerWidget.classList.add('bg-danger', 'text-white');
                timerWidget.classList.remove('bg-danger', 'bg-opacity-10', 'text-danger');
                timerWidget.style.animationDuration = '0.5s';
            } else {
                timerWidget.classList.add('bg-danger', 'bg-opacity-10', 'text-danger');
                timerWidget.classList.remove('bg-danger', 'text-white');
                timerWidget.style.animationDuration = '2s';
            }
        }
        
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        const addressInput = document.getElementById('address_line');
        const cityInput = document.getElementById('city');
        const phoneInput = document.getElementById('phone');
        const shippingCostDisplay = document.getElementById('shippingCostDisplay');
        const shippingMessageDisplay = document.getElementById('shippingMessageDisplay');
        const cartTotalDisplay = document.getElementById('cartTotalDisplay');
        const usePointsToggle = document.getElementById('usePointsToggle');
        const pointsRow = document.getElementById('pointsRow');
        const pointsValueDisplay = document.getElementById('pointsValueDisplay');
        
        let baseTotal = parseFloat(cartTotalDisplay.dataset.baseTotal);
        const userPointsValue = {{ isset($maxDiscount) ? $maxDiscount : 0 }};
        let currentShippingCost = 0;
        const shippingUrl = "{{ route('checkout.calculateShippingDynamic') }}";

        // Handle Saved Address Selection
        document.querySelectorAll('.address-selector').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                addressInput.value = this.dataset.line;
                cityInput.value = this.dataset.city;
                phoneInput.value = this.dataset.phone;
                calculateShipping();
            });
        });

        function updateTotal() {
            let finalTotal = baseTotal + currentShippingCost;
            let pointsDiscountApplied = 0;

            if (usePointsToggle && usePointsToggle.checked) {
                // Ensure points discount doesn't exceed baseTotal (so they still pay shipping, or minimum £1)
                // Actually, let's allow it to cover the entire subtotal 
                pointsDiscountApplied = Math.min(baseTotal, userPointsValue);
                finalTotal = finalTotal - pointsDiscountApplied;
                
                pointsRow.classList.remove('d-none');
                pointsValueDisplay.textContent = `-£${pointsDiscountApplied.toFixed(2)}`;
            } else if (pointsRow) {
                pointsRow.classList.add('d-none');
            }

            cartTotalDisplay.textContent = `£${Math.max(0, finalTotal).toFixed(2)}`;
        }

        if (usePointsToggle) {
            usePointsToggle.addEventListener('change', updateTotal);
        }

        function calculateShipping() {
            const address = addressInput.value.trim();
            const city = cityInput.value.trim();

            if (!address || !city) return;

            shippingCostDisplay.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>';
            shippingMessageDisplay.textContent = 'Calculating driving distance...';

            fetch(shippingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        address_line: address,
                        city: city
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.cost !== undefined) {
                        currentShippingCost = parseFloat(data.cost);
                        shippingCostDisplay.textContent = currentShippingCost === 0 ? 'FREE' : `£${currentShippingCost.toFixed(2)}`;
                        shippingMessageDisplay.textContent = data.message || '';
                        updateTotal();
                    } else {
                        shippingCostDisplay.textContent = 'Error';
                        shippingMessageDisplay.textContent = 'Could not calculate shipping.';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    currentShippingCost = 5.99;
                    shippingCostDisplay.textContent = '£5.99';
                    shippingMessageDisplay.textContent = 'Flat rate applied due to network error.';
                    updateTotal();
                });
        }

        [addressInput, cityInput].forEach(input => {
            input.addEventListener('blur', calculateShipping);
        });

        // Trigger calculation if address is pre-filled from user profile
        if (addressInput.value.trim() !== '' && cityInput.value.trim() !== '') {
            calculateShipping();
        }

        // Coupon AJAX Success Listener
        document.addEventListener('ajax-form-success', function(e) {
            const { form, data } = e.detail;
            
            if (form.id === 'applyCouponForm' || form.id === 'removeCouponForm') {
                const discountRow = document.getElementById('discountRow');
                const discountValueDisplay = document.getElementById('discountValueDisplay');
                const cartTotalDisplay = document.getElementById('cartTotalDisplay');
                const couponText = document.getElementById('couponText');
                const couponInfoAlert = document.getElementById('couponInfoAlert');
                const applyCouponForm = document.getElementById('applyCouponForm');

                if (data.coupon) {
                    // Applied
                    discountRow.classList.remove('d-none');
                    discountValueDisplay.textContent = `-£${parseFloat(data.coupon.discount).toFixed(2)}`;
                    
                    if (couponInfoAlert) {
                        couponInfoAlert.classList.remove('d-none');
                        couponInfoAlert.style.display = 'flex';
                        couponText.innerHTML = `<strong>${data.coupon.code}</strong> — £${parseFloat(data.coupon.discount).toFixed(2)} off`;
                    } else {
                        // If it didn't exist in the DOM (unlikely due to initial state, but safe), we might need to refresh or have a placeholder
                        location.reload(); // Fallback for complex state
                    }
                    applyCouponForm.style.setProperty('display', 'none', 'important');
                    const avCoupons = document.getElementById('availableCouponsContainer');
                    if (avCoupons) avCoupons.style.display = 'none';
                } else {
                    // Removed
                    discountRow.classList.add('d-none');
                    if (couponInfoAlert) {
                        couponInfoAlert.style.display = 'none';
                    }
                    applyCouponForm.style.setProperty('display', 'flex', 'important');
                    const avCoupons = document.getElementById('availableCouponsContainer');
                    if (avCoupons) avCoupons.style.display = 'block';
                }

                // Update totals
                if (data.total) {
                    baseTotal = parseFloat(data.total.replace(/,/g, ''));
                    cartTotalDisplay.dataset.baseTotal = baseTotal;
                    updateTotal(); // Recalculate full total with shipping and points
                }
            }
        });

        // Payment Method Toggle handler
        const payCard = document.getElementById('pay_card');
        const payBank = document.getElementById('pay_bank');
        const cardContainer = document.getElementById('cardDetailsContainer');
        const bankContainer = document.getElementById('bankDetailsContainer');
        
        function togglePaymentDrawer() {
            if (payCard && payCard.checked) {
                cardContainer.classList.remove('d-none');
                bankContainer.classList.add('d-none');
            } else if (payBank && payBank.checked) {
                cardContainer.classList.add('d-none');
                bankContainer.classList.remove('d-none');
            }
        }
        
        if (payCard && payBank) {
            payCard.addEventListener('change', togglePaymentDrawer);
            payBank.addEventListener('change', togglePaymentDrawer);
            // Run initially to set correct state
            togglePaymentDrawer();
        }

        // ── Stripe Payment Element (card) ─────────────────────────────────
        // Card data is entered in Stripe's iframe; on submit the server prices a
        // PaymentIntent, the browser confirms it with Stripe, then the form posts
        // the intent id for server-side verification + order creation.
        const stripeEnabled = {{ $stripeEnabled ? 'true' : 'false' }};
        const checkoutForm = document.getElementById('checkoutForm');
        const piInput = document.getElementById('payment_intent_id');
        const payErrors = document.getElementById('payment-element-errors');
        let stripe = null, elements = null, paying = false;

        const getItemIds = () =>
            Array.from(document.querySelectorAll('#checkoutForm input[name="items[]"]')).map(i => i.value);
        const showPayError = (msg) => { if (payErrors) payErrors.textContent = msg || ''; };
        const setPaying = (state) => {
            paying = state;
            document.querySelectorAll('button[form="checkoutForm"], #checkoutForm button[type="submit"]').forEach(btn => {
                btn.disabled = state;
                btn.style.opacity = state ? '0.7' : '';
            });
        };

        if (stripeEnabled && window.Stripe) {
            stripe = Stripe('{{ $stripeKey }}');
            const initialAmount = Math.max(100, Math.round((parseFloat(baseTotal) || 1) * 100));
            elements = stripe.elements({
                mode: 'payment',
                amount: initialAmount,
                currency: 'gbp',
                appearance: { theme: 'stripe', variables: { colorPrimary: '#6C5CE7', borderRadius: '10px' } }
            });
            elements.create('payment', { layout: 'tabs' }).mount('#payment-element');
        }

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', async function (e) {
                // Bank transfer, or a card payment already confirmed → normal POST.
                if (payBank && payBank.checked) return;
                if (piInput && piInput.value) return;

                e.preventDefault();

                if (!stripeEnabled || !stripe || !elements) {
                    showPayError('Card payments are unavailable. Please choose Bank Transfer.');
                    return;
                }
                if (paying) return;
                showPayError('');

                if (!addressInput.value.trim() || !cityInput.value.trim() || !phoneInput.value.trim()) {
                    showPayError('Please complete your delivery address first.');
                    return;
                }

                setPaying(true);

                // 1) Validate the card fields in the Element.
                const { error: submitError } = await elements.submit();
                if (submitError) { showPayError(submitError.message); setPaying(false); return; }

                // 2) Server prices the order and returns a PaymentIntent client secret.
                let data;
                try {
                    const res = await fetch("{{ route('checkout.paymentIntent') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ address_line: addressInput.value, city: cityInput.value, items: getItemIds() })
                    });
                    data = await res.json();
                    if (!res.ok) { showPayError(data.error || 'Could not start payment.'); setPaying(false); return; }
                } catch (err) {
                    showPayError('Network error starting payment. Please try again.');
                    setPaying(false); return;
                }

                // 3) Confirm the card payment with Stripe (inline, no redirect).
                const { error: confirmError, paymentIntent } = await stripe.confirmPayment({
                    elements,
                    clientSecret: data.clientSecret,
                    confirmParams: { return_url: window.location.href },
                    redirect: 'if_required'
                });
                if (confirmError) { showPayError(confirmError.message); setPaying(false); return; }

                // 4) Hand the intent id to the server, which verifies and creates the order.
                if (paymentIntent && paymentIntent.status === 'succeeded') {
                    piInput.value = paymentIntent.id;
                    checkoutForm.submit();
                } else {
                    showPayError('Payment was not completed. Please try again.');
                    setPaying(false);
                }
            });
        }
    });
</script>
@endsection