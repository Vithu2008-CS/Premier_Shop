<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Handles the public product catalogue: listing with filters/search/sort,
 * single product detail, and a live search-suggestion JSON endpoint.
 * Age-restricted products are hidden from users under 16.
 */
class ProductController extends Controller
{
    /**
     * Display the product catalogue with optional category filter, keyword search, and sorting.
     * Under-16 users receive a filtered query that excludes age-restricted items.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'reviews'])->where('is_active', true);

        // Filter by category slug when provided
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Keyword search on product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Dynamic sort column
        match ($request->get('sort', 'newest')) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        // Hide age-restricted products from verified under-16 users
        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $products   = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show a single product detail page.
     * Loads 5 approved reviews and 4 related products from the same category.
     * Tracks the visit in recently-viewed: DB for logged-in users, session for guests.
     */
    public function show($slug)
    {
        $product = Product::with([
            'category',
            // Only load approved reviews, newest first, capped at 5 for performance
            'reviews' => fn ($q) => $q->approved()->latest()->take(5),
            'reviews.user',
        ])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Hard-block underage users from viewing age-restricted products
        if ($product->is_age_restricted && auth()->check() && auth()->user()->isUnder16()) {
            abort(403, 'You must be 16 or older to view this product.');
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $recentlyViewed = collect();

        if (auth()->check()) {
            // Persist to DB for logged-in users so it survives across sessions
            \App\Models\RecentlyViewed::track(auth()->id(), $product->id);
            $recentlyViewed = \App\Models\RecentlyViewed::getForUser(auth()->id(), 5);
        } else {
            // Guest tracking via session array (max 12, most-recent at index 0)
            $recentIds = session('recently_viewed', []);
            $recentIds = array_diff($recentIds, [$product->id]); // dedupe
            array_unshift($recentIds, $product->id);             // prepend current
            $recentIds = array_slice($recentIds, 0, 12);         // cap at 12
            session(['recently_viewed' => $recentIds]);

            if (! empty($recentIds)) {
                $recentlyViewed = Product::with(['category', 'reviews'])
                    ->where('is_active', true)
                    ->whereIn('id', $recentIds)
                    ->get()
                    ->sortBy(fn ($p) => array_search($p->id, $recentIds))
                    ->take(5);
            }
        }

        return view('products.show', compact('product', 'relatedProducts', 'recentlyViewed'));
    }

    /**
     * JSON endpoint for the navbar live-search dropdown.
     * Returns up to 6 matching products with name, slug, price, image, and URL.
     * Requires at least 2 characters to avoid spamming queries on single keystrokes.
     * Results are cached 5 minutes per query+age-group combination.
     */
    public function suggest(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $isUnder16 = auth()->check() && auth()->user()->isUnder16();
        // Separate cache keys prevent age-restricted suggestions leaking to under-16 users
        $cacheKey  = 'search_suggest_'.($isUnder16 ? 'restricted_' : 'all_').$q;

        return cache()->remember($cacheKey, 300, function () use ($q, $isUnder16) {
            $query = Product::with('category:id,name')
                ->where('is_active', true)
                ->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$q}%"))
                );

            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->limit(6)
                ->get(['id', 'name', 'slug', 'price', 'images', 'category_id'])
                ->map(fn ($p) => [
                    'name'     => $p->name,
                    'slug'     => $p->slug,
                    'price'    => '£'.number_format($p->price, 2),
                    'image'    => $p->first_image,
                    'category' => $p->category?->name,
                    'url'      => route('products.show', $p->slug),
                ]);
        });
    }
}
