<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Homepage promotional content — covers both hero sliders and banners.
 *
 * The type column distinguishes display context:
 *   'slider' — full-width hero carousel on the homepage
 *   'banner' — smaller promotional banner panels
 *
 * Date-bounded visibility: start_date / end_date can be null (= always active).
 * order_priority controls display order within each type.
 *
 * Scopes:
 *   active()  — filters to is_active=true and within the date window
 *   sliders() — further filters to type='slider'
 *   banners() — further filters to type='banner'
 */
class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_path',
        'link_url',
        'button_text',
        'text_align',
        'type',           // 'slider' or 'banner'
        'start_date',
        'end_date',
        'is_active',
        'order_priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Filter to promotions that are active and within their scheduled date window.
     * Null start/end dates mean "no boundary on that side."
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', now());
            });
    }

    /** Filter to hero slider promotions. */
    public function scopeSliders($query)
    {
        return $query->where('type', 'slider');
    }

    /** Filter to banner promotions. */
    public function scopeBanners($query)
    {
        return $query->where('type', 'banner');
    }
}
