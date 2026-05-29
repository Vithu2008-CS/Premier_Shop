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
            try {
                // send_email defaults true; admin can suppress it via the UI toggle
                if ($request->send_email ?? true) {
                    \Illuminate\Support\Facades\Mail::to($order->user->email)
                        ->send(new \App\Mail\OrderStatusUpdated($order));
                }

                // Push an in-app notification to the customer
                \App\Models\AppNotification::notifyOrderStatus($order);

                // Archive a copy of the status email in the admin mail sent folder
                $htmlContent = view('emails.orders.status_updated', compact('order'))->render();
                \App\Models\ContactMessage::create([
                    'name'    => 'System ('.( auth()->user()->name ?? 'Admin').')',
                    'email'   => $order->user->email,
                    'subject' => 'Your order #'.$order->order_number.' status has been updated to '.$order->status,
                    'message' => $htmlContent,
                    'is_read' => true,
                    'folder'  => 'sent',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send order status email: '.$e->getMessage());
            }
        }

        return back()->with('success', 'Order status and tracking updated.');
    }
}
