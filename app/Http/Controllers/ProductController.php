<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'reviews'])->where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        switch ($request->get('sort', 'newest')) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'name': $query->orderBy('name', 'asc'); break;
            default: $query->orderBy('created_at', 'desc');
        }

        // Age restriction: hide 16+ products from underage users
        if (auth()->check() && auth()->user()->isUnder16()) {
            $query->where('is_age_restricted', false);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with(['reviews.user'])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Block age-restricted products for underage users
        if ($product->is_age_restricted && auth()->check() && auth()->user()->isUnder16()) {
            abort(403, 'You must be 16 or older to view this product.');
        }

        $relatedProducts = Product::with(['category', 'reviews'])->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Track recently viewed products in session (max 12, most recent first)
        $recentIds = session('recently_viewed', []);
        $recentIds = array_diff($recentIds, [$product->id]); // remove duplicate
        array_unshift($recentIds, $product->id); // add to front
        $recentIds = array_slice($recentIds, 0, 12); // keep max 12
        session(['recently_viewed' => $recentIds]);

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Search auto-suggest — returns JSON suggestions as user types
     */
    public function suggest(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Cache suggestions for 5 minutes for identical queries
        return cache()->remember("search_suggest_{$q}", 300, function () use ($q) {
            return Product::with('category:id,name')
                ->where('is_active', true)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', '%' . $q . '%')
                          ->orWhereHas('category', function ($cq) use ($q) {
                              $cq->where('name', 'like', '%' . $q . '%');
                          });
                })
                ->limit(6)
                ->get(['id', 'name', 'slug', 'price', 'images', 'category_id'])
                ->map(function ($p) {
                    return [
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'price' => '£' . number_format($p->price, 2),
                        'image' => $p->first_image,
                        'category' => $p->category?->name,
                        'url' => route('products.show', $p->slug),
                    ];
                });
        });
    }
}
