@extends('layouts.app')
@section('title', 'Shopping Cart — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4 fade-up"><i class="bi bi-bag me-2"></i>Shopping <span class="gradient-text">Cart</span></h2>

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
            <div class="row g-4">
                <div class="col-lg-8" id="cart-items-container">
                    @foreach($cart->items as $item)
                        <div class="card mb-3 fade-up delay-{{ $loop->index + 1 }} cart-item-row" id="cart-item-{{ $item->id }}">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3">
                                    {{-- Image --}}
                                    <div class="col-3 col-md-2">
                                        <div class="rounded-3 overflow-hidden" style="aspect-ratio:1;">
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
                                    <div class="col-9 col-md-4">
                                        <h6 class="fw-bold mb-1">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark">{{ $item->product->name }}</a>
                                        </h6>
                                        <p class="text-muted small mb-0">{{ $item->product->category?->name }}</p>
                                        <div class="fw-bold text-primary mt-1">£{{ number_format($item->product->price, 2) }}</div>
                                    </div>
                                    {{-- Quantity --}}
                                    <div class="col-6 col-md-3">
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="cart-update-form">
                                            @csrf @method('PATCH')
                                            <div class="qty-stepper d-flex align-items-center border rounded-3">
                                                <button type="button" class="btn btn-light btn-sm qty-minus border-0 px-2">−</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center border-0 qty-input" style="width:45px;">
                                                <button type="button" class="btn btn-light btn-sm qty-plus border-0 px-2">+</button>
                                            </div>
                                        </form>
                                    </div>
                                    {{-- Line Total + Remove --}}
                                    <div class="col-6 col-md-3 text-end">
                                        <div class="fw-bold mb-2 item-line-total" style="font-size:1.1rem;">£{{ number_format($item->line_total, 2) }}</div>
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="cart-remove-form">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" style="border-radius:8px;"><i class="bi bi-trash3 me-1"></i>Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="col-lg-4">
                    <div class="card fade-up delay-3" style="position:sticky;top:90px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal (<span id="summary-total-items">{{ $cart->totalItems }}</span> items)</span>
                                <span class="fw-bold" id="summary-subtotal">£{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <span class="text-success fw-bold" id="summary-shipping">{{ $cart->subtotal >= 50 ? 'Free' : '£5.99' }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold" style="font-size:1.1rem;">Total</span>
                                <span class="fw-bold gradient-text" style="font-size:1.3rem;" id="summary-total">£{{ number_format($cart->subtotal + ($cart->subtotal >= 50 ? 0 : 5.99), 2) }}</span>
                            </div>
                            <a href="{{ route('checkout.index') }}" class="btn btn-add-cart w-100">
                                <i class="bi bi-lock me-2"></i> Secure Checkout
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2" style="border-radius:50px;">
                                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCartUI(data, itemRow = null) {
        if(data.success) {
            if(itemRow && data.lineTotal) {
                itemRow.querySelector('.item-line-total').textContent = '£' + data.lineTotal;
            }
            document.getElementById('summary-total-items').textContent = data.totalItems;
            document.getElementById('summary-subtotal').textContent = '£' + data.subtotal;
            document.getElementById('summary-shipping').textContent = data.shipping;
            document.getElementById('summary-total').textContent = '£' + data.total;
            
            // Update top nav cart badge if present
            document.querySelectorAll('.cart-badge .badge').forEach(badge => {
                badge.textContent = data.totalItems;
            });
        }
    }

    // Handle Quantity increments/decrements
    document.querySelectorAll('.qty-stepper').forEach(stepper => {
        const input = stepper.querySelector('.qty-input');
        const minusBtn = stepper.querySelector('.qty-minus');
        const plusBtn = stepper.querySelector('.qty-plus');
        const form = stepper.closest('.cart-update-form');

        const triggerUpdate = () => {
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    updateCartUI(data, form.closest('.cart-item-row'));
                } else {
                    alert(data.message || 'Error updating cart');
                    // Reset input to something reasonably valid if possible, or just reload on error
                }
            });
        };

        minusBtn.addEventListener('click', () => {
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
                triggerUpdate();
            }
        });

        plusBtn.addEventListener('click', () => {
            if (parseInt(input.value) < parseInt(input.max)) {
                input.value = parseInt(input.value) + 1;
                triggerUpdate();
            }
        });

        input.addEventListener('change', () => {
            if(input.value < 1) input.value = 1;
            if(parseInt(input.value) > parseInt(input.max)) input.value = input.max;
            triggerUpdate();
        });
    });

    // Handle Removals
    document.querySelectorAll('.cart-remove-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = form.querySelector('button');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btn.disabled = true;

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(data.empty) {
                        window.location.reload(); // Reload to show empty state
                    } else {
                        const row = form.closest('.cart-item-row');
                        row.classList.remove('fade-up');
                        row.style.transition = 'all 0.4s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            row.remove();
                            updateCartUI(data);
                        }, 400);
                    }
                } else {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    alert(data.message || 'Error removing item');
                }
            })
            .catch(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                alert('Connection error');
            });
        });
    });
});
</script>
@endpush
@endsection
