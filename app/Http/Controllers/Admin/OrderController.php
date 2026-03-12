<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'processing_date' => 'nullable|date',
            'shipped_date' => 'nullable|date',
            'delivered_date' => 'nullable|date',
        ]);
        
        $oldStatus = $order->status;
        $updates = ['status' => $request->status];

        // Auto-set dates if status changes to a state it wasn't in before
        if ($request->status === 'processing' && !$order->processing_date) {
            $updates['processing_date'] = now();
        }
        if ($request->status === 'shipped' && !$order->shipped_date) {
            $updates['shipped_date'] = now();
        }
        if ($request->status === 'delivered' && !$order->delivered_date) {
            $updates['delivered_date'] = now();
        }

        // Allow manual overrides
        if ($request->has('processing_date')) $updates['processing_date'] = $request->processing_date;
        if ($request->has('shipped_date')) $updates['shipped_date'] = $request->shipped_date;
        if ($request->has('delivered_date')) $updates['delivered_date'] = $request->delivered_date;

        $order->update($updates);

        if ($oldStatus !== $request->status) {
            \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdated($order));
        }

        return back()->with('success', 'Order status updated.');
    }
}
