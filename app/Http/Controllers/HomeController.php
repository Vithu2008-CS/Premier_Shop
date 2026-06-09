<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;

/**
 * Renders the public storefront home page, offers page, and categories listing.
 * All product queries are split by age restriction so under-16 users only see
 * age-appropriate products. Results are cached for 5 minutes to reduce DB load.
 */
class HomeController extends Controller
{
    /**
     * Build the home page with sliders, categories, and product sections.
     * Drivers are immediately redirected to their own dashboard on login.
     * Cache keys are suffixed with '_restricted' / '_all' so under-16 users
     * get a separate cache entry and never see age-restricted products.
     */
    public function index()
    {
        // Drivers land on their delivery dashboard, not the shop home
        if (auth()->check() && auth()->user()->isDriver()) {
            return redirect()->route('driver.dashboard');
        }

        $isUnder16 = auth()->check() && auth()->user()->isUnder16();
        // Suffix differentiates cache entries per age group
        $suffix    = $isUnder16 ? '_restricted' : '_all';

        // Main hero carousel sliders (type='slider')
        $mainSliders = cache()->remember('home_sliders_main', 300, fn () =>
            Promotion::where('type', 'slider')->active()->orderBy('order_priority')->get()
        );

        // Sub-banner 1 — shown after New Arrivals section (type='slider_mid')
        $subSliders1 = cache()->remember('home_sliders_mid', 300, fn () =>
            Promotion::where('type', 'slider_mid')->active()->orderBy('order_priority')->get()
        );

        // Sub-banner 2 — shown after Recently Viewed section (type='slider_top')
        $subSliders2 = cache()->remember('home_sliders_top', 300, fn () =>
            Promotion::where('type', 'slider_top')->active()->orderBy('order_priority')->get()
        );

        $categories = cache()->remember('home_categories', 300, fn () =>
            Category::all()
        );

        // Products currently on bulk/offer pricing
        $offerProducts = cache()->remember("home_offers{$suffix}", 300, function () use ($isUnder16) {
            $q = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $q->where('is_age_restricted', false);
            }

            return $q->withActiveOffers()->take(4)->get();
        });

        // Best-selling products ordered by order item count
        $popularProducts = cache()->remember("home_popular{$suffix}", 300, function () use ($isUnder16) {
            $q = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $q->where('is_age_restricted', false);
            }

            return $q->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(4)
                ->get();
        });

        // Newest products by created_at
        $newProducts = cache()->remember("home_new{$suffix}", 300, function () use ($isUnder16) {
            $q = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $q->where('is_age_restricted', false);
            }

            return $q->latest()->take(4)->get();
        });

        // Random picks — shorter 1-min cache so the section feels fresh on repeat visits
        $randomProducts = cache()->remember("home_random{$suffix}", 60, function () use ($isUnder16) {
            $q = Product::with(['category', 'reviews'])->where('is_active', true);
            if ($isUnder16) {
                $q->where('is_age_restricted', false);
            }

            return $q->inRandomOrder()->take(4)->get();
        });

        // Banner-style promotional images beneath the product sections
        $promotions = cache()->remember('home_promotions', 300, fn () =>
            Promotion::banners()->active()->take(3)->get()
        );

        // Recently viewed — DB for logged-in users, session array for guests
        $recentlyViewed = collect();
        if (auth()->check()) {
            $recentlyViewed = \App\Models\RecentlyViewed::getForUser(auth()->id(), 8);
        } else {
            $recentIds = session('recently_viewed', []);
            if (! empty($recentIds)) {
                // Preserve the session order (most recent first) after fetching
                $recentlyViewed = Product::with(['category', 'reviews'])
                    ->where('is_active', true)
                    ->whereIn('id', $recentIds)
                    ->get()
                    ->sortBy(fn ($p) => array_search($p->id, $recentIds));
            }
        }

        return view('home', compact(
            'mainSliders', 'subSliders1', 'subSliders2',
            'categories', 'offerProducts', 'popularProducts',
            'newProducts', 'randomProducts', 'promotions', 'recentlyViewed'
        ));
    }

    /** Display all active offer products, filtering for under-16 users. */
    public function offers()
    {
        $query = Product::with(['category', 'reviews'])->where('is_active', true);

        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $offerProducts = $query->withActiveOffers()->paginate(12);

        return view('offers', compact('offerProducts'));
    }

    /** List all categories with a count of their active products. */
    public function categories()
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('categories', compact('categories'));
    }
}
