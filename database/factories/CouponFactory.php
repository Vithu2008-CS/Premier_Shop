<?php

/**
 * CouponFactory — Test/seeder factory for Coupon model.
 * Produces an active percentage coupon valid for the next 7 days.
 * Used in feature tests.
 */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('SAVE####')),
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_order_amount' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(7),
            'usage_limit' => null,
            'times_used' => 0,
            'is_active' => true,
        ];
    }
}
