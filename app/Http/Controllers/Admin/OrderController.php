<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function print(Order $order)
    {
        $order->load('items.product', 'user');
        $pdf = Pdf::loadView('admin.orders.print', compact('order'));
        return $pdf->download("order-{$order->order_number}.pdf");
    }

    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user', 'driver');
        $drivers = User::whereHas('role', function($q) {
            $q->where('name', 'driver');
        })->where('is_on_duty', true)->get();
        
        return view('admin.orders.show', compact('order', 'drivers'));
    }

    public function assignDriver(Request $request, Order $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $order->update(['driver_id' => $request->driver_id]);

        return back()->with('success', 'Driver assigned to order.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'processing_date' => 'nullable|date|after:2020-01-01|before:2050-01-01',
            'shipped_date' => 'nullable|date|after:2020-01-01|before:2050-01-01',
            'delivered_date' => 'nullable|date|after:2020-01-01|before:2050-01-01',
        ]);

        $statusChanged = $order->updateStatusAndTracking(
            $request->status,
            $request->processing_date,
            $request->shipped_date,
            $request->delivered_date
        );

        if ($statusChanged) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdated($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send order status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Order status and tracking updated.');
    }
}
