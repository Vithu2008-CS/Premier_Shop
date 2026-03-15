<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isDriver()) {
            return redirect()->route('driver.dashboard');
        }

        $sliders = Promotion::sliders()->active()->orderBy('order_priority')->get();
        $categories = Category::all();
        
        $offerProducts = Product::where('is_active', true)
            ->withActiveOffers()
            ->take(4)
            ->get();
            
        $popularProducts = Product::where('is_active', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(4)
            ->get();
            
        $newProducts = Product::where('is_active', true)
            ->latest()
            ->take(4)
            ->get();
            
        $randomProducts = Product::where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $promotions = Promotion::banners()->active()->take(3)->get();

        return view('home', compact('sliders', 'categories', 'offerProducts', 'popularProducts', 'newProducts', 'randomProducts', 'promotions'));
    }

    public function offers()
    {
        $offerProducts = Product::where('is_active', true)
            ->withActiveOffers()
            ->paginate(12);
            
        return view('offers', compact('offerProducts'));
    }
}
