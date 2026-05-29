<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a customer purchase order.
 *
 * Status lifecycle: pending → processing → shipped → delivered | cancelled
 *
 * Money fields use decimal:2 casts so arithmetic stays precise.
 * The shipping_address is stored as a JSON blob (cast to array) so it
 * preserves the address at the time of purchase even if the user later
 * updates their saved addresses.
 */
class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'status',
        'subtotal', 'discount_amount', 'coupon_code',
        'points_discount', 'points_used',
        'shipping_cost', 'total', 'shipping_address',
        'payment_intent_id', 'payment_status', 'payment_method',
        'distance', 'cancellation_reason',
        'processing_date', 'shipped_date', 'delivered_date',
        'driver_id', 'delivery_proof',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal'         => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'points_discount'  => 'decimal:2',
            'points_used'      => 'integer',
            'shipping_cost'    => 'decimal:2',
            'total'            => 'decimal:2',
            'distance'         => 'decimal:2',
            'processing_date'  => 'datetime',
            'shipped_date'     => 'datetime',
            'delivered_date'   => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /** The customer who placed the order. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The delivery driver assigned to fulfil this order. */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /** The individual product line-items on this order. */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /** The customer's return request for this order (one per order max). */
    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class);
    }

    /** Loyalty point earn/redeem/clawback transactions linked to this order. */
    public function rewardPointTransactions()
    {
        return $this->hasMany(RewardPointTransaction::class);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Generate a QR code URL pointing to the order detail page.
     * Used on printed receipts so the customer can pull up their order by scanning.
     */
    public function getQrCodeUrlAttribute(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?'.http_build_query([
            'size'   => '150x150',
            'data'   => route('orders.show', $this),
            'color'  => '3498DB',
            'margin' => 0,
        ]);
    }

    // ── Business logic ───────────────────────────────────────────────────────

    /**
     * Update order status and auto-fill missing tracking dates.
     *
     * Date auto-fill rules:
     *  - processing → sets processing_date if not already set
     *  - shipped    → sets processing_date + shipped_date if not set
     *  - delivered  → sets all three dates if not set
     *
     * Year < 1000 guard: input "0026-05-01" is treated as "2026-05-01"
     * (MariaDB can parse two-digit years incorrectly in some locales).
     *
     * On cancellation, calls restoreStock() to reverse stock/points.
     *
     * @return bool True when the status actually changed.
     */
    public function updateStatusAndTracking(
        string $status,
        $processingDate = null,
        $shippedDate    = null,
        $deliveredDate  = null
    ): bool {
        $oldStatus = $this->status;

        $updates = [
            'status'          => $status,
            'processing_date' => $processingDate ? \Carbon\Carbon::parse($processingDate) : $this->processing_date,
            'shipped_date'    => $shippedDate    ? \Carbon\Carbon::parse($shippedDate)    : $this->shipped_date,
            'delivered_date'  => $deliveredDate  ? \Carbon\Carbon::parse($deliveredDate)  : $this->delivered_date,
        ];

        // Fix dates where a 2-digit year was parsed as year 26 instead of 2026
        foreach (['processing_date', 'shipped_date', 'delivered_date'] as $field) {
            if ($updates[$field] && $updates[$field]->year < 1000) {
                $updates[$field] = $updates[$field]->addYears(2000);
            }
        }

        // Auto-advance status when dates imply a later stage than the selected status.
        // This prevents the status badge and tracking timeline from getting out of sync
        // (e.g. admin fills in shipped_date but forgets to change the status dropdown).
        $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3, 'cancelled' => -1];
        $currentLevel = $statusOrder[$updates['status']] ?? 0;

        if ($updates['status'] !== 'cancelled') {
            if ($updates['delivered_date'] && $currentLevel < $statusOrder['delivered']) {
                $updates['status'] = 'delivered';
            } elseif ($updates['shipped_date'] && $currentLevel < $statusOrder['shipped']) {
                $updates['status'] = 'shipped';
            } elseif ($updates['processing_date'] && $currentLevel < $statusOrder['processing']) {
                $updates['status'] = 'processing';
            }
        }

        // Auto-set dates that weren't supplied but are implied by the new status
        if ($updates['status'] === 'processing' && ! $updates['processing_date']) {
            $updates['processing_date'] = now();
        }
        if ($updates['status'] === 'shipped') {
            $updates['processing_date'] = $updates['processing_date'] ?? now();
            $updates['shipped_date']    = $updates['shipped_date']    ?? now();
        }
        if ($updates['status'] === 'delivered') {
            $updates['processing_date'] = $updates['processing_date'] ?? now();
            $updates['shipped_date']    = $updates['shipped_date']    ?? now();
            $updates['delivered_date']  = $updates['delivered_date']  ?? now();
        }

        $this->update($updates);

        // Cancellation: restore stock and claw back loyalty points
        if ($updates['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->restoreStock();
        }

        return $oldStatus !== $updates['status'];
    }

    /**
     * Restore product stock for all items on this order and reverse loyalty point transactions.
     *
     * Points rules on cancellation:
     *  - Earned points are clawed back (negative 'refunded' transaction)
     *  - Redeemed (spent) points are refunded back to the customer
     */
    public function restoreStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        // Clawback points the customer earned from this order
        $earnedTx = $this->rewardPointTransactions()->where('type', 'earned')->first();
        if ($earnedTx && $this->user) {
            $this->user->decrement('loyalty_points', $earnedTx->amount);
            RewardPointTransaction::create([
                'user_id'     => $this->user_id,
                'amount'      => -$earnedTx->amount,
                'type'        => 'refunded',
                'description' => "Clawback for cancelled Order #{$this->order_number}",
                'order_id'    => $this->id,
            ]);
        }

        // Refund points the customer spent on this order
        $spentTx = $this->rewardPointTransactions()->where('type', 'redeemed')->first();
        if ($spentTx && $this->user) {
            $refundAmount = abs($spentTx->amount);
            $this->user->increment('loyalty_points', $refundAmount);
            RewardPointTransaction::create([
                'user_id'     => $this->user_id,
                'amount'      => $refundAmount,
                'type'        => 'refunded',
                'description' => "Refunded points for cancelled Order #{$this->order_number}",
                'order_id'    => $this->id,
            ]);
        }
    }

    /**
     * Generate a unique order number with a "PS-" prefix.
     * Uses uniqid() (microsecond-based) so collisions are effectively impossible.
     */
    public static function generateOrderNumber(): string
    {
        return 'PS-'.strtoupper(uniqid());
    }
}
