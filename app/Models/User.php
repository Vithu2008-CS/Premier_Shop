<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'phone',
        'address',
        'city',
        'role_id',
        'is_on_duty',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role && $this->role->name === 'driver';
    }

    public function isStaff(): bool
    {
        return $this->role && $this->role->is_staff;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->role) return false;
        if ($this->role->name === 'admin') return true; // Admin has all permissions
        return $this->role->hasPermission($permission);
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->dob)->age;
    }

    public function isUnder16(): bool
    {
        return $this->age < 16;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }
}
