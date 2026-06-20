<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\UserItem;
use Illuminate\Http\Request;

/**
 * Manages the authenticated user's shopping cart (UserItem rows with type = 'cart').
 * All mutating actions support both JSON (AJAX) and standard form responses.
 */
class CartController extends Controller
{
    /** Show the cart page, only including items whose product is still active. */
    public function index()
    {
        $items = auth()->user()->cartItems()
            ->with('product')
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->get();

        return view('cart.index', compact('items'));
    }

    /** Return all active cart items and calculations as JSON. */
    public function itemsJson()
    {
        $items = auth()->user()->cartItems()
            ->with('product')
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => (float) $item->product->active_price,
                        'first_image' => $item->product->first_image,
                        'stock' => $item->product->stock,
                    ]
                ];
            });

        $subtotal  = $items->sum('line_total');
        $threshold = Setting::get('free_delivery_threshold', 50);
        $baseFee   = Setting::get('flat_rate_fee', 5.99);

        return response()->json([
            'items' => $items,
            'subtotal' => (float) $subtotal,
            'freeDeliveryThreshold' => (float) $threshold,
            'flatRateFee' => (float) $baseFee,
            'cartCount' => auth()->user()->cartItems()->sum('quantity'),
        ]);
    }

    /**
     * Add a product to the cart and stay on the current page.
     * Delegates to addToCart() so logic is shared with buyNow().
     */
    public function add(Request $request)
    {
        $result = $this->addToCart($request);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return $result['success']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    /**
     * Add a product to the cart and immediately redirect to checkout.
     * Used by the "Buy Now" button on product pages.
     */
    public function buyNow(Request $request)
    {
        $result = $this->addToCart($request);

        if (! $result['success']) {
            return $request->wantsJson()
                ? response()->json($result)
                : back()->with('error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Proceeding to checkout...',
                'redirect' => route('checkout.index'),
            ]);
        }

        return redirect()->route('checkout.index');
    }

    /**
     * Core add-to-cart logic shared by add() and buyNow().
     * Validates product existence, age restriction, stock level,
     * and merges quantity into an existing cart row if present.
     * Returns an associative result array — never redirects.
     */
    private function addToCart(Request $request): array
    {
        // quantity is capped (max:1000) so a tampered request can't submit a
        // huge integer; the per-product stock ceiling is still enforced below.
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:1000',
        ]);

        $product = Product::where('is_active', true)->findOrFail($request->product_id);

        if ($product->is_age_restricted && auth()->user()->isUnder16()) {
            return ['success' => false, 'message' => 'You must be 16 or older to purchase this product.'];
        }

        if ($product->stock < $request->quantity) {
            return ['success' => false, 'message' => 'Not enough stock available.'];
        }

        $cartItem = auth()->user()->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Product already in cart — increase quantity, respecting stock ceiling
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return ['success' => false, 'message' => 'Cannot add more. Stock limit reached.'];
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            UserItem::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'type'       => 'cart',
            ]);
        }

        return [
            'success'   => true,
            'message'   => "{$product->name} added to cart!",
            // Return fresh total for the nav badge to update via JS
            'cartCount' => auth()->user()->cartItems()->sum('quantity'),
        ];
    }

    /**
     * Update the quantity of a cart item.
     * Returns updated totals as JSON when called via AJAX so the cart page
     * can recalculate without a full page reload.
     */
    public function update(Request $request, UserItem $cartItem)
    {
        // Ownership check — prevent users from modifying each other's carts
        if ($cartItem->user_id !== auth()->id() || $cartItem->type !== 'cart') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:1000']);

        if ($request->quantity > $cartItem->product->stock) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Not enough stock available.'])
                : back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->wantsJson()) {
            $items      = auth()->user()->cartItems()->with('product')->get();
            $subtotal   = $items->sum('line_total');
            $threshold  = Setting::get('free_delivery_threshold', 50);
            $baseFee    = Setting::get('flat_rate_fee', 5.99);
            $shipping   = $subtotal >= $threshold ? 0 : $baseFee;

            return response()->json([
                'success'    => true,
                'message'    => 'Cart updated.',
                'lineTotal'  => number_format($cartItem->line_total, 2),
                'subtotal'   => number_format($subtotal, 2),
                'totalItems' => $items->sum('quantity'),
                'shipping'   => $shipping == 0 ? 'Free' : '£'.number_format($baseFee, 2),
                'total'      => number_format($subtotal + $shipping, 2),
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove a single item from the cart.
     * When the cart is now empty the JSON response includes `empty: true`
     * so the JS can swap in the empty-cart UI without a reload.
     */
    public function remove(Request $request, UserItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id() || $cartItem->type !== 'cart') {
            abort(403, 'Unauthorized action.');
        }

        $cartItem->delete();

        if ($request->wantsJson()) {
            $items = auth()->user()->cartItems()->with('product')->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => true, 'empty' => true]);
            }

            $subtotal  = $items->sum('line_total');
            $threshold = Setting::get('free_delivery_threshold', 50);
            $baseFee   = Setting::get('flat_rate_fee', 5.99);
            $shipping  = $subtotal >= $threshold ? 0 : $baseFee;

            return response()->json([
                'success'    => true,
                'empty'      => false,
                'message'    => 'Item removed from cart.',
                'subtotal'   => number_format($subtotal, 2),
                'totalItems' => $items->sum('quantity'),
                'shipping'   => $shipping == 0 ? 'Free' : '£'.number_format($baseFee, 2),
                'total'      => number_format($subtotal + $shipping, 2),
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }
}
