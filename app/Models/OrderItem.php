<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single product line on an order.
 *
 * price is snapshotted at the time of purchase so the order history
 * remains accurate even if the product's price changes later.
 * quantity × price = line total (computed in views, not stored).
 *
 * returnItems links to any ReturnRequestItem rows for this line,
 * allowing partial-quantity returns per product.
 */
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /** The product at the time of purchase (may be soft-deleted/renamed since). */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Return request line-items referencing this order line. */
    public function returnItems()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }
}
