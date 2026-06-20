<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

/**
 * Displays the admin dashboard with key business metrics and a 7-day sales chart.
 */
class DashboardController extends Controller
{
    /**
     * Load headline stats and last-7-days sales data for the dashboard view.
     */
    public function index()
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            // Only sum orders that were actually paid
            'totalRevenue' => Order::where('payment_status', 'completed')->sum('total'),
            // Count users whose role is 'customer' (excludes admin/driver/staff)
            'totalCustomers' => User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->count(),
            // Products with fewer than 10 units — shown as a warning
            'lowStock' => Product::where('stock', '<', 10)->count(),
            // 5 most recent orders with their customer eager-loaded
            'recentOrders' => Order::with('user')->latest()->limit(5)->get(),
        ];

        // Build chart data for the last 7 days (oldest → newest for left-to-right display)
        $salesData = [];
        $salesLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $salesLabels[] = $date->format('M d');
            $salesData[] = (float) Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'completed')
                ->sum('total');
        }

        return view('admin.dashboard', compact('stats', 'salesData', 'salesLabels'));
    }

    /**
     * Live Omni-Search API for navbar search bar
     */
    public function omniSearch(\Illuminate\Http\Request $request)
    {
        $q = trim($request->get('q'));

        if (strlen($q) < 2) {
            return response()->json([
                'products' => [],
                'orders' => [],
                'customers' => [],
            ]);
        }

        // 1. Search Products
        $products = Product::where('name', 'LIKE', "%{$q}%")
            ->orWhere('barcode', 'LIKE', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => number_format($product->price, 2),
                    'stock' => $product->stock,
                    'image' => $product->image_url ?? asset('admin_assets/images/placeholder.png'),
                    'url' => route('admin.products.edit', $product),
                ];
            });

        // 2. Search Orders
        $orders = Order::with('user')
            ->where('order_number', 'LIKE', "%{$q}%")
            ->orWhereHas('user', function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->user->name ?? 'N/A',
                    'total' => number_format($order->total, 2),
                    'status' => ucfirst($order->status),
                    'url' => route('admin.orders.show', $order),
                ];
            });

        // 3. Search Customers
        $customers = User::whereHas('role', fn ($qRole) => $qRole->where('name', 'customer'))
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->profile_photo_url,
                    'url' => route('admin.customers.show', $user),
                ];
            });

        return response()->json([
            'products' => $products,
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }
}
