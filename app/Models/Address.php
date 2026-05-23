<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A saved delivery address belonging to a user.
 *
 * Users can store multiple addresses; exactly one can be flagged is_default.
 * setAsDefault() enforces the single-default invariant by clearing other
 * defaults before setting this one — done in two queries rather than a
 * conditional update to avoid race conditions.
 *
 * The formatted accessor builds a single display string for dropdowns and
 * confirmation pages, omitting any null parts.
 */
class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',        // friendly name e.g. "Home", "Work"
        'address_line',
        'city',
        'postcode',
        'phone',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Business logic ───────────────────────────────────────────────────────

    /**
     * Mark this address as the user's default.
     * Clears is_default on all other addresses for this user first.
     */
    public function setAsDefault(): void
    {
        self::where('user_id', $this->user_id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Comma-separated address string, omitting any blank parts. */
    public function getFormattedAttribute(): string
    {
        $parts = array_filter([$this->address_line, $this->city, $this->postcode]);

        return implode(', ', $parts);
    }
}
