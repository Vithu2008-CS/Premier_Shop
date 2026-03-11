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
        $featuredProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();
        $categories = Category::withCount('products')->get();

        // Products with active offers for offer banner
        $offerProducts = Product::withActiveOffers()
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $sliders = \App\Models\Slider::where('is_active', true)->orderBy('order')->get();

        return view('home', compact('promotions', 'featuredProducts', 'categories', 'offerProducts', 'sliders'));
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
