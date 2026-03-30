<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index()
    {
        $returns = ReturnRequest::with(['user', 'order'])->latest()->paginate(20);
        return view('admin.returns.index', compact('returns'));
    }

    public function show(ReturnRequest $return)
    {
        $return->load(['user', 'order', 'items.orderItem.product']);
        return view('admin.returns.show', compact('return'));
    }

    public function update(Request $request, ReturnRequest $return)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,refunded',
            'admin_note' => 'nullable|string',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $return->status;
        $newStatus = $request->status;
        
        $return->update([
            'status' => $newStatus,
            'admin_note' => $request->admin_note,
            'refund_amount' => $request->refund_amount ?? $return->refund_amount,
        ]);

        // If approved from a non-approved state, restore stock
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $return->restoreStock();
        }

        // Notify customer
        if (method_exists(AppNotification::class, 'notifyReturnStatus')) {
            AppNotification::notifyReturnStatus($return);
        }

        return redirect()->route('admin.returns.show', $return)->with('success', 'Return request updated successfully.');
    }
}
