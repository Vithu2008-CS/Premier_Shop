<?php

namespace App\Http\Controllers;

use App\Models\UserItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Setting;
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
        $items = auth()->user()->cartItems()->with('product')->get();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        if ($request->has('items')) {
            $selectedIds = $request->items;
            $items = $items->whereIn('id', $selectedIds);
            
            if ($items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Please select at least one item to checkout.');
            }
        }

        return view('checkout.index', compact('items'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        $items = auth()->user()->cartItems()->with('product')->get();
        if ($request->has('items')) {
            $items = $items->whereIn('id', $request->items);
        }

        $subtotal = $items->sum(fn($i) => $i->product->price * $i->quantity);

        $error = $coupon->getValidationError($subtotal);
        if ($error) {
            return back()->with('error', $error);
        }

        session(['coupon' => [
            'code' => $coupon->code,
            'discount' => $coupon->calculateDiscount($subtotal),
            'id' => $coupon->id,
        ]]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Coupon '{$coupon->code}' applied!",
                'coupon' => session('coupon'),
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($subtotal - session('coupon.discount'), 2)
            ]);
        }

        return back()->with('success', "Coupon '{$coupon->code}' applied!");
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('coupon');

        if ($request->wantsJson()) {
            $items = auth()->user()->cartItems()->with('product')->get();
            $subtotal = $items->sum(fn($i) => $i->product->price * $i->quantity);
            
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed.',
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($subtotal, 2)
            ]);
        }

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

        $allCartItems = auth()->user()->cartItems()->with('product')->get();
        if ($allCartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $purchasedItems = $allCartItems;
        if ($request->has('items')) {
            $purchasedItems = $allCartItems->whereIn('id', $request->items);
            if ($purchasedItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
            }
        }

        $settings = Setting::first();

        $subtotal = $purchasedItems->sum(fn($i) => $i->product->price * $i->quantity);
        $shippingCost = $settings ? $settings->flat_rate_fee : 5.99;
        $distance = null;

        if ($settings) {
            $origin = $settings->origin_address ?? config('app.address', 'United Kingdom');
            $destination = "{$request->address_line}, {$request->city}, UK";
            $distance = $this->shippingService->calculateDrivingDistance($origin, $destination);

            if ($subtotal >= $settings->free_delivery_threshold) {
                $shippingCost = 0.00;
            } elseif ($distance !== null) {
                $distanceInMiles = $distance * 0.621371;
                if ($distanceInMiles <= $settings->free_delivery_radius_miles) {
                    $shippingCost = 0.00;
                } else {
                    $extraMiles = max(0, $distanceInMiles - $settings->free_delivery_radius_miles);
                    $shippingCost = $settings->flat_rate_fee + ($extraMiles * $settings->surcharge_per_mile);
                }
            }
        }

        $discount = 0;
        $couponCode = null;

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
                foreach ($purchasedItems as $item) {
                    if ($item->quantity > $item->product->stock) {
                        throw new \Exception("Not enough stock for {$item->product->name}.");
                    }
                    if ($item->product->is_age_restricted && auth()->user()->isUnder16()) {
                        throw new \Exception("You must be 16 or older to purchase {$item->product->name}.");
                    }
                }

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

                foreach ($purchasedItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
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

        UserItem::whereIn('id', $purchasedItems->pluck('id'))->delete();
        session()->forget('coupon');

        $order->load('items.product', 'user');
        try {
            Mail::to($order->user->email)->send(new OrderReceipt($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order receipt: ' . $e->getMessage());
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'address_line' => 'required|string',
            'city' => 'required|string'
        ]);

        $settings = Setting::first();
        if (!$settings) {
            return response()->json(['cost' => 5.99, 'distance' => null, 'message' => 'Flat rate typical shipping.']);
        }

        $items = auth()->user()->cartItems()->with('product')->get();
        $subtotal = $items->sum(fn($i) => $i->product->price * $i->quantity);

        $origin = $settings->origin_address ?? config('app.address', 'United Kingdom');
        $destination = "{$request->address_line}, {$request->city}, UK";
        $distance = $this->shippingService->calculateDrivingDistance($origin, $destination);

        $shippingCost = $settings->flat_rate_fee;
        $message = "Flat rate shipping.";

        if ($subtotal >= $settings->free_delivery_threshold) {
            $shippingCost = 0.00;
            $message = "Free shipping (Over £{$settings->free_delivery_threshold})";
        } elseif ($distance !== null) {
            $distanceInMiles = $distance * 0.621371;
            if ($distanceInMiles <= $settings->free_delivery_radius_miles) {
                $shippingCost = 0.00;
                $message = "Free local delivery (" . number_format($distanceInMiles, 1) . " miles)";
            } else {
                $extraMiles = $distanceInMiles - $settings->free_delivery_radius_miles;
                $shippingCost = $settings->flat_rate_fee + ($extraMiles * $settings->surcharge_per_mile);
                $message = "Distance: " . number_format($distanceInMiles, 1) . " miles.";
            }
        }

        return response()->json([
            'cost' => round($shippingCost, 2),
            'distance' => $distance ? round($distance * 0.621371, 1) : null,
            'message' => $message
        ]);
    }
}
