<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     *
     * Status changes are restricted to ReturnRequest::ALLOWED_TRANSITIONS
     * ('refunded' is terminal). Stock is kept consistent with the invariant
     * "returned units are back in stock iff status is approved/refunded":
     * entering 'approved' restores stock, leaving it for pending/rejected
     * deducts it again, and approved → refunded leaves it untouched.
     * The customer is notified only when the status actually changes.
     */
    public function update(Request $request, ReturnRequest $return)
    {
        $request->validate([
            'status'        => 'required|in:pending,approved,rejected,refunded',
            'admin_note'    => 'nullable|string',
            'refund_amount' => 'required_if:status,refunded|nullable|numeric|min:0',
        ]);

        $oldStatus = $return->status;
        $newStatus = $request->status;

        if (! $return->canTransitionTo($newStatus)) {
            return back()
                ->withErrors(['status' => "Cannot change status from '{$oldStatus}' to '{$newStatus}'."])
                ->withInput();
        }

        // Status and stock must change together — wrap in a transaction
        DB::transaction(function () use ($request, $return, $oldStatus, $newStatus) {
            $return->update([
                'status'        => $newStatus,
                'admin_note'    => $request->admin_note,
                // Keep existing refund_amount if none submitted
                'refund_amount' => $request->refund_amount ?? $return->refund_amount,
            ]);

            if ($newStatus === 'approved' && $oldStatus !== 'approved') {
                $return->restoreStock();
            } elseif ($oldStatus === 'approved' && in_array($newStatus, ['pending', 'rejected'], true)) {
                $return->deductStock();
            }
        });

        // Notify the customer only on a real status change, not on note edits
        if ($newStatus !== $oldStatus) {
            AppNotification::notifyReturnStatus($return);
        }

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
