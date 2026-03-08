<?php

namespace App\Services;

use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DistanceCalculationService
{
    /**
     * Calculate straight-line distance between origin and destination UK postcodes in miles.
     */
    public function calculateDistance(string $destinationPostcode): ?float
    {
        $shippingSettings = ShippingSetting::first();
        if (!$shippingSettings || !$shippingSettings->origin_postal_code) {
            Log::warning('Shipping origin postal code not configured.');
            return null;
        }

        $origin = $this->getCoordinates($shippingSettings->origin_postal_code);
        $destination = $this->getCoordinates($destinationPostcode);

        if (!$origin || !$destination) {
            return null;
        }

        return $this->haversine($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng']);
    }

    /**
     * Fetch latitude and longitude from postcodes.io.
     */
    protected function getCoordinates(string $postcode): ?array
    {
        // Clean the postcode (remove spaces) for the API request
        $cleanPostcode = str_replace(' ', '', $postcode);

        try {
            $response = Http::timeout(5)->get("https://api.postcodes.io/postcodes/{$cleanPostcode}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'lat' => $data['result']['latitude'],
                    'lng' => $data['result']['longitude'],
                ];
            }

            Log::warning("Could not geocode postcode: {$postcode}");
            return null;
        } catch (\Exception $e) {
            Log::error("Postcodes API exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate Haversine distance in miles between two coordinates.
     */
    protected function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadiusMiles = 3958.8; // Radius of earth in miles

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusMiles * $c;
    }
}
