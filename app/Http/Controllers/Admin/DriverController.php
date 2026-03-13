<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    //
    public function index()
    {
        $drivers = User::whereHas('role', function($q) {
            $q->where('name', 'driver');
        })->withCount(['assignedOrders as processing_orders_count' => function($q) {
            $q->whereIn('status', ['pending', 'processing', 'shipped']);
        }])->get();

        return view('admin.drivers.index', compact('drivers'));
    }
}
