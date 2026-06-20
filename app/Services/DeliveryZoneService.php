<?php

namespace App\Services;

use App\Models\DeliveryZone;
use App\Models\Setting;

/**
 * Resolves the delivery charge for an order using admin-defined distance zones.
 *
 * Flow: Google driving distance (store → customer) → matching DeliveryZone →
 * zone fee from the order subtotal. When the distance is unavailable (API
 * failure / unparseable address) or no zone covers it, falls back to the
 * flat_rate_fee setting so checkout never breaks.
 */
class DeliveryZoneService
{
    public function __construct(protected ShippingCalculationService $distanceService) {}

    /**
     * Quote delivery for a destination address.
     *
     * @return array{cost: float, distance_miles: float|null, zone: DeliveryZone|null, message: string}
     */
    public function quoteForAddress(string $destination, float $subtotal): array
    {
        $distanceMiles = $this->distanceService->calculateDrivingDistance($destination);

        return $this->quote($distanceMiles, $subtotal);
    }

    /**
     * Quote delivery for a known driving distance.
     *
     * @return array{cost: float, distance_miles: float|null, zone: DeliveryZone|null, message: string}
     */
    public function quote(?float $distanceMiles, float $subtotal): array
    {
        if ($distanceMiles !== null && ($zone = DeliveryZone::matchFor($distanceMiles))) {
            $cost = $zone->feeFor($subtotal);

            if ($cost <= 0.0) {
                $message = $zone->is_free
                    ? sprintf('Free delivery — %s (%s miles).', $zone->name, number_format($distanceMiles, 1))
                    : sprintf('Free delivery — order over £%s (%s, %s miles).',
                        number_format((float) $zone->free_over_amount, 2), $zone->name, number_format($distanceMiles, 1));
            } else {
                $message = sprintf('Delivery to %s (%s miles).', $zone->name, number_format($distanceMiles, 1));
            }

            return [
                'cost' => $cost,
                'distance_miles' => $distanceMiles,
                'zone' => $zone,
                'message' => $message,
            ];
        }

        // Fallback: distance unknown or address outside every configured zone
        $flatRate = max(0.0, (float) (Setting::get('flat_rate_fee') ?? 5.99));

        return [
            'cost' => $flatRate,
            'distance_miles' => $distanceMiles,
            'zone' => null,
            'message' => $distanceMiles === null
                ? 'Flat rate delivery.'
                : sprintf('Flat rate delivery (%s miles — outside configured zones).', number_format($distanceMiles, 1)),
        ];
    }
}
