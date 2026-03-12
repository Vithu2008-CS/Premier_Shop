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

        return view('admin.dashboard', compact('stats'));
    }
}
