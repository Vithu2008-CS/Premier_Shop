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
        
        $offerProducts = Product::with(['category', 'reviews'])->where('is_active', true)
            ->withActiveOffers()
            ->take(4)
            ->get();
            
        $popularProducts = Product::with(['category', 'reviews'])->where('is_active', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(4)
            ->get();
            
        $newProducts = Product::with(['category', 'reviews'])->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();
            
        $randomProducts = Product::with(['category', 'reviews'])->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $promotions = Promotion::banners()->active()->take(3)->get();

        // Recently viewed products
        $recentlyViewed = collect();
        $recentIds = session('recently_viewed', []);
        if (!empty($recentIds)) {
            $recentlyViewed = Product::with(['category', 'reviews'])
                ->where('is_active', true)
                ->whereIn('id', $recentIds)
                ->get()
                ->sortBy(function ($product) use ($recentIds) {
                    return array_search($product->id, $recentIds);
                });
        }

        return view('home', compact('sliders', 'categories', 'offerProducts', 'popularProducts', 'newProducts', 'randomProducts', 'promotions', 'recentlyViewed'));
    }

    public function offers()
    {
        $offerProducts = Product::with(['category', 'reviews'])->where('is_active', true)
            ->withActiveOffers()
            ->paginate(12);
            
        return view('offers', compact('offerProducts'));
    }

    public function categories()
    {
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }])->orderBy('name')->get();

        return view('categories', compact('categories'));
    }
}
