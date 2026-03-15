<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function print(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category')
            ->withSum('orderItems as total_sold', 'quantity')
            ->withCount(['wishlistedBy as total_wishlist']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $sortBy = $request->get('sort_by', 'sold');
        $order = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $order);
                break;
            case 'stock':
                $query->orderBy('stock', $order);
                break;
            case 'wishlist':
                $query->orderBy('total_wishlist', $order);
                break;
            case 'sold':
            default:
                $query->orderBy('total_sold', $order);
                break;
        }

        $query->orderBy('id', 'desc');
        $products = $query->get();

        $pdf = Pdf::loadView('admin.reports.print', compact('products', 'categories'));
        return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category')
            ->withSum(['orderItems as total_sold' => function ($query) {
                // You can add conditions here if you only want to count paid/completed orders
            }], 'quantity')
            ->withCount(['wishlistedBy as total_wishlist']);

        // Apply Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply Dynamic Sorting
        $sortBy = $request->get('sort_by', 'sold');
        $order = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $order);
                break;
            case 'stock':
                $query->orderBy('stock', $order);
                break;
            case 'wishlist':
                $query->orderBy('total_wishlist', $order);
                break;
            case 'sold':
            default:
                $query->orderBy('total_sold', $order);
                break;
        }

        // Also order by id as a secondary sort
        $query->orderBy('id', 'desc');

        if ($request->has('print')) {
            $products = $query->get();
        } else {
            $products = $query->paginate(50)->withQueryString();
        }

        return view('admin.reports.index', compact('products', 'categories'));
    }
}
