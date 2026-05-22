<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isDriver()) {
            return redirect()->route('driver.dashboard');
        }

        $isUnder16 = auth()->check() && auth()->user()->isUnder16();
        $suffix = $isUnder16 ? '_restricted' : '_all';

        $sliders = cache()->remember('home_sliders', 300, function () {
            return Promotion::sliders()->active()->orderBy('order_priority')->get();
        });

        $categories = cache()->remember('home_categories', 300, function () {
            return Category::all();
        });

        $offerProducts = cache()->remember("home_offers{$suffix}", 300, function () use ($isUnder16) {
            $query = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->withActiveOffers()->take(4)->get();
        });

        $popularProducts = cache()->remember("home_popular{$suffix}", 300, function () use ($isUnder16) {
            $query = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(4)
                ->get();
        });

        $newProducts = cache()->remember("home_new{$suffix}", 300, function () use ($isUnder16) {
            $query = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->latest()->take(4)->get();
        });

        $randomProducts = cache()->remember("home_random{$suffix}", 60, function () use ($isUnder16) {
            $query = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->inRandomOrder()->take(4)->get();
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
            if (! empty($recentIds)) {
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
        $query = Product::with(['category', 'reviews'])->where('is_active', true);

        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $offerProducts = $query->withActiveOffers()->paginate(12);

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
