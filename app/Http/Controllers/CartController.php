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
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Item removed from cart.');
    }
}
