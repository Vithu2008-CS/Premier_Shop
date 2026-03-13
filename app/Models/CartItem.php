<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getLineTotalAttribute(): float
    {
        $price = $this->product->price;

        if ($this->product->has_offer && $this->quantity >= $this->product->offer_min_qty) {
            $price = $this->product->offer_price;
        }

        return $price * $this->quantity;
    }
}
