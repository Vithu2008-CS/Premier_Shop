<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'delivered') {
            abort(403, 'Order not eligible for return.');
        }

        if ($order->returnRequest) {
            return redirect()->route('orders.show', $order)->with('error', 'A return request already exists for this order.');
        }

        $order->load('items.product');
        return view('returns.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'delivered') {
            abort(403);
        }

        if ($order->returnRequest) {
            return redirect()->route('orders.show', $order);
        }

        $request->validate([
            'reason' => 'required|string',
            'customer_note' => 'nullable|string',
            'items' => 'required|array',
            'items.*' => 'integer|min:0',
            'photo' => 'nullable|image|max:5120',
        ]);

        $requestedItems = array_filter($request->items, fn($qty) => $qty > 0);
        
        if (empty($requestedItems)) {
            return back()->with('error', 'You must select at least one item to return.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('returns', 'public');
        }

        $returnRequest = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'reason' => $request->reason,
            'customer_note' => $request->customer_note,
            'photo_path' => $photoPath,
        ]);

        foreach ($requestedItems as $orderItemId => $qty) {
            $orderItem = $order->items()->find($orderItemId);
            if ($orderItem && $qty <= $orderItem->quantity) {
                ReturnRequestItem::create([
                    'return_request_id' => $returnRequest->id,
                    'order_item_id' => $orderItemId,
                    'quantity' => $qty,
                ]);
            }
        }

        // Trigger Notification
        if (method_exists(AppNotification::class, 'notifyNewReturnRequest')) {
            AppNotification::notifyNewReturnRequest($returnRequest);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Return request submitted successfully. We will review it shortly.');
    }

    public function show(ReturnRequest $return)
    {
        if ($return->user_id !== auth()->id()) {
            abort(403);
        }

        $return->load(['order', 'items.orderItem.product']);
        return view('returns.show', compact('return'));
    }
}
