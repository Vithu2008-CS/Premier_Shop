<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Customer product review with star rating, optional photos, and admin reply.
 *
 * Only approved reviews are surfaced on the storefront. Approval is manual
 * (admin toggles is_approved). Rating must be 1–5.
 * Photos stored as a JSON array of storage paths.
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'title',
        'comment',
        'is_approved',
        'photos',
        'admin_reply',  // optional staff response shown under the review
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'is_approved' => 'boolean',
            'photos'      => 'array',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Filters to reviews visible on the storefront (admin-approved only). */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
