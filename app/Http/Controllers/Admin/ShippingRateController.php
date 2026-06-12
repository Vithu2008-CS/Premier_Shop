<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

/**
 * Manages the admin global shipping rates (base fee, per-mile, per-kg surcharge).
 */
class ShippingRateController extends Controller
{
    /**
     * Show the shipping rates edit form.
     */
    public function index()
    {
        $rates = ShippingRate::firstOrCreate([], [
            'base_connection_fee' => 5.00,
            'per_mile_rate'       => 0.50,
            'per_kg_surcharge'    => 0.20,
        ]);

        return view('admin.shipping_rates.index', compact('rates'));
    }

    /**
     * Update the global shipping rates.
     */
    public function update(Request $request)
    {
        // gt:0 rejects negative and zero rates; max matches the decimal(8,2)
        // column capacity so an oversized rate fails validation instead of
        // erroring at the database on save
        $validated = $request->validate([
            'base_connection_fee' => 'required|numeric|gt:0|max:999999.99',
            'per_mile_rate'       => 'required|numeric|gt:0|max:999999.99',
            'per_kg_surcharge'    => 'required|numeric|gt:0|max:999999.99',
        ], [
            'base_connection_fee.gt' => 'The Base Connection Fee must be a positive number greater than 0.',
            'per_mile_rate.gt'       => 'The Per-Mile Rate must be a positive number greater than 0.',
            'per_kg_surcharge.gt'    => 'The Per-Kg Surcharge must be a positive number greater than 0.',
        ]);

        $rates = ShippingRate::firstOrCreate([], [
            'base_connection_fee' => 5.00,
            'per_mile_rate'       => 0.50,
            'per_kg_surcharge'    => 0.20,
        ]);

        $rates->update($validated);

        return redirect()->route('admin.shipping-rates.index')->with('success', 'Shipping rates updated successfully!');
    }
}
