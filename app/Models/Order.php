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

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class);
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
    public function updateStatusAndTracking(string $status, $processingDate = null, $shippedDate = null, $deliveredDate = null)
    {
        $oldStatus = $this->status;
        
        $updates = [
            'status' => $status,
            'processing_date' => $processingDate ? \Carbon\Carbon::parse($processingDate) : $this->processing_date,
            'shipped_date' => $shippedDate ? \Carbon\Carbon::parse($shippedDate) : $this->shipped_date,
            'delivered_date' => $deliveredDate ? \Carbon\Carbon::parse($deliveredDate) : $this->delivered_date,
        ];

        // Ensure dates are not invalid early years (0026 vs 2026)
        foreach(['processing_date', 'shipped_date', 'delivered_date'] as $dateField) {
            if ($updates[$dateField] && $updates[$dateField]->year < 1000) {
                $updates[$dateField] = $updates[$dateField]->addYears(2000);
            }
        }

        // Auto-fill logic
        if ($status === 'processing' && !$updates['processing_date']) $updates['processing_date'] = now();
        if ($status === 'shipped') {
            if (!$updates['processing_date']) $updates['processing_date'] = now();
            if (!$updates['shipped_date']) $updates['shipped_date'] = now();
        }
        if ($status === 'delivered') {
            if (!$updates['processing_date']) $updates['processing_date'] = now();
            if (!$updates['shipped_date']) $updates['shipped_date'] = now();
            if (!$updates['delivered_date']) $updates['delivered_date'] = now();
        }

        $this->update($updates);

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
