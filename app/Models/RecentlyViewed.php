<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyViewed extends Model
{
    protected $table = 'recently_viewed';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Track a product view for a user. Keeps only 10 most recent.
     */
    public static function track(int $userId, int $productId): void
    {
        self::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['viewed_at' => now()]
        );

        // Keep only the 10 most recent
        $oldest = self::where('user_id', $userId)
            ->orderByDesc('viewed_at')
            ->skip(10)
            ->pluck('id');

        if ($oldest->isNotEmpty()) {
            self::whereIn('id', $oldest)->delete();
        }
    }

    /**
     * Get recently viewed products for a user.
     */
    public static function getForUser(int $userId, int $limit = 10)
    {
        return self::where('user_id', $userId)
            ->with('product')
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter() // Remove any null products (deleted)
            ->filter(fn($p) => $p->is_active);
    }
}
