<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Persists per-user product view history for the "Recently Viewed" widget.
 *
 * Capped at 10 records per user — older entries are pruned automatically by track().
 * $timestamps = false because we manage our own viewed_at column rather than
 * using Laravel's created_at / updated_at pair.
 *
 * Guest recently-viewed history is handled separately via session in ProductController.
 */
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

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Static helpers ───────────────────────────────────────────────────────

    /**
     * Record a product view for a logged-in user.
     * Uses updateOrCreate so revisiting a product just bumps the timestamp
     * rather than creating a duplicate row. After upsert, deletes any
     * records beyond the 10 most recent.
     *
     * The take(100) in the prune query is a MariaDB workaround:
     * DELETE with OFFSET is not directly supported, so we SELECT the IDs first.
     */
    public static function track(int $userId, int $productId): void
    {
        self::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['viewed_at' => now()]
        );

        // Collect IDs beyond the 10 most recent and delete them
        $oldest = self::where('user_id', $userId)
            ->orderByDesc('viewed_at')
            ->skip(10)
            ->take(100) // MariaDB requires LIMIT with OFFSET in subqueries
            ->get(['id'])
            ->pluck('id');

        if ($oldest->isNotEmpty()) {
            self::whereIn('id', $oldest)->delete();
        }
    }

    /**
     * Fetch active, age-appropriate recently viewed products for a user.
     * Filters out deleted (null) products and age-restricted items
     * when the viewing user is under 16.
     */
    public static function getForUser(int $userId, int $limit = 10)
    {
        $isUnder16 = auth()->check() && auth()->user()->isUnder16();

        return self::where('user_id', $userId)
            ->with('product')
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter()  // drop nulls (product was deleted after being viewed)
            ->filter(fn ($p) => $p->is_active && (! $isUnder16 || ! $p->is_age_restricted));
    }
}
