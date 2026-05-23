<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 * Admin moderation of product reviews: listing, approval toggling,
 * admin reply, and deletion.
 */
class ReviewController extends Controller
{
    /** List all reviews with their user and product, newest first. */
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Toggle a review's approved status.
     * Unapproved reviews are hidden from the storefront product page.
     */
    public function toggleApproval(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', 'Review status updated successfully.');
    }

    /** Save an admin reply that appears beneath the review on the product page. */
    public function reply(Request $request, Review $review)
    {
        $request->validate(['admin_reply' => 'nullable|string']);

        $review->update(['admin_reply' => $request->admin_reply]);

        return back()->with('success', 'Reply saved successfully.');
    }

    /** Permanently delete a review. */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
