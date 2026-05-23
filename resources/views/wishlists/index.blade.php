{{--
    wishlists/index.blade.php — Customer wishlist
    ===============================================
    Grid of wishlisted products using the product_card partial.
    Remove uses the wishlist toggle AJAX handler in layouts/app.blade.php
    (removes the card from DOM without reload when on the wishlist page).
    Variable: $wishlistItems — UserItem rows (type='wishlist') with product loaded
--}}
@extends('layouts.app')
@section('title', 'My Wishlist — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4 reveal-3d">My <span class="gradient-text">Wishlist</span></h2>

        @if($wishlists->isEmpty())
            <div class="text-center py-5 reveal-3d">
                <i class="bi bi-heart text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                <h4>Your wishlist is empty</h4>
                <p class="text-muted">Start saving your favorite items for later.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2 rounded-pill px-4">Browse Products</a>
            </div>
        @else
            <div class="row g-4 stagger-children">
                @foreach($wishlists as $index => $wishlist)
                    @php $product = $wishlist->product; @endphp
                    @include('partials.product_card', ['product' => $product, 'delay' => ($index % 8) + 1])
                @endforeach
            </div>
            <div class="mt-4">{{ $wishlists->links() }}</div>
        @endif
    </div>
</section>
@endsection
