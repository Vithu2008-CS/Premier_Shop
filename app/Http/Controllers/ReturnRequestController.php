<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use Illuminate\Http\Request;

/**
 * Customer-facing return request flow: create form, submission, and status view.
 * Returns are only allowed for delivered orders that don't already have a return request.
 * Staff are notified via AppNotification on submission.
 */
class ReturnRequestController extends Controller
{
    /**
     * Show the return request creation form for a delivered order.
     * Guards: order must belong to the user AND be in 'delivered' status.
     * Redirects if a return request already exists for this order.
     */
    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'delivered') {
            abort(403, 'Order not eligible for return.');
        }

        if ($order->returnRequest) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'A return request already exists for this order.');
        }

        $order->load('items.product');

        return view('returns.create', compact('order'));
    }

    /**
     * Validate and store a new return request with its line items.
     * Validates all item quantities before any DB writes to avoid partial inserts.
     * An optional photo upload is stored in /storage/returns.
     * Notifies admin/staff of the new request via AppNotification.
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'delivered') {
            abort(403);
        }

        // Idempotency guard — redirect silently if a request was already submitted
        if ($order->returnRequest) {
            return redirect()->route('orders.show', $order);
        }

        $request->validate([
            'reason'        => 'required|string',
            'customer_note' => 'nullable|string',
            'items'         => 'required|array',
            'items.*'       => 'integer|min:0',
            'photo'         => 'nullable|image|max:5120',
        ]);

        // Filter out items with 0 quantity (unchecked in the form)
        $requestedItems = array_filter($request->items, fn ($qty) => $qty > 0);

        if (empty($requestedItems)) {
            return back()->with('error', 'You must select at least one item to return.');
        }

        // Pre-validate all quantities before writing to DB — avoids partial return records
        foreach ($requestedItems as $orderItemId => $qty) {
            $orderItem = $order->items()->find($orderItemId);
            if (! $orderItem || $qty > $orderItem->quantity) {
                return back()->with('error', 'Invalid return quantity or item selected.');
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = \App\Helpers\ImageHelper::storeAsWebp($request->file('photo'), 'returns');
        }

        $returnRequest = ReturnRequest::create([
            'order_id'      => $order->id,
            'user_id'       => auth()->id(),
            'status'        => 'pending',
            'reason'        => $request->reason,
            'customer_note' => $request->customer_note,
            'photo_path'    => $photoPath,
        ]);

        // Create one ReturnRequestItem row per selected order item
        foreach ($requestedItems as $orderItemId => $qty) {
            $orderItem = $order->items()->find($orderItemId);
            if ($orderItem && $qty <= $orderItem->quantity) {
                ReturnRequestItem::create([
                    'return_request_id' => $returnRequest->id,
                    'order_item_id'     => $orderItemId,
                    'quantity'          => $qty,
                ]);
            }
        }

        // Alert all admin/staff users about the new return via in-app notification
        AppNotification::notifyNewReturnRequest($returnRequest);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Return request submitted successfully. We will review it shortly.');
    }

    /** Show the customer their return request status and items. */
    public function show(ReturnRequest $return)
    {
        if ($return->user_id !== auth()->id()) {
            abort(403);
        }

        $return->load(['order', 'items.orderItem.product']);

        return view('returns.show', compact('return'));
    }
}
