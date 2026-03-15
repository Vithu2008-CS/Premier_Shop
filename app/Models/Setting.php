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
}
