<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'completed')->sum('total'),
            'totalCustomers' => User::whereHas('role', fn($q) => $q->where('name', 'customer'))->count(),
            'lowStock' => Product::where('stock', '<', 10)->count(),
            'recentOrders' => Order::with('user')->latest()->limit(5)->get(),
        ];

        // Get sales for last 7 days
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
}
