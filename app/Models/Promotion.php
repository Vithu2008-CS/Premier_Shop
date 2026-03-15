<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'type',
        'start_date',
        'end_date',
        'is_active',
        'order_priority',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

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

    public function scopeSliders($query)
    {
        return $query->where('type', 'slider');
    }

    public function scopeBanners($query)
    {
        return $query->where('type', 'banner');
    }
}
