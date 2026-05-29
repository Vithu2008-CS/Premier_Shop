<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Handles admin order management: listing, detail view, status updates,
 * driver assignment, and PDF receipt generation.
 */
class OrderController extends Controller
{
    /**
     * Download a PDF receipt for a given order.
     * Eager-loads items→product and user so the PDF view has all needed data.
     */
    public function print(Order $order)
    {
        $order->load('items.product', 'user');
        $pdf = Pdf::loadView('admin.orders.print', compact('order'));

        return $pdf->download("order-{$order->order_number}.pdf");
    }

    /** List all orders (newest first), paginated with their customer. */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show a single order's detail page.
     * Also fetches drivers who are currently on duty so admin can assign one.
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'user', 'driver');

        // Only on-duty drivers are eligible for assignment
        $drivers = User::whereHas('role', fn ($q) => $q->where('name', 'driver'))
            ->where('is_on_duty', true)
            ->get();

        return view('admin.orders.show', compact('order', 'drivers'));
    }

    /** Assign a delivery driver to an order. */
    public function assignDriver(Request $request, Order $order)
    {
        $request->validate(['driver_id' => 'required|exists:users,id']);

        $order->update(['driver_id' => $request->driver_id]);

        return back()->with('success', 'Driver assigned to order.');
    }

    /**
     * Update order status and optional tracking dates.
     * Delegates to Order::updateStatusAndTracking() which auto-fills missing dates
     * and handles stock restoration on cancellation.
     * On status change, sends the customer an email notification and logs a sent-mail record.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'           => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status'   => 'sometimes|required|in:pending,completed',
            'processing_date'  => 'nullable|date|after:2020-01-01|before:2050-01-01',
            'shipped_date'     => 'nullable|date|after:2020-01-01|before:2050-01-01',
            'delivered_date'   => 'nullable|date|after:2020-01-01|before:2050-01-01',
        ]);

        if ($request->has('payment_status')) {
            $order->update(['payment_status' => $request->payment_status]);
        }

        $statusChanged = $order->updateStatusAndTracking(
            $request->status,
            $request->processing_date,
            $request->shipped_date,
            $request->delivered_date
        );

        // Only fire notifications when the status actually changed
        if ($statusChanged) {
            // Ensure the user relationship is loaded for email rendering
            $order->load('user');

            \Log::info("Order #{$order->order_number} status changed to '{$order->status}' — sending notifications.");

            // Save in-app notification + sent-folder record (non-critical — don't let failures block email)
            try {
                \App\Models\AppNotification::notifyOrderStatus($order);

                $htmlContent = view('emails.orders.status_updated', compact('order'))->render();
                \App\Models\ContactMessage::create([
                    'name'    => 'System ('.(auth()->user()->name ?? 'Admin').')',
                    'email'   => $order->user->email,
                    'subject' => 'Your order #'.$order->order_number.' status has been updated to '.$order->status,
                    'message' => $htmlContent,
                    'is_read' => true,
                    'folder'  => 'sent',
                ]);
            } catch (\Exception $e) {
                \Log::error("Failed to save notification/sent-record for order #{$order->order_number}: ".$e->getMessage());
            }

            // Send customer email — failure flashes a warning but doesn't block the status update
            if ($request->boolean('send_email', true)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($order->user->email)
                        ->send(new \App\Mail\OrderStatusUpdated($order));

                    \Log::info("Status email sent to {$order->user->email} for order #{$order->order_number}.");
                } catch (\Exception $e) {
                    \Log::error("Failed to send order status email for order #{$order->order_number}: ".$e->getMessage());
                    return back()
                        ->with('warning', 'Order updated, but the customer notification email could not be sent: '.$e->getMessage());
                }
            }
        } else {
            \Log::info("Order #{$order->order_number} status unchanged ('{$order->status}') — no notification sent.");
        }

        return back()->with('success', 'Order status and tracking updated.');
    }

    /** Delete an order. */
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order deleted successfully.');
    }
}
