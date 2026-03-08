<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Mail\OrderReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
        if (!$coupon->isValid($cart->subtotal)) {
            return back()->with('error', 'This coupon is not valid for your order.');
        }

        session(['coupon' => [
            'code' => $coupon->code,
            'discount' => $coupon->calculateDiscount($cart->subtotal),
            'id' => $coupon->id,
        ]]);

        return back()->with('success', "Coupon '{$coupon->code}' applied!");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Re-validate stock
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('error', "Not enough stock for {$item->product->name}.");
            }
        }

        $subtotal = $cart->subtotal;
        $discount = 0;
        $couponCode = null;

        // Apply coupon if exists
        if (session('coupon')) {
            $coupon = Coupon::find(session('coupon.id'));
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
                $coupon->increment('times_used');
            }
        }

        $shippingCost = 5.99; // flat rate default
        $total = $subtotal - $discount + $shippingCost;

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'coupon_code' => $couponCode,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'shipping_address' => [
                'address_line' => $request->address_line,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'phone' => $request->phone,
            ],
            'payment_status' => 'completed', // simplified for now
        ]);

        // Create order items and reduce stock
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
            $item->product->decrement('stock', $item->quantity);
        }

        // Clear cart and coupon
        $cart->items()->delete();
        session()->forget('coupon');

        // Send order receipt email
        $order->load('items.product', 'user');
        try {
            Mail::to($order->user->email)->send(new OrderReceipt($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order receipt: ' . $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully! A receipt has been sent to your email.');
    }
}
