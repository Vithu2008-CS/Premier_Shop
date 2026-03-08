<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $table = 'shipping_settings';

    protected $fillable = [
        'origin_address',
        'free_delivery_threshold',
        'free_delivery_radius_miles',
        'surcharge_per_mile',
        'flat_rate_fee',
    ];

    protected function casts(): array
    {
        return [
            'free_delivery_threshold' => 'decimal:2',
            'free_delivery_radius_miles' => 'decimal:2',
            'surcharge_per_mile' => 'decimal:2',
            'flat_rate_fee' => 'decimal:2',
        ];
    }
}
