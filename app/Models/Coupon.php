<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
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

    public function isValid(float $orderAmount = 0): bool
    {
        return $this->getValidationError($orderAmount) === null;
    }

    public function getValidationError(float $orderAmount = 0): ?string
    {
        if (!$this->is_active) {
            return 'This coupon is no longer active.';
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return 'This coupon is not yet active (starts ' . $this->valid_from->format('M d, Y') . ').';
        }
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return 'This coupon has expired.';
        }
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return 'Your order total must be at least £' . number_format($this->min_order_amount, 2) . ' to use this coupon.';
        }
        return null;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return round($subtotal * ($this->discount_value / 100), 2);
        }
        return min($this->discount_value, $subtotal);
    }
}
