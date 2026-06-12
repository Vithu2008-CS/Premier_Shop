<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Generates sales/stock reports for the admin panel.
 * Supports category filtering, dynamic column sorting, paginated web view,
 * and a PDF download of the same data set.
 */
class ReportController extends Controller
{
    /**
     * Download the current filtered/sorted report as a PDF.
     * Uses the same query logic as index() but fetches all rows (no pagination).
     */
    public function print(Request $request)
    {
        $categories = Category::all();

        // withTrashed: soft-deleted products keep their sales history in the report
        $query = Product::withTrashed()
            ->with('category')
            ->withSum('orderItems as total_sold', 'quantity')
            ->withCount(['wishlistedBy as total_wishlist']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $this->applySorting($query, $request);

        $products = $query->get();

        $pdf = Pdf::loadView('admin.reports.print', compact('products', 'categories'));

        return $pdf->download('sales-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Display the interactive report page with optional category filter and sorting.
     * Paginated to 50 rows per page for performance on large catalogues.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        // withTrashed: soft-deleted products keep their sales history in the report
        $query = Product::withTrashed()
            ->with('category')
            ->withSum('orderItems as total_sold', 'quantity')
            ->withCount(['wishlistedBy as total_wishlist']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $this->applySorting($query, $request);

        $products = $query->paginate(50)->withQueryString();

        return view('admin.reports.index', compact('products', 'categories'));
    }

    /**
     * Apply sort column and direction from the request to the query.
     * Falls back to 'sold desc' when parameters are missing or unrecognised.
     * A secondary sort by id desc ensures a stable, deterministic order.
     */
    private function applySorting($query, Request $request): void
    {
        $sortBy = $request->get('sort_by', 'sold');

        // Whitelist the direction — orderBy() throws a 500 on anything else
        $order = strtolower($request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sortBy) {
            'price'    => $query->orderBy('price', $order),
            'stock'    => $query->orderBy('stock', $order),
            'wishlist' => $query->orderBy('total_wishlist', $order),
            default    => $query->orderBy('total_sold', $order),
        };

        // Tie-break by id so pagination pages don't shuffle between requests
        $query->orderBy('id', 'desc');
    }
}
