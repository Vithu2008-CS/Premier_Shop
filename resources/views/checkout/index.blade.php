@extends('layouts.app')
@section('title', 'Checkout - Premier Shop')

@section('content')
<section class="section-padding">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d"><i class="bi bi-bag-check me-2"></i>Check<span class="gradient-text">out</span></h2>
    <div class="row g-4">
        <div class="col-lg-7 reveal-slide-left">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5">
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
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 bg-white" style="border-radius: 12px; min-width: 250px;">
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
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm" style="position:sticky;top:100px; border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="checkout-item-list mb-4 overflow-auto" style="max-height: 250px;">
                        @foreach($items as $item)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span class="bg-light rounded text-muted small fw-bold d-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;">{{ $item->quantity }}</span>
                                <span class="small truncate-1" style="max-width: 180px;">{{ $item->product->name }}</span>
                            </div>
                            <span class="small fw-bold">£{{ number_format($item->line_total, 2) }}</span>
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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Shipping</span>
                        <span id="shippingCostDisplay" class="small fw-bold text-primary">£0.00</span>
                    </div>
                    <div id="shippingMessageDisplay" class="text-muted x-small text-end fst-italic mb-3"></div>
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
                        <button type="button" onclick="document.getElementById('checkoutForm').submit()" class="btn btn-primary btn-lg w-100 py-3" style="border-radius: 50px; background: var(--ps-gradient); border: none; font-weight: 700; box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2);">
                            <i class="bi bi-shield-lock-fill me-2"></i> Place Order
                        </button>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <div class="d-inline-flex align-items-center gap-2 text-muted x-small">
                            <i class="bi bi-shield-fill-check text-success"></i>
                            Secure SSL Checkout
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressInput = document.getElementById('address_line');
        const cityInput = document.getElementById('city');
        const phoneInput = document.getElementById('phone');
        const shippingCostDisplay = document.getElementById('shippingCostDisplay');
        const shippingMessageDisplay = document.getElementById('shippingMessageDisplay');
        const cartTotalDisplay = document.getElementById('cartTotalDisplay');
        const baseTotal = parseFloat(cartTotalDisplay.dataset.baseTotal);
        const shippingUrl = "{{ route('checkout.calculateShipping') }}";

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
                        const shippingCost = parseFloat(data.cost);
                        shippingCostDisplay.textContent = shippingCost === 0 ? 'FREE' : `£${shippingCost.toFixed(2)}`;
                        shippingMessageDisplay.textContent = data.message || '';

                        const newTotal = baseTotal + shippingCost;
                        cartTotalDisplay.textContent = `£${newTotal.toFixed(2)}`;
                    } else {
                        shippingCostDisplay.textContent = 'Error';
                        shippingMessageDisplay.textContent = 'Could not calculate shipping.';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    shippingCostDisplay.textContent = '£5.99';
                    shippingMessageDisplay.textContent = 'Flat rate applied due to network error.';
                    cartTotalDisplay.textContent = `£${(baseTotal + 5.99).toFixed(2)}`;
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
                } else {
                    // Removed
                    discountRow.classList.add('d-none');
                    if (couponInfoAlert) {
                        couponInfoAlert.style.display = 'none';
                    }
                    applyCouponForm.style.setProperty('display', 'flex', 'important');
                }

                // Update totals
                if (data.total) {
                    cartTotalDisplay.dataset.baseTotal = data.total.replace(/,/g, '');
                    calculateShipping(); // Recalculate full total with shipping
                }
            }
        });
    });
</script>
@endsection