<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Immutable audit log entry for a loyalty points movement.
 *
 * Transaction types:
 *   'earned'   — points awarded after a purchase (positive amount)
 *   'redeemed' — points spent at checkout (negative amount)
 *   'refunded' — clawback (negative) or re-credit (positive) on cancellation
 *
 * amount sign convention: positive = points added to user balance,
 * negative = points deducted. The User::loyalty_points column is
 * the running balance; these rows are the audit trail behind it.
 *
 * Clawback flow on order cancellation (Order::restoreStock):
 *   - Earned points → negative 'refunded' transaction + decrement balance
 *   - Redeemed points → positive 'refunded' transaction + increment balance
 */
class RewardPointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',       // signed integer: + = credit, - = debit
        'type',         // 'earned' | 'redeemed' | 'refunded'
        'description',  // human-readable reason shown in transaction history
        'order_id',     // nullable — links to the triggering order
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
