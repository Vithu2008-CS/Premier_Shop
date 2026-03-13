<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->load('items.product');
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Age restriction check
        if ($product->is_age_restricted && auth()->user()->isUnder16()) {
            return back()->with('error', 'You must be 16 or older to purchase this product.');
        }

        // Stock check
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Cannot add more. Stock limit reached.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', "{$product->name} added to cart!");
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->is_age_restricted && auth()->user()->isUnder16()) {
            return back()->with('error', 'You must be 16 or older to purchase this product.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        // Optional: clear cart for true "buy now" experience, or just append. 
        // We'll just append and redirect to checkout.
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Cannot add more. Stock limit reached.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('checkout.index');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($request->quantity > $cartItem->product->stock) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
            }
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->wantsJson()) {
            $cartLevel = \App\Models\Cart::with('items.product')->find($cartItem->cart_id);
            $settings = \App\Models\ShippingSetting::first();
            $threshold = $settings ? $settings->free_delivery_threshold : 50;
            $baseFee = $settings ? $settings->flat_rate_fee : 5.99;
            
            $shippingCost = $cartLevel->subtotal >= $threshold ? 0 : $baseFee;
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'lineTotal' => number_format($cartItem->line_total, 2),
                'subtotal' => number_format($cartLevel->subtotal, 2),
                'totalItems' => $cartLevel->totalItems,
                'shipping' => $shippingCost == 0 ? 'Free' : '£' . number_format($baseFee, 2),
                'total' => number_format($cartLevel->subtotal + $shippingCost, 2)
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $cartItem)
    {
        $cartId = $cartItem->cart_id;
        $cartItem->delete();

        if ($request->wantsJson()) {
            $cartLevel = \App\Models\Cart::with('items.product')->find($cartId);
            if (!$cartLevel || $cartLevel->items->isEmpty()) {
                return response()->json(['success' => true, 'empty' => true]);
            }
            
            $settings = \App\Models\ShippingSetting::first();
            $threshold = $settings ? $settings->free_delivery_threshold : 50;
            $baseFee = $settings ? $settings->flat_rate_fee : 5.99;
            
            $shippingCost = $cartLevel->subtotal >= $threshold ? 0 : $baseFee;
            
            return response()->json([
                'success' => true,
                'empty' => false,
                'message' => 'Item removed from cart.',
                'subtotal' => number_format($cartLevel->subtotal, 2),
                'totalItems' => $cartLevel->totalItems,
                'shipping' => $shippingCost == 0 ? 'Free' : '£' . number_format($baseFee, 2),
                'total' => number_format($cartLevel->subtotal + $shippingCost, 2)
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }
}
