<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

/**
 * CRUD controller for delivery zones (distance-banded delivery pricing).
 * Lives under the admin "Shipping Rates" sidebar section.
 */
class DeliveryZoneController extends Controller
{
    /** List all zones ordered by their distance band. */
    public function index()
    {
        $zones = DeliveryZone::orderBy('min_miles')->orderBy('max_miles')->get();

        return view('admin.delivery_zones.index', compact('zones'));
    }

    /** Show the create-zone form. */
    public function create()
    {
        return view('admin.delivery_zones.create');
    }

    /** Validate and persist a new zone. */
    public function store(Request $request)
    {
        $validated = $this->validateZone($request);

        DeliveryZone::create($validated);

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Delivery zone created!');
    }

    /** Show the edit form for an existing zone. */
    public function edit(DeliveryZone $delivery_zone)
    {
        return view('admin.delivery_zones.edit', ['zone' => $delivery_zone]);
    }

    /** Validate and persist changes to an existing zone. */
    public function update(Request $request, DeliveryZone $delivery_zone)
    {
        $validated = $this->validateZone($request);

        $delivery_zone->update($validated);

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Delivery zone updated!');
    }

    /** Delete a zone. Orders priced with it keep their stored shipping_cost. */
    public function destroy(DeliveryZone $delivery_zone)
    {
        $delivery_zone->delete();

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Delivery zone deleted.');
    }

    /**
     * Shared store/update validation.
     *
     * Bands must be coherent (max above min) and every money/distance value is
     * non-negative and bounded by its column capacity — decimal(8,2) for miles,
     * decimal(10,2) for money — so bad input fails validation, not the insert.
     */
    private function validateZone(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_miles' => 'required|numeric|min:0|max:999999.99',
            'max_miles' => 'required|numeric|gt:min_miles|max:999999.99',
            'free_over_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'delivery_fee' => 'nullable|numeric|min:0|max:99999999.99',
        ], [
            'max_miles.gt' => 'The zone end (max miles) must be greater than its start (min miles).',
        ]);

        $validated['is_free'] = $request->has('is_free');
        $validated['delivery_fee'] = $validated['delivery_fee'] ?? 0;

        return $validated;
    }
}
