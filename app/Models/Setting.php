<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'origin_address',
        'free_delivery_threshold',
        'free_delivery_radius_miles',
        'surcharge_per_mile',
        'flat_rate_fee',
        'other_settings',
    ];

    protected $casts = [
        'other_settings' => 'array',
        'free_delivery_threshold' => 'decimal:2',
        'free_delivery_radius_miles' => 'decimal:2',
        'surcharge_per_mile' => 'decimal:2',
        'flat_rate_fee' => 'decimal:2',
    ];

    /**
     * Get a setting value by key with optional default.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $settings = self::first();
        if (!$settings) {
            return $default;
        }

        // Check if $key exists as a direct attribute/column
        // We use array_key_exists on attributes to avoid triggering magic __get
        // which might fail if the column is missing in the database.
        $attributes = $settings->getAttributes();
        if (array_key_exists($key, $attributes)) {
            return $settings->{$key};
        }

        // Compatibility mapping for legacy or alternative names
        $mappings = [
            'shop_address' => 'origin_address',
        ];

        if (array_key_exists($key, $mappings)) {
            $mappedKey = $mappings[$key];
            if (array_key_exists($mappedKey, $attributes)) {
                return $settings->{$mappedKey};
            }
        }

        // Check in other_settings JSON column
        if ($settings->other_settings && isset($settings->other_settings[$key])) {
            return $settings->other_settings[$key];
        }

        return $default;
    }
}
