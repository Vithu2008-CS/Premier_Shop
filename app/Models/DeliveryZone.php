<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Admin-defined delivery zone: a distance band (min_miles–max_miles from the
 * store) plus the pricing rule applied to orders delivered inside it.
 *
 * Fee resolution for a zone (see feeFor()):
 *   1. is_free                          → £0 for any order
 *   2. subtotal >= free_over_amount     → £0 (threshold free delivery)
 *   3. otherwise                        → delivery_fee
 */
class DeliveryZone extends Model
{
    protected $fillable = [
        'name', 'min_miles', 'max_miles', 'is_free', 'free_over_amount', 'delivery_fee',
    ];

    protected $casts = [
        'min_miles' => 'decimal:2',
        'max_miles' => 'decimal:2',
        'is_free' => 'boolean',
        'free_over_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    /**
     * Find the zone covering the given driving distance.
     * Bounds are inclusive; when bands overlap the tightest zone
     * (smallest max_miles) wins so a "0–1.5 free" zone beats a wider paid one.
     */
    public static function matchFor(float $distanceMiles): ?self
    {
        return static::where('min_miles', '<=', $distanceMiles)
            ->where('max_miles', '>=', $distanceMiles)
            ->orderBy('max_miles')
            ->first();
    }

    /** Delivery fee for an order subtotal delivered inside this zone. */
    public function feeFor(float $subtotal): float
    {
        if ($this->is_free) {
            return 0.0;
        }

        if ($this->free_over_amount !== null && $subtotal >= (float) $this->free_over_amount) {
            return 0.0;
        }

        // Clamp so a bad row can never subtract from the order total
        return max(0.0, (float) $this->delivery_fee);
    }
}
