<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dual-purpose table that stores both cart and wishlist items.
 *
 * The type column ('cart' | 'wishlist') distinguishes them.
 * Using one table avoids schema duplication since both share the same
 * user_id / product_id / quantity structure.
 *
 * Scopes:
 *   cart()     — used via User::cartItems() relationship
 *   wishlist() — used via User::wishlists() relationship
 *
 * line_total accessor applies the bulk-buy offer price when the quantity
 * meets the offer threshold, otherwise uses the standard product price.
 */
class UserItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'type', // 'cart' or 'wishlist'
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeCart($query)
    {
        return $query->where('type', 'cart');
    }

    public function scopeWishlist($query)
    {
        return $query->where('type', 'wishlist');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /**
     * Total price for this cart line.
     * Uses offer_price when the cart quantity meets the bulk-buy minimum;
     * falls back to standard price otherwise.
     * Returns 0 when the product has been deleted.
     */
    public function getLineTotalAttribute()
    {
        if (! $this->product) {
            return 0;
        }

        $unitPrice = $this->product->active_price;

        // Apply bulk-buy discount when quantity qualifies
        if ($this->type === 'cart' && $this->product->has_offer && $this->quantity >= $this->product->offer_min_qty) {
            $unitPrice = $this->product->offer_price;
        }

        // Apply personalized customer offer if active
        $user = $this->user;
        if ($user && $user->offer_discount_percentage > 0) {
            $applies = false;
            if ($user->offer_scope === 'all') {
                $applies = true;
            } elseif ($user->offer_scope === 'selected' && is_array($user->offer_product_ids) && in_array($this->product_id, $user->offer_product_ids)) {
                $applies = true;
            }

            if ($applies) {
                $unitPrice = round($unitPrice * (1 - $user->offer_discount_percentage / 100), 2);
            }
        }

        return $unitPrice * $this->quantity;
    }
}
