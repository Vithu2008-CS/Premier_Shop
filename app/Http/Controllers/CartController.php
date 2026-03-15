<?php

namespace App\Http\Controllers;

use App\Models\UserItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = auth()->user()->cartItems()->with('product')->get();
        return view('cart.index', compact('items'));
    }

    public function add(Request $request)
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

        $cartItem = UserItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('type', 'cart')
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cannot add more. Stock limit reached.']);
                }
                return back()->with('error', 'Cannot add more. Stock limit reached.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            UserItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'cart',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$product->name} added to cart!",
                'cartCount' => auth()->user()->cartItems()->sum('quantity')
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

        $cartItem = UserItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('type', 'cart')
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Cannot add more. Stock limit reached.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            UserItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'cart',
            ]);
        }

        return redirect()->route('checkout.index');
    }

    public function update(Request $request, UserItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id() || $cartItem->type !== 'cart') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($request->quantity > $cartItem->product->stock) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Not enough stock available.']);
            }
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->wantsJson()) {
            $user = auth()->user();
            $items = $user->cartItems()->with('product')->get();
            $subtotal = $items->sum(fn($i) => $i->product->price * $i->quantity);
            $totalItems = $items->sum('quantity');
            
            $settings = Setting::first();
            $threshold = $settings ? $settings->free_delivery_threshold : 50;
            $baseFee = $settings ? $settings->flat_rate_fee : 5.99;
            
            $shippingCost = $subtotal >= $threshold ? 0 : $baseFee;
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'lineTotal' => number_format($cartItem->line_total, 2),
                'subtotal' => number_format($subtotal, 2),
                'totalItems' => $totalItems,
                'shipping' => $shippingCost == 0 ? 'Free' : '£' . number_format($baseFee, 2),
                'total' => number_format($subtotal + $shippingCost, 2)
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, UserItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id() || $cartItem->type !== 'cart') {
            abort(403, 'Unauthorized action.');
        }

        $cartItem->delete();

        if ($request->wantsJson()) {
            $user = auth()->user();
            $items = $user->cartItems()->with('product')->get();
            if ($items->isEmpty()) {
                return response()->json(['success' => true, 'empty' => true]);
            }
            
            $subtotal = $items->sum(fn($i) => $i->product->price * $i->quantity);
            $totalItems = $items->sum('quantity');
            
            $settings = Setting::first();
            $threshold = $settings ? $settings->free_delivery_threshold : 50;
            $baseFee = $settings ? $settings->flat_rate_fee : 5.99;
            
            $shippingCost = $subtotal >= $threshold ? 0 : $baseFee;
            
            return response()->json([
                'success' => true,
                'empty' => false,
                'message' => 'Item removed from cart.',
                'subtotal' => number_format($subtotal, 2),
                'totalItems' => $totalItems,
                'shipping' => $shippingCost == 0 ? 'Free' : '£' . number_format($baseFee, 2),
                'total' => number_format($subtotal + $shippingCost, 2)
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }
}
