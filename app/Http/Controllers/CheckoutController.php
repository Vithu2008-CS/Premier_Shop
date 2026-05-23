<?php

namespace App\Http\Controllers;

use App\Mail\OrderReceipt;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\UserItem;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Orchestrates the full checkout flow:
 *  1. index()            — Show checkout form pre-populated with cart items
 *  2. applyCoupon()      — Validate and store coupon in session
 *  3. removeCoupon()     — Remove coupon from session
 *  4. calculateShipping()— AJAX shipping cost preview as user types their address
 *  5. process()          — Place the order inside a DB transaction
 *
 * Pricing layers applied in order: subtotal → coupon discount → loyalty points → shipping.
 */
class CheckoutController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Show the checkout page.
     * Redirects to cart if it's empty.
     * Supports a subset checkout: if ?items[]=id is present, only those cart rows are shown.
     */
    public function index(Request $request)
    {
        $items = auth()->user()->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Allow checking out a subset of cart items (e.g. "buy selected")
        if ($request->has('items')) {
            $items = $items->whereIn('id', $request->items);
            if ($items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Please select at least one item to checkout.');
            }
        }

        return view('checkout.index', compact('items'));
    }

    /**
     * Validate and apply a coupon code, storing the result in session.
     * Responds with JSON when called via AJAX (live coupon field) or redirects otherwise.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
        if (! $coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        $items    = auth()->user()->cartItems()->with('product')->get();
        if ($request->has('items')) {
            $items = $items->whereIn('id', $request->items);
        }
        $subtotal = $items->sum('line_total');

        // Coupon model handles expiry, min-order, usage-limit checks
        $error = $coupon->getValidationError($subtotal);
        if ($error) {
            return back()->with('error', $error);
        }

        // Store coupon details in session so process() can apply them without a second lookup
        session(['coupon' => [
            'code'     => $coupon->code,
            'discount' => $coupon->calculateDiscount($subtotal),
            'id'       => $coupon->id,
        ]]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Coupon '{$coupon->code}' applied!",
                'coupon'   => session('coupon'),
                'subtotal' => number_format($subtotal, 2),
                'total'    => number_format($subtotal - session('coupon.discount'), 2),
            ]);
        }

        return back()->with('success', "Coupon '{$coupon->code}' applied!");
    }

    /** Remove the active coupon from the session. */
    public function removeCoupon(Request $request)
    {
        session()->forget('coupon');

        if ($request->wantsJson()) {
            $subtotal = auth()->user()->cartItems()->with('product')->get()->sum('line_total');

            return response()->json([
                'success'  => true,
                'message'  => 'Coupon removed.',
                'subtotal' => number_format($subtotal, 2),
                'total'    => number_format($subtotal, 2),
            ]);
        }

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Place the order.
     *
     * Pricing resolution:
     *  subtotal → coupon discount → optional loyalty redemption → + shipping = total
     *
     * The entire order creation (Order + OrderItems + stock decrement + points accounting)
     * runs inside a DB transaction so a mid-process failure leaves no partial data.
     *
     * Post-transaction (outside TX so a mail failure can't roll back the order):
     *  - Cart items deleted
     *  - Session coupon cleared
     *  - Order receipt emailed + archived in ContactMessage sent folder
     *  - Admin/staff notified of new order
     *  - Low-stock notifications fired for any product that drops below 10 units
     */
    public function process(Request $request)
    {
        $request->validate([
            'address_line' => 'required|string|max:255',
            'city'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'items'        => 'nullable|array',
        ]);

        $allCartItems = auth()->user()->cartItems()->with('product')->get();
        if ($allCartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Resolve which items are being purchased (all or a selected subset)
        $purchasedItems = $request->has('items')
            ? $allCartItems->whereIn('id', $request->items)
            : $allCartItems;

        if ($purchasedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        $settings = Setting::first();
        $subtotal  = $purchasedItems->sum('line_total');

        // ── Shipping calculation ─────────────────────────────────────────────
        $shippingCost = $settings ? $settings->flat_rate_fee : 5.99;
        $distance     = null;

        if ($settings) {
            $origin      = $settings->origin_address ?? config('app.address', 'United Kingdom');
            $destination = "{$request->address_line}, {$request->city}, UK";
            $distance    = $this->shippingService->calculateDrivingDistance($origin, $destination);

            if ($subtotal >= $settings->free_delivery_threshold) {
                // Order value qualifies for free delivery
                $shippingCost = 0.00;
            } elseif ($distance !== null) {
                $distanceInMiles = $distance * 0.621371; // km → miles
                if ($distanceInMiles <= $settings->free_delivery_radius_miles) {
                    // Within free local delivery radius
                    $shippingCost = 0.00;
                } else {
                    // Flat base + per-extra-mile surcharge beyond the free radius
                    $extraMiles   = max(0, $distanceInMiles - $settings->free_delivery_radius_miles);
                    $shippingCost = $settings->flat_rate_fee + ($extraMiles * $settings->surcharge_per_mile);
                }
            }
        }

        // ── Coupon discount ──────────────────────────────────────────────────
        $discount  = 0;
        $couponCode = null;

        if (session('coupon')) {
            $coupon = Coupon::find(session('coupon.id'));
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount   = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
            }
        }

        $subtotalAfterCoupon = $subtotal - $discount;

        // ── Loyalty points redemption ────────────────────────────────────────
        $pointsDiscount = 0;
        $pointsUsed     = 0;

        $loyaltyEnabled = $settings && ($settings->other_settings['loyalty_enabled'] ?? false);

        if ($request->has('use_points') && $loyaltyEnabled) {
            $userPoints     = auth()->user()->loyalty_points;
            $redemptionRate = $settings->other_settings['points_redemption_value'] ?? 0.01; // £ per point

            if ($userPoints > 0) {
                $maxValueFromPoints = $userPoints * $redemptionRate;

                if ($maxValueFromPoints >= $subtotalAfterCoupon) {
                    // Points cover the full amount — only spend what's needed
                    $pointsDiscount = $subtotalAfterCoupon;
                    $pointsUsed     = (int) ceil($subtotalAfterCoupon / $redemptionRate);
                } else {
                    // Spend all available points for a partial discount
                    $pointsDiscount = $maxValueFromPoints;
                    $pointsUsed     = $userPoints;
                }
            }
        }

        $total = $subtotalAfterCoupon - $pointsDiscount + $shippingCost;

        // ── DB Transaction: create order, items, stock decrement, points ─────
        try {
            $order = DB::transaction(function () use (
                $request, $purchasedItems, $subtotal, $discount, $couponCode,
                $shippingCost, $distance, $total, $settings, $pointsDiscount, $pointsUsed
            ) {
                // Pre-flight validation inside the transaction so stock checks are atomic
                foreach ($purchasedItems as $item) {
                    if ($item->quantity > $item->product->stock) {
                        throw new \Exception("Not enough stock for {$item->product->name}.");
                    }
                    if ($item->product->is_age_restricted && auth()->user()->isUnder16()) {
                        throw new \Exception("You must be 16 or older to purchase {$item->product->name}.");
                    }
                }

                $order = Order::create([
                    'user_id'         => auth()->id(),
                    'order_number'    => Order::generateOrderNumber(),
                    'status'          => 'pending',
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discount,
                    'coupon_code'     => $couponCode,
                    'points_discount' => $pointsDiscount,
                    'points_used'     => $pointsUsed,
                    'shipping_cost'   => $shippingCost,
                    'distance'        => $distance,
                    'total'           => $total,
                    'shipping_address' => [
                        'address_line' => $request->address_line,
                        'city'         => $request->city,
                        'phone'        => $request->phone,
                    ],
                    'payment_status'  => 'completed',
                ]);

                // Snapshot item prices at time of purchase (price can change later)
                foreach ($purchasedItems as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->price,
                    ]);
                    $item->product->decrement('stock', $item->quantity);
                }

                // Increment coupon usage counter to enforce usage_limit
                if ($couponCode) {
                    Coupon::find(session('coupon.id'))?->increment('times_used');
                }

                // Deduct redeemed points and log the transaction
                if ($pointsUsed > 0) {
                    auth()->user()->decrement('loyalty_points', $pointsUsed);
                    \App\Models\RewardPointTransaction::create([
                        'user_id'     => auth()->id(),
                        'amount'      => -$pointsUsed,
                        'type'        => 'redeemed',
                        'description' => "Redeemed for Order #{$order->order_number}",
                        'order_id'    => $order->id,
                    ]);
                }

                // Award earned points based on the net paid amount (after discounts)
                if ($loyaltyEnabled) {
                    $ptsPerPound      = $settings->other_settings['points_per_pound'] ?? 1;
                    $earnableSubtotal = $subtotal - $discount - $pointsDiscount;
                    $pointsEarned     = (int) floor($earnableSubtotal * $ptsPerPound);

                    if ($pointsEarned > 0) {
                        auth()->user()->increment('loyalty_points', $pointsEarned);
                        \App\Models\RewardPointTransaction::create([
                            'user_id'     => auth()->id(),
                            'amount'      => $pointsEarned,
                            'type'        => 'earned',
                            'description' => "Earned from Order #{$order->order_number}",
                            'order_id'    => $order->id,
                        ]);
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // ── Post-transaction tasks (non-atomic, failures are logged not fatal) ─
        // Clear purchased items from cart and reset session coupon
        UserItem::whereIn('id', $purchasedItems->pluck('id'))->delete();
        session()->forget('coupon');

        // Email receipt and archive a copy in the admin mail sent folder
        $order->load('items.product', 'user');
        try {
            Mail::to($order->user->email)->send(new OrderReceipt($order));

            $htmlContent = view('emails.order-receipt', compact('order'))->render();
            \App\Models\ContactMessage::create([
                'name'    => 'System (Checkout)',
                'email'   => $order->user->email,
                'subject' => 'Your Premier Shop Order #'.$order->order_number,
                'message' => $htmlContent,
                'is_read' => true,
                'folder'  => 'sent',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order receipt: '.$e->getMessage());
        }

        // Notify admin/staff of the new order
        \App\Models\AppNotification::notifyNewOrder($order);

        // Fire low-stock alerts for any product that dropped below 10 units
        foreach ($purchasedItems as $item) {
            if ($item->product->stock < 10) {
                \App\Models\AppNotification::notifyLowStock($item->product);
            }
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
    }

    /**
     * AJAX endpoint that calculates a real-time shipping cost preview
     * as the user types their delivery address on the checkout page.
     * Uses ShippingService to get the driving distance, then applies the
     * same tier logic as process().
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'address_line' => 'required|string',
            'city'         => 'required|string',
        ]);

        $settings = Setting::first();
        if (! $settings) {
            return response()->json(['cost' => 5.99, 'distance' => null, 'message' => 'Flat rate shipping.']);
        }

        $items    = auth()->user()->cartItems()->with('product')->get();
        $subtotal = $items->sum('line_total');

        $origin      = $settings->origin_address ?? 'United Kingdom';
        $destination = "{$request->address_line}, {$request->city}, UK";
        $distance    = $this->shippingService->calculateDrivingDistance($origin, $destination);

        $shippingCost = $settings->flat_rate_fee;
        $message      = 'Flat rate shipping.';

        if ($subtotal >= $settings->free_delivery_threshold) {
            $shippingCost = 0.00;
            $message      = "Free shipping (Over £{$settings->free_delivery_threshold})";
        } elseif ($distance !== null) {
            $distanceInMiles = $distance * 0.621371;
            if ($distanceInMiles <= $settings->free_delivery_radius_miles) {
                $shippingCost = 0.00;
                $message      = 'Free local delivery ('.number_format($distanceInMiles, 1).' miles)';
            } else {
                $extraMiles   = $distanceInMiles - $settings->free_delivery_radius_miles;
                $shippingCost = $settings->flat_rate_fee + ($extraMiles * $settings->surcharge_per_mile);
                $message      = 'Distance: '.number_format($distanceInMiles, 1).' miles.';
            }
        }

        return response()->json([
            'cost'     => round($shippingCost, 2),
            'distance' => $distance ? round($distance * 0.621371, 1) : null,
            'message'  => $message,
        ]);
    }
}
