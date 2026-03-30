<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use App\Models\RewardPointTransaction;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 1. Verify user purchased the product
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereIn('status', ['delivered', 'shipped', 'processing'])
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        // 2. Prevent duplicate reviews
        $existingReview = Review::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // 3. Handle Photo Uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                $photoPaths[] = $path;
            }
        }

        // 4. Create Review
        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve
            'photos' => empty($photoPaths) ? null : $photoPaths,
        ]);

        // 5. Gamification: Award 50 loyalty points if it's their FIRST review on this product (which is guaranteed by Step 2)
        $pointsToAward = 50;
        auth()->user()->increment('loyalty_points', $pointsToAward);
        
        RewardPointTransaction::create([
            'user_id' => auth()->id(),
            'amount' => $pointsToAward,
            'type' => 'earned',
            'description' => "Earned for reviewing " . $product->name,
            'order_id' => null, // Not tied to an order
        ]);

        return back()->with('success', 'Your review has been published! You earned ' . $pointsToAward . ' loyalty points.');
    }
}
