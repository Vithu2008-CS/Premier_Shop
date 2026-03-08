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
                <div class="col-lg-8">
                    @foreach($cart->items as $item)
                        <div class="card mb-3 fade-up delay-{{ $loop->index + 1 }}">
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
                                        <form action="{{ route('cart.update', $item) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="qty-stepper d-flex align-items-center border rounded-3">
                                                <button type="button" class="btn btn-light btn-sm qty-minus border-0 px-2">−</button>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center border-0" style="width:45px;" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-light btn-sm qty-plus border-0 px-2">+</button>
                                            </div>
                                        </form>
                                    </div>
                                    {{-- Line Total + Remove --}}
                                    <div class="col-6 col-md-3 text-end">
                                        <div class="fw-bold mb-2" style="font-size:1.1rem;">£{{ number_format($item->line_total, 2) }}</div>
                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
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
                                <span class="text-muted">Subtotal ({{ $cart->totalItems }} items)</span>
                                <span class="fw-bold">£{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <span class="text-success fw-bold">{{ $cart->subtotal >= 50 ? 'Free' : '£5.99' }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold" style="font-size:1.1rem;">Total</span>
                                <span class="fw-bold gradient-text" style="font-size:1.3rem;">£{{ number_format($cart->subtotal + ($cart->subtotal >= 50 ? 0 : 5.99), 2) }}</span>
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
@endsection
