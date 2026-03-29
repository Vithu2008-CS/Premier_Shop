<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set this address as default and unset others.
     */
    public function setAsDefault(): void
    {
        self::where('user_id', $this->user_id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    /**
     * Get formatted address as a single string.
     */
    public function getFormattedAttribute(): string
    {
        $parts = array_filter([$this->address_line, $this->city, $this->postcode]);
        return implode(', ', $parts);
    }
}
