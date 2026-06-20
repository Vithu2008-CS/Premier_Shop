<?php

namespace App\Http\Controllers;

use App\Services\DeliveryZoneService;
use Illuminate\Http\Request;

/**
 * Handles secure real-time AJAX delivery charge quotes for the checkout page.
 * Distance (store → customer) comes from Google, pricing from the admin's
 * delivery zones, with a flat-rate fallback when neither resolves.
 */
class ShippingCalculationController extends Controller
{
    protected DeliveryZoneService $deliveryZones;

    public function __construct(DeliveryZoneService $deliveryZones)
    {
        $this->deliveryZones = $deliveryZones;
    }

    /**
     * Quote the delivery charge for the authenticated user's cart and address.
     */
    public function calculate(Request $request)
    {
        // Length-bound both fields: they are concatenated into the address sent
        // to the external geocoding API, so cap them to realistic sizes to stop
        // oversized/garbage payloads being forwarded upstream.
        $request->validate([
            'address_line' => 'required|string|max:255',
            'city'         => 'required|string|max:120',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'cost'           => 0.00,
                'distance_miles' => 0,
                'message'        => 'Your cart is empty.',
            ]);
        }

        $subtotal    = (float) $cartItems->sum('line_total');
        $destination = "{$request->address_line}, {$request->city}, UK";

        $quote = $this->deliveryZones->quoteForAddress($destination, $subtotal);

        return response()->json([
            // Clamp so a misconfigured zone can never quote below zero
            'cost'           => round(max(0.0, (float) $quote['cost']), 2),
            'distance_miles' => $quote['distance_miles'] !== null ? round($quote['distance_miles'], 1) : null,
            'message'        => $quote['message'],
        ]);
    }
}
