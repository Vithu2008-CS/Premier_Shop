<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Discount coupon that can be applied at checkout.
 *
 * Two discount types supported:
 *  - 'percentage' — reduce subtotal by discount_value %
 *  - 'fixed'      — subtract a flat discount_value (capped at subtotal)
 *
 * Validity is controlled by: is_active flag, valid_from/valid_until date range,
 * usage_limit (null = unlimited), and min_order_amount threshold.
 */
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_order_amount',
        'valid_from', 'valid_until', 'usage_limit', 'times_used', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ── Validation ───────────────────────────────────────────────────────────

    /** Returns true when the coupon passes all validity checks for the given order amount. */
    public function isValid(float $orderAmount = 0): bool
    {
        return $this->getValidationError($orderAmount) === null;
    }

    /**
     * Returns a human-readable error string if the coupon fails any check,
     * or null when the coupon is fully valid. Used to surface specific errors
     * in both the checkout AJAX response and the back-redirect flash message.
     */
    public function getValidationError(float $orderAmount = 0): ?string
    {
        if (! $this->is_active) {
            return 'This coupon is no longer active.';
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return 'This coupon is not yet active (starts '.$this->valid_from->format('M d, Y').').';
        }
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return 'This coupon has expired.';
        }
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return 'Your order total must be at least £'.number_format($this->min_order_amount, 2).' to use this coupon.';
        }

        return null;
    }

    // ── Discount calculation ─────────────────────────────────────────────────

    /**
     * Calculate the discount amount to deduct from the given subtotal.
     * Both types are capped at the subtotal so the total can never go negative
     * (admin validation caps percentages at 100, but rows that predate that
     * rule — or are seeded directly — must not produce a negative total).
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return min(round($subtotal * ($this->discount_value / 100), 2), $subtotal);
        }

        // Fixed: never discount more than the order is worth
        return min((float) $this->discount_value, $subtotal);
    }
}
