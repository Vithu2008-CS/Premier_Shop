<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Customer-facing order management: listing, detail view, cancellation,
 * and PDF receipt download.
 * Every action enforces ownership — users cannot see or modify other people's orders.
 */
class OrderController extends Controller
{
    /**
     * Download a PDF receipt for the authenticated user's order.
     * Aborts 403 if the order belongs to a different user.
     */
    public function print(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product', 'user');
        $pdf = Pdf::loadView('admin.orders.print', compact('order'));

        return $pdf->download("order-{$order->order_number}.pdf");
    }

    /** List the authenticated user's orders, newest first, paginated. */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show an order's detail page.
     * Accessible to the owning customer or any admin user.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('items.product', 'driver');

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel a pending order.
     * Only 'pending' status orders can be cancelled — once processing has started
     * the customer must contact support.
     * Stock is restored and loyalty points are clawed back via Order::restoreStock().
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $request->validate(['cancellation_reason' => 'required|string|max:1000']);

        $order->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Restore product stock and reverse any loyalty point transactions
        $order->restoreStock();

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Publicly track an order by its tracking number securely.
     */
    public function trackPublic(string $orderNumber)
    {
        $order = Order::where('order_number', strtoupper(trim($orderNumber)))
            ->with(['driver'])
            ->first(['id', 'order_number', 'status', 'total', 'created_at', 'shipped_date', 'delivered_date', 'driver_id', 'user_id']);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'order_number'   => $order->order_number,
                'status'         => $order->status,
                'total'          => (float) $order->total,
                'created_at'     => $order->created_at->format('d M Y'),
                'shipped_date'   => $order->shipped_date ? $order->shipped_date->format('d M Y') : null,
                'delivered_date' => $order->delivered_date ? $order->delivered_date->format('d M Y') : null,
                'driver_name'    => $order->driver->name ?? null,
                'url'            => auth()->check() && (auth()->id() === $order->user_id || auth()->user()->isAdmin()) ? route('orders.show', $order) : null,
            ]
        ]);
    }
}

