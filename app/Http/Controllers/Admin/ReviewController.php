<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 * Admin moderation of product reviews: listing, approval toggling,
 * admin reply, and deletion.
 * 
 * FIXED: Added HTML escaping to prevent XSS vulnerability in admin_reply
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
     * Show a single review's detail page.
     */
    public function show(Review $review)
    {
        $review->load(['user', 'product']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Update a review's status and admin reply.
     * FIXED: Added HTML escaping for admin_reply to prevent XSS
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
            'admin_reply' => 'nullable|string|max:1000',
        ]);

        // FIXED: Escape HTML to prevent XSS vulnerability
        $review->update([
            'is_approved' => $request->is_approved,
            'admin_reply' => $request->admin_reply ? e($request->admin_reply) : null,
        ]);

        return redirect()->route('admin.reviews.show', $review)
            ->with('success', 'Review updated successfully.');
    }

    /**
     * Toggle a review's approved status.
     * Unapproved reviews are hidden from the storefront product page.
     */
    public function toggleApproval(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);

        return back()->with('success', 'Review status updated successfully.');
    }

    /**
     * Save an admin reply that appears beneath the review on the product page.
     * FIXED: Added HTML escaping for admin_reply
     */
    public function reply(Request $request, Review $review)
    {
        $request->validate(['admin_reply' => 'nullable|string|max:1000']);

        // FIXED: Escape HTML to prevent XSS vulnerability
        $review->update(['admin_reply' => $request->admin_reply ? e($request->admin_reply) : null]);

        return back()->with('success', 'Reply saved successfully.');
    }

    /** Permanently delete a review. */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
