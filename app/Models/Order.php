<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_cost',
        'total',
        'shipping_address',
        'payment_intent_id',
        'payment_status',
        'distance',
        'cancellation_reason',
        'processing_date',
        'shipped_date',
        'delivered_date',
        'driver_id',
        'delivery_proof',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'distance' => 'decimal:2',
            'processing_date' => 'datetime',
            'shipped_date' => 'datetime',
            'delivered_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getQrCodeUrlAttribute()
    {
        $data = route('orders.show', $this);
        return 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
            'size' => '150x150',
            'data' => $data,
            'color' => '3498DB',
            'margin' => 0,
        ]);
    }

    /**
     * Restore stock for all products in this order.
     */
    public function restoreStock()
    {
        foreach ($this->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }
    }

    public static function generateOrderNumber(): string
    {
        return 'PS-' . strtoupper(uniqid());
    }
}
