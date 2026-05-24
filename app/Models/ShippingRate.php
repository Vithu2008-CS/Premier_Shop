<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Single-row application shipping rates configuration.
 * Always retrieve using ShippingRate::first() or ShippingRate::firstOrCreate().
 */
class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_connection_fee',
        'per_mile_rate',
        'per_kg_surcharge',
    ];

    protected $casts = [
        'base_connection_fee' => 'decimal:2',
        'per_mile_rate'       => 'decimal:2',
        'per_kg_surcharge'    => 'decimal:2',
    ];
}
