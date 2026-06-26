<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Handles the public product catalogue: listing with filters/search/sort,
 * single product detail, and a live search-suggestion JSON endpoint.
 * Age-restricted products are hidden from users under 16.
 * 
 * FIXED: Added eager loading for reviews to prevent N+1 queries
 */
class ProductController extends Controller
{
    /**
     * Display the product catalogue with optional category filter, keyword search, and sorting.
     * Under-16 users receive a filtered query that excludes age-restricted items.
     */
    public function index(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:150',
            'min_price' => 'nullable|numeric|min:0|max:1000000',
            'max_price' => 'nullable|numeric|min:0|max:1000000',
            'rating' => 'nullable|numeric|min:0|max:5',
            'sort' => 'nullable|string|in:newest,price_low,price_high,name,rating',
            'page' => 'nullable|integer|min:1',
        ]);

        // FIXED: Added eager loading to prevent N+1 query on categories and reviews
        $query = Product::with(['category', 'reviews'])
            ->withAvg(['reviews' => fn ($q) => $q->approved()], 'rating')
            ->where('is_active', true);

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->boolean('on_offer')) {
            $query->where(fn ($q) => $q->where('retail_offer', true)->orWhere('offer_active', true));
        }

        if ($request->filled('rating')) {
            $minRating = (float) $request->rating;
            $query->whereRaw("(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviews.product_id = products.id AND reviews.is_approved = 1) >= {$minRating}");
        }

        match ($request->get('sort', 'newest')) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            'rating' => $query->orderByDesc('reviews_avg_rating'),
            default => $query->orderBy('created_at', 'desc'),
        };

        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        $priceBounds = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('products.index', compact('products', 'categories', 'priceBounds'));
    }

    /**
     * Show a single product detail page.
     * FIXED: Added eager loading for all relationships to prevent N+1 queries
     */
    public function show($slug)
    {
        // FIXED: Added eager loading for category and reviews
        $product = Product::with(['category', 'reviews'])
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($product->is_age_restricted && auth()->check() && auth()->user()->isUnder16()) {
            abort(403, 'You must be 16 or older to view this product.');
        }

        // FIXED: Added eager loading for related products
        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $recentlyViewed = collect();

        if (auth()->check()) {
            \App\Models\RecentlyViewed::track(auth()->id(), $product->id);
            $recentlyViewed = \App\Models\RecentlyViewed::getForUser(auth()->id(), 5);
        } else {
            $recentIds = session('recently_viewed', []);
            $recentIds = array_diff($recentIds, [$product->id]);
            array_unshift($recentIds, $product->id);
            $recentIds = array_slice($recentIds, 0, 12);
            session(['recently_viewed' => $recentIds]);

            if (!empty($recentIds)) {
                // FIXED: Added eager loading for reviews
                $recentlyViewed = Product::with(['category', 'reviews'])
                    ->where('is_active', true)
                    ->whereIn('id', $recentIds)
                    ->get()
                    ->sortBy(fn ($p) => array_search($p->id, $recentIds))
                    ->take(5);
            }
        }

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

        // FIXED: Added eager loading for user
        $approvedReviews = $product->reviews()->with('user')->approved()->latest()->paginate(5);

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

        $coPurchasedIds = \Illuminate\Support\Facades\DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->where('oi1.product_id', $product->id)
            ->whereColumn('oi2.product_id', '!=', 'oi1.product_id')
            ->groupBy('oi2.product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(8)
            ->pluck('oi2.product_id');

        $frequentlyBought = collect();
        if ($coPurchasedIds->isNotEmpty()) {
            // FIXED: Added eager loading for category
            $fbQuery = Product::with('category')
                ->whereIn('id', $coPurchasedIds)
                ->where('is_active', true);
            if (auth()->check() && auth()->user()->isUnder16()) {
                $fbQuery->where('is_age_restricted', false);
            }
            $frequentlyBought = $fbQuery->get()
                ->sortBy(fn ($p) => array_search($p->id, $coPurchasedIds->all()))
                ->take(4)
                ->values();
        }

        return view('products.show', compact(
            'product', 'relatedProducts', 'recentlyViewed',
            'reviewsCount', 'avgRating', 'ratingDistribution', 'approvedReviews',
            'inWishlist', 'hasReviewed', 'hasPurchased', 'frequentlyBought'
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
        $q = trim((string) $request->get('q', ''));

        if (strlen($q) < 2 || strlen($q) > 100) {
            return response()->json([]);
        }

        $isUnder16 = auth()->check() && auth()->user()->isUnder16();
        $cacheKey = 'search_suggest_'.($isUnder16 ? 'restricted_' : 'all_').$q;

        return cache()->remember($cacheKey, 300, function () use ($q, $isUnder16) {
            // FIXED: Added eager loading for category
            $query = Product::with('category:id,name')
                ->where('is_active', true)
                ->where(fn ($q2) => $q2->where('name', 'like', "%{$q}%")
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$q}%"))
                );

            if ($isUnder16) {
                $query->where('is_age_restricted', false);
            }

            return $query->limit(6)
                ->get(['id', 'name', 'slug', 'price', 'images', 'category_id'])
                ->map(fn ($p) => [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => '£'.number_format($p->price, 2),
                    'image' => $p->first_image,
                    'category' => $p->category?->name,
                    'url' => route('products.show', $p->slug),
                ]);
        });
    }
}
