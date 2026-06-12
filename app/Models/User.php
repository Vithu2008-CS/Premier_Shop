<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * The application user model — covers customers, admins, staff, and drivers.
 * Role-based access is handled by the Role / Permission models (not Gates/Policies).
 *
 * Prunable: unverified accounts older than 1 day are deleted by the scheduler
 * so abandoned registrations don't pollute the users table.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, Prunable;

    /**
     * Delete unverified accounts that are more than 1 day old.
     * Run automatically by `php artisan model:prune`.
     */
    public function prunable()
    {
        return static::whereNull('email_verified_at')->where('created_at', '<=', now()->subDay());
    }

    protected $fillable = [
        'name', 'email', 'password',
        'dob', 'phone', 'address', 'city',
        'role_id', 'is_on_duty', 'loyalty_points', 'profile_photo',
        'offer_discount_percentage', 'offer_scope', 'offer_product_ids',
        'latitude', 'longitude', 'location_updated_at',
        'cart_reminder_sent_at',
    ];

    protected $hidden = [
        'password',       // never serialise the hash
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'dob'               => 'date',
            'loyalty_points'    => 'integer',
            'is_on_duty'        => 'boolean',  // driver duty status
            'offer_product_ids' => 'array',
            'latitude'             => 'float',
            'longitude'            => 'float',
            'location_updated_at'  => 'datetime',
            'cart_reminder_sent_at' => 'datetime',
        ];
    }

    // ── Profile Photo Helpers ────────────────────────────────────────────────

    /** Get the user's profile photo URL or default letter-based avatar. */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6C5CE7&color=fff&size=128&font-size=0.33';
    }

    // ── Role helpers ─────────────────────────────────────────────────────────

    /** The user's role record (admin / customer / driver / staff / custom). */
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

    /** Staff = any role with is_staff = true (admin, manager, etc.). */
    public function isStaff(): bool
    {
        return $this->role && $this->role->is_staff;
    }

    /**
     * Check if the user has a specific named permission.
     * Admins implicitly have every permission without a DB lookup.
     */
    public function hasPermission(string $permission): bool
    {
        if (! $this->role) {
            return false;
        }

        // Admin role bypasses all individual permission checks
        if ($this->role->name === 'admin') {
            return true;
        }

        return $this->role->hasPermission($permission);
    }

    // ── Age / age restriction ────────────────────────────────────────────────

    /** Calculate age from DOB. Returns null when DOB is not set. */
    public function getAgeAttribute(): ?int
    {
        return $this->dob ? Carbon::parse($this->dob)->age : null;
    }

    /**
     * Returns true when the user's verified age is under 16.
     * Null DOB (age unknown) returns false — only restrict when we're certain.
     */
    public function isUnder16(): bool
    {
        $age = $this->age;

        return $age !== null && $age < 16;
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /** Cart items (UserItem rows with type = 'cart'). */
    public function cartItems()
    {
        return $this->hasMany(UserItem::class)->cart();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** Wishlist items (UserItem rows with type = 'wishlist'). */
    public function wishlists()
    {
        return $this->hasMany(UserItem::class)->wishlist();
    }

    /** Per-request memo for wishlistedProductIds(). */
    private ?array $wishlistIdsMemo = null;

    /**
     * IDs of every product on this user's wishlist, loaded once per request.
     * Product cards render in grids of 12+; checking against this array costs
     * one query total instead of one EXISTS query per card.
     */
    public function wishlistedProductIds(): array
    {
        // Cast defensively: some PDO configs return integer columns as strings
        return $this->wishlistIdsMemo ??= $this->wishlists()->pluck('product_id')->map(fn ($id) => (int) $id)->all();
    }

    /** Orders where this user is the assigned delivery driver. */
    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    /** Recently viewed products ordered by most recent first. */
    public function recentlyViewed()
    {
        return $this->hasMany(RecentlyViewed::class)->orderByDesc('viewed_at');
    }

    /** All in-app notifications, newest first. */
    public function notifications()
    {
        return $this->hasMany(AppNotification::class)->orderByDesc('created_at');
    }

    /** Subset of notifications that have not been read yet. */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    /** Loyalty point transaction history, newest first. */
    public function rewardPointTransactions()
    {
        return $this->hasMany(RewardPointTransaction::class)->orderByDesc('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
