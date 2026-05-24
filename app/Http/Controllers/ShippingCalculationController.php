<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ShippingRate;
use App\Services\ShippingCalculationService;
use Illuminate\Http\Request;

/**
 * Handles secure real-time AJAX shipping rate calculations.
 */
class ShippingCalculationController extends Controller
{
    protected ShippingCalculationService $shippingCalculationService;

    public function __construct(ShippingCalculationService $shippingCalculationService)
    {
        $this->shippingCalculationService = $shippingCalculationService;
    }

    /**
     * Calculate and return dynamic shipping fees based on cart weight and destination mileage.
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'address_line' => 'required|string',
            'city'         => 'required|string',
        ]);

        // Retrieve the current cart items for the authenticated user
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'cost'           => 0.00,
                'distance_miles' => 0,
                'weight_kg'      => 0.00,
                'message'        => 'Your cart is empty.',
            ]);
        }

        // Aggregate total weight
        $totalWeight = $this->shippingCalculationService->calculateCartWeight($cartItems);

        // Fetch global rates with fail-safe defaults
        $rates = ShippingRate::first() ?? (object) [
            'base_connection_fee' => 5.00,
            'per_mile_rate'       => 0.50,
            'per_kg_surcharge'    => 0.20,
        ];

        $baseFee        = (float) $rates->base_connection_fee;
        $perMileRate    = (float) $rates->per_mile_rate;
        $perKgSurcharge = (float) $rates->per_kg_surcharge;

        // Compile destination address
        $destination = "{$request->address_line}, {$request->city}, UK";

        // Query driving distance via Google Maps Matrix API
        $distanceMiles = $this->shippingCalculationService->calculateDrivingDistance($destination);

        if ($distanceMiles !== null) {
            // Apply standard formula
            $shippingTotal = $baseFee + ($distanceMiles * $perMileRate) + ($totalWeight * $perKgSurcharge);
            $message = sprintf(
                'Distance: %s miles. Total package weight: %s kg.',
                number_format($distanceMiles, 1),
                number_format($totalWeight, 2)
            );
        } else {
            // Robust fallback system: apply setting's flat rate or standard default
            $flatRate = Setting::first()->flat_rate_fee ?? 5.99;
            $shippingTotal = (float) $flatRate;
            $message = sprintf(
                'Flat rate applied due to distance matrix API fallback (Total package weight: %s kg).',
                number_format($totalWeight, 2)
            );
        }

        return response()->json([
            'cost'           => round($shippingTotal, 2),
            'distance_miles' => $distanceMiles !== null ? round($distanceMiles, 1) : null,
            'weight_kg'      => round($totalWeight, 2),
            'message'        => $message,
        ]);
    }
}
