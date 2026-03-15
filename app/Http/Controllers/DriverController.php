<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverController extends Controller
{
    //
    public function dashboard()
    {
        $driver = auth()->user();
        
        $pendingOrders = $driver->assignedOrders()
            ->with('user')
            ->whereIn('status', ['pending', 'processing', 'shipped'])
            ->latest()
            ->get();

        $deliveredOrders = $driver->assignedOrders()
            ->with('user')
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('driver.dashboard', compact('driver', 'pendingOrders', 'deliveredOrders'));
    }

    public function toggleDuty(Request $request)
    {
        $driver = auth()->user();
        $driver->update([
            'is_on_duty' => !$driver->is_on_duty
        ]);

        return back()->with('success', 'Your duty status has been updated.');
    }

    public function showOrder(Order $order)
    {
        if ($order->driver_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load('items.product', 'user');
        return view('driver.order_details', compact('order'));
    }

    public function completeDelivery(Request $request, Order $order)
    {
        if ($order->driver_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'delivery_proof' => 'required|image|max:2048',
            'delivered_date' => 'nullable|date',
        ]);

        $path = $request->file('delivery_proof')->store('delivery_proofs', 'public');

        $deliveredDate = $request->filled('delivered_date') 
            ? Carbon::parse($request->delivered_date) 
            : now();

        $order->update([
            'status' => 'delivered',
            'delivered_date' => $deliveredDate,
            'delivery_proof' => $path,
        ]);

        // Notify user
        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdated($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order status email: ' . $e->getMessage());
        }

        return redirect()->route('driver.dashboard')->with('success', 'Order marked as delivered!');
    }
}
