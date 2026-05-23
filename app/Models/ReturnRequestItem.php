<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single product line within a return request.
 *
 * Stores the quantity being returned for a specific order line item.
 * quantity must be <= the original OrderItem quantity (validated in
 * ReturnRequestController before any DB writes).
 */
class ReturnRequestItem extends Model
{
    protected $fillable = [
        'return_request_id',
        'order_item_id',
        'quantity',  // how many units of this product the customer wants to return
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /** The original order line this return item corresponds to. */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
