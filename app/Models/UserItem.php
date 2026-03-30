<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'type', // 'cart' or 'wishlist'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeCart($query)
    {
        return $query->where('type', 'cart');
    }

    public function scopeWishlist($query)
    {
        return $query->where('type', 'wishlist');
    }

    public function getLineTotalAttribute()
    {
        if ($this->type === 'cart' && $this->product->has_offer && $this->quantity >= $this->product->offer_min_qty) {
            return $this->product->offer_price * $this->quantity;
        }
        return $this->product->price * $this->quantity;
    }
}
