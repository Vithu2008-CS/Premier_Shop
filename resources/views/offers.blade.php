@extends('layouts.app')
@section('title', 'Offers — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 reveal-3d">
            <span class="badge bg-danger px-3 py-2 mb-3" style="font-size:0.85rem;">
                <i class="bi bi-lightning-fill me-1"></i> Limited Time Deals
            </span>
            <h1 class="section-title">Special <span class="gradient-text">Offers</span></h1>
            <p class="section-subtitle">Buy in bulk and save! The more you buy, the more you save.</p>
        </div>

        @if($offerProducts->isEmpty())
            <div class="text-center py-5 reveal-3d">
                <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;background:rgba(108,92,231,0.08);border-radius:50%;">
                    <i class="bi bi-tag text-primary" style="font-size:3rem;"></i>
                </div>
                <h4 class="fw-bold mb-2">No offers currently available</h4>
                <p class="text-muted mb-4">Check back soon for amazing deals!</p>
                <a href="{{ route('products.index') }}" class="btn btn-add-cart">Browse All Products</a>
            </div>
        @else
            <div class="row g-4 stagger-children">
                @foreach($offerProducts as $index => $product)
                    @include('partials.product_card', ['delay' => ($index % 8) + 1])
                @endforeach
            </div>
            <div class="mt-4">{{ $offerProducts->links() }}</div>
        @endif
    </div>
</section>
@endsection
