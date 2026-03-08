@extends('layouts.app')
@section('title', 'Checkout - Premier Shop')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Checkout</h2>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Shipping Address</h5>
                    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Address Line</label>
                            <input type="text" name="address_line" class="form-control @error('address_line') is-invalid @enderror" value="{{ old('address_line', auth()->user()->address) }}" required>
                            @error('address_line') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', auth()->user()->city) }}" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', auth()->user()->phone) }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                            <i class="bi bi-lock me-1"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Coupon --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-tag me-1"></i> Got a Coupon?</h5>
                    @if(session('coupon'))
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <span><strong>{{ session('coupon.code') }}</strong> — £{{ number_format(session('coupon.discount'), 2) }} off</span>
                        <form action="{{ route('checkout.removeCoupon') }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('checkout.applyCoupon') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="coupon_code" class="form-control" placeholder="Enter coupon code">
                        <button type="submit" class="btn btn-outline-primary">Apply</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card shadow-sm" style="position:sticky;top:100px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    @foreach($cart->items as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ Str::limit($item->product->name, 25) }} x{{ $item->quantity }}</span>
                        <span>£{{ number_format($item->line_total, 2) }}</span>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>£{{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    @if(session('coupon'))
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount</span>
                        <span>-£{{ number_format(session('coupon.discount'), 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span id="shippingCostDisplay">Enter address</span>
                    </div>
                    <div id="shippingMessageDisplay" class="text-muted small text-end fst-italic mb-2"></div>
                    <hr>
                    @php
                    $subtotalMinusDiscount = $cart->subtotal - (session('coupon.discount') ?? 0);
                    @endphp
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary" id="cartTotalDisplay" data-base-total="{{ $subtotalMinusDiscount }}">£{{ number_format($subtotalMinusDiscount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressInput = document.querySelector('input[name="address_line"]');
        const cityInput = document.querySelector('input[name="city"]');
        const shippingCostDisplay = document.getElementById('shippingCostDisplay');
        const shippingMessageDisplay = document.getElementById('shippingMessageDisplay');
        const cartTotalDisplay = document.getElementById('cartTotalDisplay');
        const baseTotal = parseFloat(cartTotalDisplay.dataset.baseTotal);
        const shippingUrl = "{{ route('checkout.calculateShipping') }}";

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
    });
</script>
@endsection