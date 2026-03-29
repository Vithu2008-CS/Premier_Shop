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

        $sliders = cache()->remember('home_sliders', 300, function () {
            return Promotion::sliders()->active()->orderBy('order_priority')->get();
        });

        $categories = cache()->remember('home_categories', 300, function () {
            return Category::all();
        });
        
        $offerProducts = cache()->remember('home_offers', 300, function () {
            return Product::with(['category', 'reviews'])->where('is_active', true)
                ->withActiveOffers()
                ->take(4)
                ->get();
        });
            
        $popularProducts = cache()->remember('home_popular', 300, function () {
            return Product::with(['category', 'reviews'])->where('is_active', true)
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(4)
                ->get();
        });
            
        $newProducts = cache()->remember('home_new', 300, function () {
            return Product::with(['category', 'reviews'])->where('is_active', true)
                ->latest()
                ->take(4)
                ->get();
        });
            
        $randomProducts = cache()->remember('home_random', 60, function () {
            return Product::with(['category', 'reviews'])->where('is_active', true)
                ->inRandomOrder()
                ->take(4)
                ->get();
        });

        $promotions = cache()->remember('home_promotions', 300, function () {
            return Promotion::banners()->active()->take(3)->get();
        });

        // Recently viewed products
        $recentlyViewed = collect();
        if (auth()->check()) {
            $recentlyViewed = \App\Models\RecentlyViewed::getForUser(auth()->id(), 8);
        } else {
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
