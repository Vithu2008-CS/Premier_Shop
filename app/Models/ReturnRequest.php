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
}
