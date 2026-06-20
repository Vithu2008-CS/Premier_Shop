<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles package weight aggregation and driving distance computation via Google Maps.
 */
class ShippingCalculationService
{
    /**
     * Compute the aggregate weight of a collection of cart items.
     * Total Weight = Sum of (Item Weight * Quantity)
     *
     * @param  \Illuminate\Support\Collection  $cartItems
     */
    public function calculateCartWeight($cartItems): float
    {
        $totalWeight = 0.00;

        foreach ($cartItems as $item) {
            if ($item->product) {
                // Ensure product has a weight attribute, otherwise default to 0
                $weight = (float) ($item->product->weight ?? 0.00);
                $totalWeight += $weight * (int) $item->quantity;
            }
        }

        return round($totalWeight, 2);
    }

    /**
     * Calculate the driving distance (in miles) between the shop origin and customer destination.
     *
     * @param  string  $destination  Customer delivery address
     * @return float|null Distance in miles, or null on failure (e.g., API limits or invalid address)
     */
    public function calculateDrivingDistance(string $destination): ?float
    {
        $settings = Setting::first();

        $origin = 'SW1A 1AA, London, UK'; // Warehouse fallback postcode
        if ($settings) {
            $other = $settings->other_settings ?? [];
            $lat = $other['origin_latitude'] ?? null;
            $lng = $other['origin_longitude'] ?? null;
            if ($lat !== null && $lng !== null) {
                $origin = "{$lat},{$lng}";
            } else {
                $origin = $settings->origin_address ?? $origin;
            }
        }

        $apiKey = config('services.google.maps_key');

        if (! $apiKey) {
            Log::error('Google Maps API key is not configured inside services config.');

            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => $apiKey,
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $element = $data['rows'][0]['elements'][0] ?? [];

                if (($data['status'] ?? '') === 'OK' && ($element['status'] ?? '') === 'OK') {
                    // Distance is returned in meters — convert to kilometers
                    $distanceKm = $element['distance']['value'] / 1000;

                    // Convert kilometers to miles (1 km = 0.621371 miles)
                    $distanceMiles = $distanceKm * 0.621371;

                    return round($distanceMiles, 2);
                }

                // Log warning details for troubleshooting
                Log::warning('Google Distance Matrix warning response status: '.($data['status'] ?? 'UNKNOWN').' Element status: '.($element['status'] ?? 'UNKNOWN'));
            } else {
                Log::error('Google Distance Matrix request failed with HTTP status: '.$response->status());
            }
        } catch (\Exception $e) {
            Log::error('Google Distance Matrix exception caught: '.$e->getMessage());
        }

        return null; // Fallback indicator
    }
}
