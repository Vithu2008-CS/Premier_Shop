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
        // Normalise to uppercase *before* validating so the unique check runs
        // against the stored form — "sale10" must collide with an existing "SALE10"
        // rather than pass validation and hit the DB unique constraint (500)
        $request->merge(['code' => strtoupper((string) $request->input('code'))]);

        $validated = $request->validate($this->rules($request));

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
        // Same pre-validation normalisation as store() — see comment there
        $request->merge(['code' => strtoupper((string) $request->input('code'))]);

        $validated = $request->validate($this->rules($request, $coupon));

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated!');
    }

    /**
     * Shared validation rules for store/update.
     *
     * Discount bounds: percentage coupons are capped at 100 so a coupon can never
     * inflate the discount past the order value; fixed coupons (and the min-order
     * threshold) are capped at the decimal(10,2) column maximum so an oversized
     * value fails validation instead of erroring at insert time.
     */
    private function rules(Request $request, ?Coupon $ignore = null): array
    {
        $maxDiscount = $request->input('discount_type') === 'percentage' ? 100 : 99999999.99;

        return [
            // On update, the unique rule ignores the coupon's own row
            'code' => 'required|string|max:50|unique:coupons,code'.($ignore ? ','.$ignore->id : ''),
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|gt:0|max:'.$maxDiscount,
            'min_order_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
        ];
    }

    /** Delete a coupon. Existing orders that used it retain their discount_amount. */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }
}
