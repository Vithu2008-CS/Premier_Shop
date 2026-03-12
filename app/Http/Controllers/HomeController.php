<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $promotions = Promotion::active()->get();
        $categories = Category::withCount('products')->get();
        $sliders = \App\Models\Slider::where('is_active', true)->orderBy('order')->get();

        // 1. Offers
        $offerProducts = Product::withActiveOffers()
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // 2. Popular Products
        $popularProducts = Product::where('is_active', true)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->limit(4)
            ->get();

        // 3. New Products
        $newProducts = Product::where('is_active', true)
            ->latest()
            ->limit(4)
            ->get();

        // 4. Random Products
        $randomProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('home', compact(
            'promotions', 'categories', 'sliders', 
            'offerProducts', 'popularProducts', 'newProducts', 'randomProducts'
        ));
    }

    public function offers()
    {
        $offerProducts = Product::withActiveOffers()
            ->where('is_active', true)
            ->with('category')
            ->paginate(12);

        return view('offers', compact('offerProducts'));
    }
}
