{{--
    cart/index.blade.php — Shopping cart page
    ==========================================
    Lists all cart items (UserItem, type='cart') with quantity controls,
    per-line subtotals applying bulk-buy offers when threshold met,
    cart total summary, proceed to checkout button.
    Quantity +/- uses AJAX PATCH to cart.update.
    Remove uses AJAX DELETE to cart.remove.
    "Select items" checkboxes allow subset checkout via cart.buyNow.
    Variable: $cartItems — auth user's cart items with product loaded
--}}
@extends('layouts.app')
@section('title', 'Shopping Cart — Premier Shop')

@section('content')
<section class="section-padding">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d"><i class="bi bi-bag me-2"></i>Shopping <span class="gradient-text">Cart</span></h2>

        @if($items->isEmpty())
            <div class="text-center py-5 fade-up">
                <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:rgba(116, 48, 137,0.08);border-radius:50%;">
                    <i class="bi bi-bag-x text-primary" style="font-size:3rem;"></i>
                </div>
                <h4 class="fw-bold mb-2">Your cart is empty</h4>
                <p class="text-muted mb-4">Looks like you haven't added anything yet!</p>
                <a href="{{ route('products.index') }}" class="btn btn-brand rounded-pill px-4 py-2">
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
                                <span class="text-muted small" id="selected-count">{{ $items->count() }} item(s) selected</span>
                            </div>
                        </div>

                        <div id="cart-items-container" class="stagger-children">
                            @foreach($items as $item)
                                <div class="card mb-3 fade-up delay-{{ $loop->index + 1 }} cart-item-row"
                                     id="cart-item-{{ $item->id }}"
                                     data-price="{{ $item->product->price }}"
                                     data-id="{{ $item->id }}"
                                     data-has-offer="{{ $item->product->has_offer ? '1' : '0' }}"
                                     data-offer-price="{{ $item->product->offer_price }}"
                                     data-offer-min-qty="{{ $item->product->offer_min_qty }}"
                                     data-line-total="{{ $item->line_total }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex d-md-none align-items-center mb-3">
                                            <div class="form-check me-2">
                                                <input class="form-check-input item-checkbox border-primary" type="checkbox" name="items[]" value="{{ $item->id }}" checked>
                                            </div>
                                            <h6 class="fw-bold mb-0 truncate-1">
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-body">{{ $item->product->name }}</a>
                                            </h6>
                                        </div>

                                        <div class="row align-items-center g-3">
                                            {{-- Checkbox (Desktop only) --}}
                                            <div class="col-auto d-none d-md-block">
                                                <div class="form-check">
                                                    <input class="form-check-input item-checkbox border-primary" type="checkbox" name="items[]" value="{{ $item->id }}" checked>
                                                </div>
                                            </div>
                                            {{-- Image --}}
                                            <div class="col-4 col-md-2">
                                                <div class="rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1;">
                                                    @if($item->product->images && count($item->product->images) > 0)
                                                        <img src="{{ $item->product->images[0] }}" class="w-100 h-100" style="object-fit:cover;" alt="{{ $item->product->name }}">
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center h-100 w-100" style="background: var(--ps-surface-secondary);">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Details --}}
                                            <div class="col-8 col-md-3">
                                                <div class="d-none d-md-block">
                                                    <h6 class="fw-bold mb-1">
                                                        <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-body">{{ $item->product->name }}</a>
                                                    </h6>
                                                    <p class="text-muted small mb-0">{{ $item->product->category?->name }}</p>
                                                </div>
                                                <div class="d-md-none">
                                                    <p class="text-muted small mb-1">{{ $item->product->category?->name }}</p>
                                                    <div class="fw-bold text-primary">£{{ number_format($item->product->active_price, 2) }}
                                                        @if($item->product->active_price < $item->product->price)
                                                            <small class="text-muted text-decoration-line-through ms-1" style="font-size:.72rem;">£{{ number_format($item->product->price, 2) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-none d-md-block fw-bold text-primary mt-1">£{{ number_format($item->product->active_price, 2) }}
                                                    @if($item->product->active_price < $item->product->price)
                                                        <small class="text-muted text-decoration-line-through ms-1" style="font-size:.8rem;">£{{ number_format($item->product->price, 2) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Quantity --}}
                                            <div class="col-7 col-md-3">
                                                <div class="mb-1 small fw-bold text-muted d-none d-md-block">Quantity:</div>
                                                <div class="qty-stepper d-flex align-items-center border rounded-3 shadow-sm p-1" style="background: var(--ps-surface-bg);">
                                                    <button type="button" class="btn btn-light btn-sm qty-minus border-0 px-3 py-2" data-item-id="{{ $item->id }}" style="height: 44px; border-radius: 8px;">−</button>
                                                    <input type="number" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control text-center border-0 qty-input fw-bold bg-transparent" style="width:50px; font-size: 1.1rem; color: var(--ps-text);" readonly>
                                                    <button type="button" class="btn btn-light btn-sm qty-plus border-0 px-3 py-2" data-item-id="{{ $item->id }}" style="height: 44px; border-radius: 8px;">+</button>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Stock: {{ $item->product->stock }}</small>
                                            </div>
                                            {{-- Line Total + Remove --}}
                                            <div class="col-5 col-md-2 text-end">
                                                <div class="small fw-bold text-muted mb-1 d-none d-md-block">Item Total:</div>
                                                <div class="fw-bold mb-2 item-line-total text-body" id="line-total-{{ $item->id }}" style="font-size:1.15rem;">£{{ number_format($item->line_total, 2) }}</div>
                                                <button type="button" class="btn btn-link text-danger p-0 text-decoration-none small remove-item-btn fw-bold" data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-trash3-fill me-1"></i>Remove
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
                        <div style="position:sticky;top:90px;" class="d-flex flex-column gap-4">
                            
                            <!-- Checkout Perks Card (Dynamic Milestones) -->
                            <div class="card fade-up shadow-sm border-0" style="border-radius: 20px;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center text-body" style="font-family: 'Outfit', sans-serif;">
                                        <i class="bi bi-gift text-primary me-2"></i> Active Checkout Perks
                                    </h6>
                                    
                                    <!-- Perk 1: Free Local Delivery -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1 small">
                                            <span class="text-muted">Free Delivery Target</span>
                                            <span class="fw-bold text-body" id="perk-shipping-value">£0 / £50</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 10px; background: var(--ps-surface-secondary);">
                                            <div id="perk-shipping-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%; border-radius: 10px;"></div>
                                        </div>
                                    </div>

                                    <!-- Perk 2: 1.5x Rewards Booster -->
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1 small">
                                            <span class="text-muted">1.5x Points Booster Target</span>
                                            <span class="fw-bold text-body" id="perk-points-value">£0 / £100</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 10px; background: var(--ps-surface-secondary);">
                                            <div id="perk-points-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; border-radius: 10px;"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Milestone Insight Banner -->
                                    <div class="p-3 mt-3 rounded-4 border border-dashed text-start" style="background: rgba(116, 48, 137, 0.05); border-color: var(--ps-primary) !important;">
                                        <p id="perk-insight-text" class="small text-muted mb-0 fw-semibold" style="line-height: 1.5;">
                                            Calculating eligible order incentives...
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Order Summary Card -->
                            <div class="card fade-up shadow-sm border-0" style="border-radius: 20px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal (<span id="summary-total-items">0</span> items)</span>
                                    <span class="fw-bold" id="summary-subtotal">£0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 text-success d-none" id="summary-savings-row">
                                    <span class="small">You save</span>
                                    <span class="fw-bold small" id="summary-savings">-£0.00</span>
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

                                <button type="submit" id="checkout-btn" class="btn btn-accent w-100 py-3 fw-bold rounded-pill">
                                    <i class="bi bi-lock-fill me-2"></i> Proceed to Checkout
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
                            </div> <!-- Close the sticky container div -->
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
<script nonce="{{ Vite::cspNonce() }}">
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

        let savings = 0;
        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                const row = cb.closest('.cart-item-row');
                const id = row.dataset.id;
                const qty = parseInt(document.getElementById('qty-' + id).value);
                // Authoritative per-line total from the server (covers retail, bulk and
                // personalised offers); refreshed on every quantity change.
                const lineTotal = parseFloat(row.dataset.lineTotal) || 0;
                const fullPrice = (parseFloat(row.dataset.price) || 0) * qty;
                subtotal += lineTotal;
                savings += Math.max(0, fullPrice - lineTotal);
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

        const savingsRow = document.getElementById('summary-savings-row');
        const savingsEl = document.getElementById('summary-savings');
        if (savingsRow && savingsEl) {
            if (savings > 0.005) {
                savingsEl.textContent = '-£' + savings.toFixed(2);
                savingsRow.classList.remove('d-none');
            } else {
                savingsRow.classList.add('d-none');
            }
        }
        
        checkoutBtn.disabled = itemCheckboxes.filter(c => c.checked).length === 0;
        if(checkoutBtn.disabled) {
            checkoutError.classList.remove('d-none');
        } else {
            checkoutError.classList.add('d-none');
        }

        // Dynamic Checkout Perks Progress Logic
        const shippingBar = document.getElementById('perk-shipping-bar');
        const shippingLabel = document.getElementById('perk-shipping-value');
        const pointsBar = document.getElementById('perk-points-bar');
        const pointsLabel = document.getElementById('perk-points-value');
        const insightText = document.getElementById('perk-insight-text');

        if (shippingBar && pointsBar && insightText) {
            // Shipping calculation
            const shipPct = Math.min((subtotal / threshold) * 100, 100);
            shippingBar.style.width = shipPct + '%';
            shippingLabel.textContent = '£' + Math.min(subtotal, threshold).toFixed(0) + ' / £' + threshold;
            
            // Points calculation (Target 100)
            const pointsTarget = 100;
            const pointsPct = Math.min((subtotal / pointsTarget) * 100, 100);
            pointsBar.style.width = pointsPct + '%';
            pointsLabel.textContent = '£' + Math.min(subtotal, pointsTarget).toFixed(0) + ' / £' + pointsTarget;
            
            // Dynamic helpful text recommendations
            if (subtotal === 0) {
                insightText.innerHTML = "Select items above to activate milestones.";
            } else if (subtotal < threshold) {
                const diff = threshold - subtotal;
                insightText.innerHTML = `<i class="bi bi-truck text-primary me-1"></i> Add <strong>£${diff.toFixed(2)}</strong> more to unlock <strong>Free Local Delivery</strong>!`;
            } else if (subtotal < pointsTarget) {
                const diff = pointsTarget - subtotal;
                insightText.innerHTML = `<i class="bi bi-award-fill text-success me-1"></i> Free delivery unlocked! Add <strong>£${diff.toFixed(2)}</strong> more to activate a <strong>1.5x Rewards Point Booster</strong>!`;
            } else {
                insightText.innerHTML = `<i class="bi bi-stars text-warning me-1"></i> <strong>Elite Status:</strong> Free delivery & 1.5x loyalty multiplier activated!`;
            }
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
                    const updatedRow = document.getElementById('cart-item-' + id);
                    if (updatedRow) updatedRow.dataset.lineTotal = String(data.lineTotal).replace(/,/g, '');
                    calculateTotals();
                    // Update header badge
                    document.querySelectorAll('.cart-count-badge').forEach(b => {
                        b.textContent = data.totalItems;
                        b.style.display = data.totalItems > 0 ? 'inline-block' : 'none';
                    });
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
                        document.querySelectorAll('.cart-count-badge').forEach(b => {
                            b.textContent = data.totalItems;
                            b.style.display = data.totalItems > 0 ? 'inline-block' : 'none';
                        });
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
