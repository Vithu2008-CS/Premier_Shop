<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wraps the Google Maps Distance Matrix API to calculate driving distances.
 *
 * Used by CheckoutController and its calculateShipping() AJAX endpoint to
 * determine the distance between the shop's origin address and the customer's
 * delivery address, then apply the correct shipping tier.
 *
 * Returns null on any API failure so the caller can fall back to the flat rate
 * rather than blocking checkout. All failures are logged for observability.
 *
 * Config key: services.google.maps_key (set GOOGLE_MAPS_KEY in .env)
 */
class ShippingService
{
    /**
     * Calculate the driving distance (in kilometres) between two addresses.
     *
     * @param  string  $origin       Shop address string (from Setting::origin_address)
     * @param  string  $destination  Customer delivery address "Line, City, UK"
     * @return float|null  Kilometres, or null if the API is unavailable / no route found
     */
    public function calculateDrivingDistance(string $origin, string $destination): ?float
    {
        $apiKey = config('services.google.maps_key');

        if (! $apiKey) {
            Log::error('Google Maps API key is not configured.');

            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins'      => $origin,
                'destinations' => $destination,
                'key'          => $apiKey,
                'units'        => 'metric',
            ]);

            if ($response->successful()) {
                $data    = $response->json();
                $element = $data['rows'][0]['elements'][0] ?? [];

                if (($data['status'] ?? '') === 'OK' && ($element['status'] ?? '') === 'OK') {
                    // Distance value from the API is in metres — convert to km
                    return $element['distance']['value'] / 1000;
                }

                // Log informative warnings for diagnosable failures
                if (($element['status'] ?? '') === 'ZERO_RESULTS') {
                    Log::warning('Google Maps Distance Matrix: No route found between origin and destination.');
                } elseif (($data['status'] ?? '') === 'ZERO_RESULTS') {
                    Log::warning('Google Maps Distance Matrix: API returned ZERO_RESULTS.');
                } else {
                    Log::error('Google Maps Distance Matrix API status: '.($data['status'] ?? 'UNKNOWN'));
                }
            } else {
                Log::error('Google Maps Distance Matrix API request failed with status: '.$response->status());
            }
        } catch (\Exception $e) {
            Log::error('Google Maps Distance Matrix API exception: '.$e->getMessage());
        }

        return null;
    }
}
