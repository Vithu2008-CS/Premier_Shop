<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ShippingSetting::updateOrCreate(
            ['id' => 1], // Assuming single settings row
            [
                'origin_address' => 'GQ6Q+WH London, United Kingdom', // User specified shop location (Plus Code)
                'free_delivery_threshold' => 100.00,
                'free_delivery_radius_miles' => 20.00,
                'surcharge_per_mile' => 1.50,
                'flat_rate_fee' => 5.99,
            ]
        );
    }
}
