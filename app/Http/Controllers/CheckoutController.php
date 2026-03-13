<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Mail\OrderReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
use App\Services\ShippingService;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Filter items if specifically requested from cart checkboxes
        if ($request->has('items')) {
            $selectedIds = $request->items;
            $cart->setRelation('items', $cart->items->whereIn('id', $selectedIds));
            
            if ($cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Please select at least one item to checkout.');
            }
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
        
        // Handle filtered items if passed in request
        if ($request->has('items')) {
            $cart->setRelation('items', $cart->items->whereIn('id', $request->items));
        }

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
            'phone' => 'required|string|max:20',
            'items' => 'nullable|array',
        ]);

        $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Filter items if specific IDs were passed
        $purchasedItems = $cart->items;
        if ($request->has('items')) {
            $purchasedItems = $cart->items->whereIn('id', $request->items);
            if ($purchasedItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
            }
        }

        // Fetch shipping settings
        $shippingSettings = \App\Models\ShippingSetting::first();

        // Calculate totals based on purchased items only
        $subtotal = $purchasedItems->sum('line_total');
        $shippingCost = $shippingSettings ? $shippingSettings->flat_rate_fee : 5.99;
        $distance = null;

        if ($shippingSettings) {
            $origin = $shippingSettings->origin_address ?? config('app.address', 'United Kingdom');
            $destination = "{$request->address_line}, {$request->city}, UK";
            $distance = $this->shippingService->calculateDrivingDistance($origin, $destination);

            if ($subtotal >= $shippingSettings->free_delivery_threshold) {
                $shippingCost = 0.00;
            } elseif ($distance !== null) {
                $distanceInMiles = $distance * 0.621371;
                if ($distanceInMiles <= $shippingSettings->free_delivery_radius_miles) {
                    $shippingCost = 0.00;
                } else {
                    $extraMiles = max(0, $distanceInMiles - $shippingSettings->free_delivery_radius_miles);
                    $shippingCost = $shippingSettings->flat_rate_fee + ($extraMiles * $shippingSettings->surcharge_per_mile);
                }
            }
        }

        $discount = 0;
        $couponCode = null;

        // Apply coupon if exists
        if (session('coupon')) {
            $coupon = Coupon::find(session('coupon.id'));
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
            }
        }

        $total = $subtotal - $discount + $shippingCost;

        try {
            $order = DB::transaction(function () use ($request, $purchasedItems, $subtotal, $discount, $couponCode, $shippingCost, $distance, $total) {
                // Re-validate stock and age restriction inside transaction
                foreach ($purchasedItems as $item) {
                    if ($item->quantity > $item->product->stock) {
                        throw new \Exception("Not enough stock for {$item->product->name}.");
                    }
                    
                    if ($item->product->is_age_restricted && auth()->user()->isUnder16()) {
                        throw new \Exception("You must be 16 or older to purchase {$item->product->name}.");
                    }
                }

                // Create order
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => Order::generateOrderNumber(),
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'coupon_code' => $couponCode,
                    'shipping_cost' => $shippingCost,
                    'distance' => $distance,
                    'total' => $total,
                    'shipping_address' => [
                        'address_line' => $request->address_line,
                        'city' => $request->city,
                        'phone' => $request->phone,
                    ],
                    'payment_status' => 'completed',
                ]);

                // Create order items and reduce stock
                foreach ($purchasedItems as $item) {
                    // Calculate effective price (accounting for offers)
                    $effectivePrice = $item->product->price;
                    if ($item->product->has_offer && $item->quantity >= $item->product->offer_min_qty) {
                        $effectivePrice = $item->product->offer_price;
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $effectivePrice,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                }

                if (session('coupon')) {
                    $coupon = Coupon::find(session('coupon.id'));
                    if ($coupon) {
                        $coupon->increment('times_used');
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Clear only purchased items from cart
        $purchasedIds = $purchasedItems->pluck('id');
        CartItem::whereIn('id', $purchasedIds)->delete();
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

    public function calculateShipping(Request $request)
    {
        // For distance calculation, address_line or city is now required as we removed postcode
        $request->validate([
            'address_line' => 'required|string',
            'city' => 'required|string'
        ]);

        $shippingSettings = \App\Models\ShippingSetting::first();
        if (!$shippingSettings) {
            return response()->json(['cost' => 5.99, 'distance' => null, 'message' => 'Flat rate typical shipping.']);
        }

        $cart = Cart::where('user_id', auth()->id())->first();
        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 400);
        }

        $origin = $shippingSettings->origin_address ?? config('app.address', 'United Kingdom');

        // Use full address for precision
        $addressParts = array_filter([
            $request->address_line ?? null,
            $request->city ?? null
        ]);
        $destination = implode(', ', $addressParts) . ', UK';

        $distance = $this->shippingService->calculateDrivingDistance($origin, $destination);

        $shippingCost = $shippingSettings->flat_rate_fee;
        $message = "Flat rate shipping.";

        if ($cart->subtotal >= $shippingSettings->free_delivery_threshold) {
            $shippingCost = 0.00;
            $message = "Free shipping (Over £{$shippingSettings->free_delivery_threshold})";
        } elseif ($distance !== null) {
            $distanceInMiles = $distance * 0.621371;

            if ($distanceInMiles <= $shippingSettings->free_delivery_radius_miles) {
                $shippingCost = 0.00;
                $message = "Free local delivery (" . number_format($distanceInMiles, 1) . " miles)";
            } else {
                $extraMiles = $distanceInMiles - $shippingSettings->free_delivery_radius_miles;
                $shippingCost = $shippingSettings->flat_rate_fee + ($extraMiles * $shippingSettings->surcharge_per_mile);
                $message = "Distance: " . number_format($distanceInMiles, 1) . " miles. Includes £" . number_format($shippingSettings->surcharge_per_mile, 2) . "/mile surcharge outside free radius.";
            }
        }

        return response()->json([
            'cost' => round($shippingCost, 2),
            'distance' => $distance ? round($distance * 0.621371, 1) : null,
            'message' => $message
        ]);
    }
}
