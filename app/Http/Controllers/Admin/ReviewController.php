<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApproval(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);
        return back()->with('success', 'Review status updated successfully.');
    }

    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'nullable|string'
        ]);

        $review->update(['admin_reply' => $request->admin_reply]);
        return back()->with('success', 'Reply saved successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted successfully.');
    }
}
