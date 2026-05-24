<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\RewardPointTransaction;
use Illuminate\Http\Request;

/**
 * Handles customer product review submission.
 * Reviews are only allowed from customers who have actually purchased the product
 * and haven't already reviewed it (one review per user per product).
 * Submitting a review awards 50 loyalty points as a gamification incentive.
 */
class ReviewController extends Controller
{
    /**
     * Validate and store a new product review.
     *
     * Guards:
     *  1. User must have purchased the product (order in delivered/shipped/processing state).
     *  2. User must not have already reviewed this product.
     *
     * On success: awards 50 loyalty points and logs the transaction.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'title'    => 'nullable|string|max:255',
            'comment'  => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify the user has a qualifying order for this product
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereIn('status', ['delivered', 'shipped', 'processing'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        if (! $hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        // One review per user per product
        if (Review::where('user_id', auth()->id())->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Upload optional review photos as WebP to /storage/reviews
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = \App\Helpers\ImageHelper::storeAsWebp($photo, 'reviews');
            }
        }

        Review::create([
            'user_id'     => auth()->id(),
            'product_id'  => $product->id,
            'rating'      => $request->rating,
            'title'       => $request->title,
            'comment'     => $request->comment,
            'is_approved' => true,  // auto-approve; admin can revoke via ReviewController@toggleApproval
            'photos'      => empty($photoPaths) ? null : $photoPaths,
        ]);

        // Gamification: 50 points per first review on a product (duplicate blocked above)
        $pointsToAward = 50;
        auth()->user()->increment('loyalty_points', $pointsToAward);

        RewardPointTransaction::create([
            'user_id'     => auth()->id(),
            'amount'      => $pointsToAward,
            'type'        => 'earned',
            'description' => 'Earned for reviewing '.$product->name,
            'order_id'    => null,
        ]);

        return back()->with('success', "Your review has been published! You earned {$pointsToAward} loyalty points.");
    }
}
