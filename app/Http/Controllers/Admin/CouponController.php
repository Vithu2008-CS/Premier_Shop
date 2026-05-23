<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

/**
 * CRUD controller for discount coupons.
 * Coupon codes are always stored uppercase for case-insensitive matching at checkout.
 */
class CouponController extends Controller
{
    /** List all coupons, newest first, paginated. */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /** Show the create-coupon form. */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /** Validate and persist a new coupon. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons',
            'discount_type'    => 'required|in:percentage,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'valid_from'       => 'nullable|date',
            'valid_until'      => 'nullable|date|after:valid_from',
            'usage_limit'      => 'nullable|integer|min:1',
        ]);

        // Normalise to uppercase so "sale10" and "SALE10" are the same code
        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created!');
    }

    /** Show the edit form for an existing coupon. */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /** Validate and persist changes to an existing coupon. */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            // Unique rule ignores the current coupon's own row
            'code'             => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'discount_type'    => 'required|in:percentage,fixed',
            'discount_value'   => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'valid_from'       => 'nullable|date',
            'valid_until'      => 'nullable|date|after:valid_from',
            'usage_limit'      => 'nullable|integer|min:1',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated!');
    }

    /** Delete a coupon. Existing orders that used it retain their discount_amount. */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }
}
