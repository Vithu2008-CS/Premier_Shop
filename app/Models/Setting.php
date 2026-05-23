<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Single-row application settings (uses first() everywhere — no ID needed).
 *
 * Shipping tiers (evaluated in order in CheckoutController):
 *   1. subtotal >= free_delivery_threshold  → free shipping
 *   2. distance <= free_delivery_radius_miles → free local delivery
 *   3. otherwise: flat_rate_fee + (extra_miles × surcharge_per_mile)
 *
 * other_settings is a JSON column used for miscellaneous feature flags and
 * values that don't warrant their own DB column (e.g. loyalty settings).
 * Access via the static get() helper which checks both flat columns and the
 * JSON blob, with an optional legacy key mapping for backwards compatibility.
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'origin_address',               // used as origin for shipping distance calc
        'free_delivery_threshold',      // order value above which shipping is free
        'free_delivery_radius_miles',   // local delivery radius (free within)
        'surcharge_per_mile',           // extra charge per mile beyond free radius
        'flat_rate_fee',                // base shipping rate
        'other_settings',               // JSON blob for feature flags and misc values
    ];

    protected $casts = [
        'other_settings'              => 'array',
        'free_delivery_threshold'     => 'decimal:2',
        'free_delivery_radius_miles'  => 'decimal:2',
        'surcharge_per_mile'          => 'decimal:2',
        'flat_rate_fee'               => 'decimal:2',
    ];

    /**
     * Retrieve any setting value by key with a fallback default.
     *
     * Lookup order:
     *   1. Direct column on the settings row
     *   2. Legacy key mapping (e.g. 'shop_address' → 'origin_address')
     *   3. Key inside the other_settings JSON column
     *   4. $default
     *
     * Uses getAttributes() instead of magic __get to avoid throwing when
     * a column name is passed that doesn't exist in the current DB schema.
     */
    public static function get($key, $default = null)
    {
        $settings = self::first();
        if (! $settings) {
            return $default;
        }

        $attributes = $settings->getAttributes();

        // Check for a direct column match
        if (array_key_exists($key, $attributes)) {
            return $settings->{$key};
        }

        // Legacy / alternative key aliases
        $mappings = [
            'shop_address' => 'origin_address',
        ];

        if (array_key_exists($key, $mappings)) {
            $mappedKey = $mappings[$key];
            if (array_key_exists($mappedKey, $attributes)) {
                return $settings->{$mappedKey};
            }
        }

        // Fall through to the JSON blob
        if ($settings->other_settings && isset($settings->other_settings[$key])) {
            return $settings->other_settings[$key];
        }

        return $default;
    }
}
