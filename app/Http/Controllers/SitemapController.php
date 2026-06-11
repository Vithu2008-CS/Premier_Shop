<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Generates an XML sitemap of the public storefront (home, catalogue pages,
 * categories and every active product) for search-engine crawlers.
 */
class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        $categories = Category::all(['slug']);

        return response()
            ->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'application/xml');
    }
}
