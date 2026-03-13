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
            ->withSum('orderItems as total_sold', 'quantity');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('total_sold', $sort);
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
                // $query->whereHas('order', function($q) {
                //      $q->whereIn('status', ['paid', 'completed', 'shipped']);
                // });
            }], 'quantity');

        // Apply Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply Sorting
        $sort = $request->get('sort', 'desc'); // default to highest sold
        if ($sort === 'asc') {
            $query->orderBy('total_sold', 'asc');
        } else {
            $query->orderBy('total_sold', 'desc');
        }

        // Also order by id as a secondary sort
        $query->orderBy('id', 'desc');

        // Note: For printing, we might want to show all records or paginate with a large number
        // Instead of pagination, if the request is for print, we can return all or a very large pagination limit.
        if ($request->has('print')) {
            $products = $query->get();
        } else {
            $products = $query->paginate(50)->withQueryString();
        }

        return view('admin.reports.index', compact('products', 'categories'));
    }
}
