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
            'totalProducts'  => Product::count(),
            'totalOrders'    => Order::count(),
            // Only sum orders that were actually paid
            'totalRevenue'   => Order::where('payment_status', 'completed')->sum('total'),
            // Count users whose role is 'customer' (excludes admin/driver/staff)
            'totalCustomers' => User::whereHas('role', fn ($q) => $q->where('name', 'customer'))->count(),
            // Products with fewer than 10 units — shown as a warning
            'lowStock'       => Product::where('stock', '<', 10)->count(),
            // 5 most recent orders with their customer eager-loaded
            'recentOrders'   => Order::with('user')->latest()->limit(5)->get(),
        ];

        // Build chart data for the last 7 days (oldest → newest for left-to-right display)
        $salesData   = [];
        $salesLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = now()->subDays($i);
            $salesLabels[] = $date->format('M d');
            $salesData[]   = (float) Order::whereDate('created_at', $date->toDateString())
                ->where('payment_status', 'completed')
                ->sum('total');
        }

        return view('admin.dashboard', compact('stats', 'salesData', 'salesLabels'));
    }
}
