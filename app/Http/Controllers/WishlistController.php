<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->latest()->paginate(12);
        return view('wishlists.index', compact('wishlists'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Product removed from wishlist.');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            return back()->with('success', 'Product added to wishlist.');
        }
    }
}
