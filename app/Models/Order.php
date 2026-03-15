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
     * Update order status and manage tracking dates robustly.
     */
    public function updateStatusAndTracking(string $status, ?string $processingDate = null, ?string $shippedDate = null, ?string $deliveredDate = null)
    {
        $oldStatus = $this->status;
        $updates = ['status' => $status];

        // Ensure dates are parsed correctly
        $proc = $processingDate ? \Carbon\Carbon::parse($processingDate) : $this->processing_date;
        $ship = $shippedDate ? \Carbon\Carbon::parse($shippedDate) : $this->shipped_date;
        $del = $deliveredDate ? \Carbon\Carbon::parse($deliveredDate) : $this->delivered_date;

        // Auto-fill preceding dates if status is advanced
        if ($status === 'processing' || $status === 'shipped' || $status === 'delivered') {
            $proc = $proc ?: now();
        }
        if ($status === 'shipped' || $status === 'delivered') {
            $ship = $ship ?: now();
        }
        if ($status === 'delivered') {
            $del = $del ?: now();
        }

        $updates['processing_date'] = $proc;
        $updates['shipped_date'] = $ship;
        $updates['delivered_date'] = $del;

        $this->update($updates);

        // Restore stock if cancelled
        if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->restoreStock();
        }

        return $oldStatus !== $status;
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
