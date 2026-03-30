<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'customer_note',
        'admin_note',
        'refund_amount',
        'photo_path'
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function restoreStock()
    {
        foreach ($this->items as $item) {
            $product = $item->orderItem->product;
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }
    }
}
