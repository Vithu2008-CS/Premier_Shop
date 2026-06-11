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
        // Average of approved reviews, exposed as reviews_avg_rating for the card + rating sort
        $query = Product::with(['category', 'reviews'])
            ->withAvg(['reviews' => fn ($q) => $q->approved()], 'rating')
            ->where('is_active', true);

        // Filter by category slug when provided
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Keyword search across name and description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range (filters the base list price)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Availability — in-stock only
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // On offer — retail offer or active bulk-buy offer
        if ($request->boolean('on_offer')) {
            $query->where(fn ($q) => $q->where('retail_offer', true)->orWhere('offer_active', true));
        }

        // Minimum average rating across approved reviews. The threshold is cast to a
        // float and inlined (not bound) so its placeholder can't collide with the
        // withAvg() subquery's binding above — float cast keeps it injection-safe.
        if ($request->filled('rating')) {
            $minRating = (float) $request->rating;
            $query->whereRaw("(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviews.product_id = products.id AND reviews.is_approved = 1) >= {$minRating}");
        }

        // Dynamic sort column
        match ($request->get('sort', 'newest')) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            'rating'     => $query->orderByDesc('reviews_avg_rating'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        // Hide age-restricted products from verified under-16 users
        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        // Catalogue price bounds power the price-filter placeholders
        $priceBounds = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('products.index', compact('products', 'categories', 'priceBounds'));
    }

    /**
     * Show a single product detail page.
     * Loads 5 approved reviews and 4 related products from the same category.
     * Tracks the visit in recently-viewed: DB for logged-in users, session for guests.
     */
    public function show($slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

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

        // ── Review aggregates — computed once here instead of via per-call
        // accessors repeated throughout the view (the rating distribution gives
        // count and average without extra queries). ──
        $ratingDistribution = $product->reviews()->approved()
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $reviewsCount = (int) $ratingDistribution->sum();
        $weighted = 0;
        foreach ($ratingDistribution as $rating => $count) {
            $weighted += $rating * $count;
        }
        $avgRating = $reviewsCount > 0 ? round($weighted / $reviewsCount, 1) : 0;

        $approvedReviews = $product->reviews()->with('user')->approved()->latest()->paginate(5);

        // ── Per-user state, only when authenticated ──
        $inWishlist = $hasReviewed = $hasPurchased = false;
        if (auth()->check()) {
            $userId = auth()->id();
            $inWishlist = \App\Models\UserItem::where('user_id', $userId)
                ->where('product_id', $product->id)->where('type', 'wishlist')->exists();
            $hasReviewed = $product->reviews()->where('user_id', $userId)->exists();
            $hasPurchased = \App\Models\Order::where('user_id', $userId)
                ->whereIn('status', ['delivered', 'shipped', 'processing'])
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        return view('products.show', compact(
            'product', 'relatedProducts', 'recentlyViewed',
            'reviewsCount', 'avgRating', 'ratingDistribution', 'approvedReviews',
            'inWishlist', 'hasReviewed', 'hasPurchased'
        ));
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
