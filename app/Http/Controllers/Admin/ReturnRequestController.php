<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

/**
 * Admin handling of customer return requests.
 * Status transitions trigger stock restoration and customer notifications.
 */
class ReturnRequestController extends Controller
{
    /** List all return requests with customer and order, newest first. */
    public function index()
    {
        $returns = ReturnRequest::with(['user', 'order'])->latest()->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    /**
     * Show a single return request with full detail:
     * user, original order, each returned item, and the associated order item/product.
     */
    public function show(ReturnRequest $return)
    {
        $return->load(['user', 'order', 'items.orderItem.product']);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Update a return request's status and optional admin note / refund amount.
     * When transitioning TO 'approved' (from any other state), product stock
     * is restored via ReturnRequest::restoreStock().
     * A push notification is sent to the customer regardless of the new status.
     */
    public function update(Request $request, ReturnRequest $return)
    {
        $request->validate([
            'status'        => 'required|in:pending,approved,rejected,refunded',
            'admin_note'    => 'nullable|string',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $return->status;
        $newStatus = $request->status;

        $return->update([
            'status'        => $newStatus,
            'admin_note'    => $request->admin_note,
            // Keep existing refund_amount if none submitted
            'refund_amount' => $request->refund_amount ?? $return->refund_amount,
        ]);

        // Only restore stock once — when first approved, not on subsequent saves
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $return->restoreStock();
        }

        // Push in-app notification to the customer about their return update
        AppNotification::notifyReturnStatus($return);

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Return request updated successfully.');
    }

    /**
     * Delete a return request and redirect back with success.
     */
    public function destroy(ReturnRequest $return)
    {
        $return->delete();

        return redirect()->route('admin.returns.index')
            ->with('success', 'Return request deleted successfully.');
    }
}
