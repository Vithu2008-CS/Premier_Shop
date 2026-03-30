<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Calculate driving distance between two addresses using Google Maps Distance Matrix API.
     *
     * @param string $origin
     * @param string $destination
     * @return float|null Distance in kilometers
     */
    public function calculateDrivingDistance(string $origin, string $destination): ?float
    {
        // Destination should ideally be "Address Line, City, UK" for maximum precision
        $apiKey = config('services.google.maps_key');

        if (!$apiKey) {
            Log::error('Google Maps API key is not configured.');
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

                if (isset($data['status']) && $data['status'] === 'OK') {
                    $element = $data['rows'][0]['elements'][0];

                    if (isset($element['status']) && $element['status'] === 'OK') {
                        // Distance is returned in meters, convert to kilometers
                        return $element['distance']['value'] / 1000;
                    }

                    if (($element['status'] ?? '') === 'ZERO_RESULTS') {
                        Log::warning('Google Maps Distance Matrix: No route found between origin and destination.');
                    } else {
                        Log::warning('Google Maps Distance Matrix element status: ' . ($element['status'] ?? 'UNKNOWN'));
                    }
                } elseif (($data['status'] ?? '') === 'ZERO_RESULTS') {
                    Log::warning('Google Maps Distance Matrix: API returned ZERO_RESULTS.');
                } else {
                    Log::error('Google Maps Distance Matrix API status: ' . ($data['status'] ?? 'UNKNOWN'));
                }
            } else {
                Log::error('Google Maps Distance Matrix API request failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Google Maps Distance Matrix API exception: ' . $e->getMessage());
        }

        return null;
    }
}
