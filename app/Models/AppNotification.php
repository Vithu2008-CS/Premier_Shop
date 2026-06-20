<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * In-app notification record delivered to individual users.
 *
 * Notifications are created via static factory methods rather than directly
 * calling create() — this keeps the business logic for each event type
 * co-located and prevents scattered raw inserts across controllers.
 *
 * Notification types and their audience:
 *   order_status   → customer (their own order changed state)
 *   new_order      → all staff/admin (new purchase arrived)
 *   low_stock      → all staff/admin (product fell below 10 units)
 *   back_in_stock  → customers who wishlisted the product
 *   new_return     → all staff/admin (customer submitted a return)
 *   return_status  → customer (their return was approved/rejected/refunded)
 *
 * read_at null = unread; set via markAsRead().
 */
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',   // Bootstrap Icon class, e.g. 'bi-truck'
        'url',    // deep-link the notification bell item targets
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Read state ───────────────────────────────────────────────────────────

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    // ── Static factory methods ───────────────────────────────────────────────

    /** Notify the order owner when their order status changes. */
    public static function notifyOrderStatus(Order $order): void
    {
        $statusMessages = [
            'processing' => 'Your order is now being processed!',
            'shipped' => 'Your order has been shipped and is on its way!',
            'delivered' => 'Your order has been delivered. Enjoy!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $statusIcons = [
            'processing' => 'bi-box-seam',
            'shipped' => 'bi-truck',
            'delivered' => 'bi-check-circle-fill',
            'cancelled' => 'bi-x-circle',
        ];

        $message = $statusMessages[$order->status] ?? "Order status updated to {$order->status}.";
        $icon = $statusIcons[$order->status] ?? 'bi-bell';

        self::create([
            'user_id' => $order->user_id,
            'type' => 'order_status',
            'title' => "Order #{$order->order_number}",
            'message' => $message,
            'icon' => $icon,
            'url' => route('orders.show', $order),
        ]);
    }

    /** Notify all staff/admin users when a new order is placed. */
    public static function notifyNewOrder(Order $order): void
    {
        $staffUsers = User::whereHas('role', fn ($q) => $q->where('is_staff', true))->get();

        foreach ($staffUsers as $user) {
            self::create([
                'user_id' => $user->id,
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "Order #{$order->order_number} placed by {$order->user->name} — £".number_format($order->total, 2),
                'icon' => 'bi-bag-plus-fill',
                'url' => route('admin.orders.show', $order),
            ]);
        }
    }

    /** Notify all staff/admin when a product stock drops below 10 units. */
    public static function notifyLowStock(Product $product): void
    {
        $staffUsers = User::whereHas('role', fn ($q) => $q->where('is_staff', true))->get();

        foreach ($staffUsers as $user) {
            self::create([
                'user_id' => $user->id,
                'type' => 'low_stock',
                'title' => 'Low Stock Alert',
                'message' => "{$product->name} has only {$product->stock} units left!",
                'icon' => 'bi-exclamation-triangle-fill',
                'url' => route('admin.products.edit', $product),
            ]);
        }
    }

    /** Notify all users who wishlisted a product when it comes back in stock. */
    public static function notifyBackInStock(Product $product): void
    {
        $wishlistUserIds = UserItem::where('product_id', $product->id)
            ->where('type', 'wishlist')
            ->pluck('user_id');

        foreach ($wishlistUserIds as $userId) {
            self::create([
                'user_id' => $userId,
                'type' => 'back_in_stock',
                'title' => 'Back in Stock!',
                'message' => "{$product->name} is back in stock. Don't miss out!",
                'icon' => 'bi-bag-check-fill',
                'url' => route('products.show', $product->slug),
            ]);
        }
    }

    /** Notify all staff/admin when a customer submits a return request. */
    public static function notifyNewReturnRequest(ReturnRequest $return): void
    {
        $staffUsers = User::whereHas('role', fn ($q) => $q->where('is_staff', true))->get();

        foreach ($staffUsers as $user) {
            self::create([
                'user_id' => $user->id,
                'type' => 'new_return',
                'title' => 'New Return Request',
                'message' => "Return request submitted for Order #{$return->order->order_number} by {$return->user->name}.",
                'icon' => 'bi-arrow-return-left',
                'url' => route('admin.returns.show', $return),
            ]);
        }
    }

    /** Notify the customer when their return request status is updated by admin. */
    public static function notifyReturnStatus(ReturnRequest $return): void
    {
        $statusMessages = [
            'approved' => 'Your return request has been approved!',
            'rejected' => 'Your return request has been rejected.',
            'refunded' => 'Your refund of £'.number_format((float) $return->refund_amount, 2).' has been processed.',
        ];

        $statusIcons = [
            'approved' => 'bi-check-circle',
            'rejected' => 'bi-x-circle',
            'refunded' => 'bi-cash',
        ];

        // No notification for intermediate/unknown statuses
        if (! isset($statusMessages[$return->status])) {
            return;
        }

        self::create([
            'user_id' => $return->user_id,
            'type' => 'return_status',
            'title' => "Return #{$return->id} Updated",
            'message' => $statusMessages[$return->status],
            'icon' => $statusIcons[$return->status] ?? 'bi-info-circle',
            'url' => route('returns.show', $return),
        ]);
    }
}
