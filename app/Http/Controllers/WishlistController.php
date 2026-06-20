<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserItem;
use Illuminate\Http\Request;

/**
 * Manages the authenticated user's wishlist (UserItem rows with type = 'wishlist').
 * The toggle action adds the product if not present, removes it if it is —
 * both AJAX and standard form responses are supported.
 */
class WishlistController extends Controller
{
    /** Display the user's wishlist with their products, paginated. */
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->latest()->paginate(12);

        return view('wishlists.index', compact('wishlists'));
    }

    /**
     * Toggle a product's wishlist status for the current user.
     * Returns JSON with `status: 'added' | 'removed'` for the heart-icon JS to update.
     */
    public function toggle(Request $request, Product $product)
    {
        $existing = UserItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('type', 'wishlist')
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Product removed from wishlist.';
        } else {
            UserItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'type' => 'wishlist',
            ]);
            $status = 'added';
            $message = 'Product added to wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $status, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
