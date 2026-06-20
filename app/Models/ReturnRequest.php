<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A customer's request to return items from a delivered order.
 *
 * Status lifecycle: pending → approved | rejected → refunded
 *
 * One return request per order maximum (enforced in ReturnRequestController).
 * Individual items and their quantities are stored as ReturnRequestItem rows.
 * An optional photo_path allows the customer to upload evidence of damage.
 */
class ReturnRequest extends Model
{
    /**
     * Allowed status transitions. Keys are the current status; values are the
     * statuses an admin may move to. Same-status saves (note/amount edits) are
     * always allowed via canTransitionTo(). 'refunded' is terminal — money has
     * been sent, so the status can no longer change.
     */
    public const ALLOWED_TRANSITIONS = [
        'pending' => ['approved', 'rejected'],
        'approved' => ['pending', 'rejected', 'refunded'],
        'rejected' => ['pending', 'approved'],
        'refunded' => [],
    ];

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'customer_note',
        'admin_note',       // staff response visible to customer
        'refund_amount',    // populated by admin when status → refunded
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** The individual product lines being returned. */
    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    // ── Business logic ───────────────────────────────────────────────────────

    /**
     * Whether the admin may move this return to the given status.
     * Same-status saves are allowed so notes/amounts can be edited without
     * a status change.
     */
    public function canTransitionTo(string $status): bool
    {
        if ($status === $this->status) {
            return true;
        }

        return in_array($status, self::ALLOWED_TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Return the stock for all items in this return request back to the product.
     * Called by admin when approving or refunding a return.
     */
    public function restoreStock()
    {
        foreach ($this->items as $item) {
            if ($item->orderItem) {
                $product = $item->orderItem->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
    }

    /**
     * Reverse of restoreStock(): remove the returned quantities from product
     * stock again. Called when a return leaves 'approved' (e.g. an admin
     * corrects a mis-click by moving it back to pending/rejected).
     * Clamped at zero so intervening sales can't drive stock negative.
     */
    public function deductStock()
    {
        foreach ($this->items as $item) {
            if ($item->orderItem) {
                $product = $item->orderItem->product;
                if ($product) {
                    $product->update(['stock' => max(0, $product->stock - $item->quantity)]);
                }
            }
        }
    }
}
