<?php

/**
 * DriverController — Driver-facing delivery management.
 * dashboard(): active + completed orders for auth driver.
 * toggleDuty(): flips is_on_duty flag.
 * completeDelivery(): stores delivery proof image, marks order delivered,
 *   fires AppNotification + sends status email + logs to sent folder.
 * All actions enforce driver_id === auth()->id() ownership check.
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'is_on_duty' => ! $driver->is_on_duty,
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

        $order->updateStatusAndTracking(
            'delivered',
            null,
            null,
            $deliveredDate
        );

        $order->update(['delivery_proof' => $path]);

        // Trigger in-app notification
        \App\Models\AppNotification::notifyOrderStatus($order);

        // Notify user
        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusUpdated($order));

            $htmlContent = view('emails.orders.status_updated', compact('order'))->render();

            \App\Models\ContactMessage::create([
                'name' => 'System (Driver)',
                'email' => $order->user->email,
                'subject' => 'Your order #'.$order->order_number.' status has been updated to '.$order->status,
                'message' => $htmlContent,
                'is_read' => true,
                'folder' => 'sent',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order status email: '.$e->getMessage());
        }

        return redirect()->route('driver.dashboard')->with('success', 'Order marked as delivered!');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $driver = auth()->user();
        $driver->update([
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'location_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
