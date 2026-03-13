@extends('layouts.app')
@section('title', 'Shopping Cart — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d"><i class="bi bi-bag me-2"></i>Shopping <span class="gradient-text">Cart</span></h2>

        @if(!$cart || $cart->items->isEmpty())
            <div class="text-center py-5 fade-up">
                <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:rgba(108,92,231,0.08);border-radius:50%;">
                    <i class="bi bi-bag-x text-primary" style="font-size:3rem;"></i>
                </div>
                <h4 class="fw-bold mb-2">Your cart is empty</h4>
                <p class="text-muted mb-4">Looks like you haven't added anything yet!</p>
                <a href="{{ route('products.index') }}" class="btn btn-add-cart">
                    <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                </a>
            </div>
        @else
            <form id="cart-form" action="{{ route('checkout.index') }}" method="GET">
                <div class="row g-4">
                    <div class="col-lg-8">
                        {{-- Selection Header --}}
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input border-primary" type="checkbox" id="selectAll" checked>
                                    <label class="form-check-label fw-bold ms-2" for="selectAll">Select All Items</label>
                                </div>
                                <span class="text-muted small" id="selected-count">{{ $cart->items->count() }} item(s) selected</span>
                            </div>
                        </div>

                        <div id="cart-items-container" class="stagger-children">
                            @foreach($cart->items as $item)
                                <div class="card mb-3 fade-up delay-{{ $loop->index + 1 }} cart-item-row" id="cart-item-{{ $item->id }}" data-price="{{ $item->product->price }}" data-id="{{ $item->id }}">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-3">
                                            {{-- Checkbox --}}
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input item-checkbox border-primary" type="checkbox" name="items[]" value="{{ $item->id }}" checked>
                                                </div>
                                            </div>
                                            {{-- Image --}}
                                            <div class="col-3 col-md-2">
                                                <div class="rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1;">
                                                    @if($item->product->images && count($item->product->images) > 0)
                                                        <img src="{{ $item->product->images[0] }}" class="w-100 h-100" style="object-fit:cover;" alt="{{ $item->product->name }}">
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center h-100 w-100" style="background:#f0f0f5;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Details --}}
                                            <div class="col-7 col-md-3">
                                                <h6 class="fw-bold mb-1">
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark">{{ $item->product->name }}</a>
                                                </h6>
                                                <p class="text-muted small mb-0">{{ $item->product->category?->name }}</p>
                                                <div class="fw-bold text-primary mt-1">£{{ number_format($item->product->price, 2) }}</div>
                                            </div>
                                            {{-- Quantity --}}
                                            <div class="col-6 col-md-3">
                                                <div class="mb-1 small fw-bold text-muted">Quantity:</div>
                                                <div class="qty-stepper d-flex align-items-center border rounded-3 bg-white">
                                                    <button type="button" class="btn btn-light btn-sm qty-minus border-0 px-2" data-item-id="{{ $item->id }}">−</button>
                                                    <input type="number" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center border-0 qty-input fw-bold" style="width:45px;" readonly>
                                                    <button type="button" class="btn btn-light btn-sm qty-plus border-0 px-2" data-item-id="{{ $item->id }}">+</button>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">Stock: {{ $item->product->stock }}</small>
                                            </div>
                                            {{-- Line Total + Remove --}}
                                            <div class="col-6 col-md-2 text-end">
                                                <div class="small fw-bold text-muted mb-1">Item Total:</div>
                                                <div class="fw-bold mb-2 item-line-total text-dark" id="line-total-{{ $item->id }}" style="font-size:1.1rem;">£{{ number_format($item->line_total, 2) }}</div>
                                                <button type="button" class="btn btn-link text-danger p-0 text-decoration-none small remove-item-btn" data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-trash3 me-1"></i>Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Order Summary --}}
                    <div class="col-lg-4 reveal-slide-right">
                        <div class="card fade-up shadow-sm border-0" style="position:sticky;top:90px; border-radius: 20px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal (<span id="summary-total-items">0</span> items)</span>
                                    <span class="fw-bold" id="summary-subtotal">£0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Shipping</span>
                                    <span class="fw-bold" id="summary-shipping">£0.00</span>
                                </div>
                                <hr class="my-4">
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold fs-5">Total</span>
                                    <span class="fw-bold gradient-text fs-4" id="summary-total">£0.00</span>
                                </div>
                                
                                <div id="checkout-error" class="alert alert-danger py-2 small d-none">Please select at least one item.</div>

                                <button type="submit" id="checkout-btn" class="btn btn-add-cart w-100 py-3 fw-bold">
                                    <i class="bi bi-lock me-2"></i> Proceed to Checkout
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2 border-0" style="border-radius:50px;">
                                    <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                                </a>
                                
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex align-items-center gap-3 text-muted small">
                                        <i class="bi bi-shield-check fs-4 text-success"></i>
                                        <span>Secure 256-bit SSL encrypted checkout.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</section>

{{-- Hidden forms for AJAX --}}
<form id="qty-update-form" method="POST" style="display:none;">@csrf @method('PATCH')<input type="hidden" name="quantity" id="update-qty-val"></form>
<form id="item-remove-form" method="POST" style="display:none;">@csrf @method('DELETE')</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryShipping = document.getElementById('summary-shipping');
    const summaryTotal = document.getElementById('summary-total');
    const summaryCount = document.getElementById('summary-total-items');
    const selectedCountLabel = document.getElementById('selected-count');
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutError = document.getElementById('checkout-error');
    
    // Shipping Constants (Fallback)
    let threshold = 50;
    let flatRate = 5.99;

    // Fetch actual settings
    fetch('/api/shipping-settings').then(r => r.json()).then(data => {
        threshold = data.free_delivery_threshold || 50;
        flatRate = data.flat_rate_fee || 5.99;
        calculateTotals();
    }).catch(() => calculateTotals());

    function calculateTotals() {
        let subtotal = 0;
        let count = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                const row = cb.closest('.cart-item-row');
                const id = row.dataset.id;
                const price = parseFloat(row.dataset.price);
                const qty = parseInt(document.getElementById('qty-' + id).value);
                subtotal += price * qty;
                count += qty;
            }
        });

        const shipping = (subtotal >= threshold || subtotal === 0) ? 0 : flatRate;
        const total = subtotal + shipping;

        summarySubtotal.textContent = '£' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        summaryShipping.textContent = shipping === 0 ? 'Free' : '£' + shipping.toFixed(2);
        summaryShipping.className = shipping === 0 ? 'text-success fw-bold' : 'fw-bold';
        summaryTotal.textContent = '£' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        summaryCount.textContent = count;
        selectedCountLabel.textContent = itemCheckboxes.filter(c => c.checked).length + ' item(s) selected';
        
        checkoutBtn.disabled = itemCheckboxes.filter(c => c.checked).length === 0;
        if(checkoutBtn.disabled) {
            checkoutError.classList.remove('d-none');
        } else {
            checkoutError.classList.add('d-none');
        }
    }

    // Helper for NodeList filtering
    NodeList.prototype.filter = Array.prototype.filter;

    // Select All logic
    selectAll.addEventListener('change', function() {
        itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        calculateTotals();
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAll.checked = itemCheckboxes.filter(c => c.checked).length === itemCheckboxes.length;
            calculateTotals();
        });
    });

    // Quantity Update
    document.querySelectorAll('.qty-stepper button').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.itemId;
            const input = document.getElementById('qty-' + id);
            let val = parseInt(input.value);
            
            if (this.classList.contains('qty-plus')) {
                if(val < parseInt(input.max)) val++;
            } else {
                if(val > 1) val--;
            }
            
            input.value = val;
            
            // AJAX Update
            const form = document.getElementById('qty-update-form');
            form.action = '/cart/' + id;
            document.getElementById('update-qty-val').value = val;
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('line-total-' + id).textContent = '£' + data.lineTotal;
                    calculateTotals();
                    // Update header badge
                    document.querySelectorAll('.cart-badge .badge').forEach(b => b.textContent = data.totalItems);
                } else {
                    alert(data.message);
                }
            });
        });
    });

    // Remove logic
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if(!confirm('Remove this item?')) return;
            const id = this.dataset.itemId;
            const form = document.getElementById('item-remove-form');
            form.action = '/cart/' + id;
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    if(data.empty) window.location.reload();
                    else {
                        document.getElementById('cart-item-' + id).remove();
                        calculateTotals();
                        document.querySelectorAll('.cart-badge .badge').forEach(b => b.textContent = data.totalItems);
                    }
                }
            });
        });
    });

    // Initialize
    calculateTotals();
});
</script>
@endpush
@endsection
