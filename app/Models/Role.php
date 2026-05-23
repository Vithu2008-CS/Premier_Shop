<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * User role in the RBAC system (e.g. admin, customer, driver, staff).
 *
 * is_staff = true means the role grants access to admin/staff-facing areas.
 * Permissions are many-to-many via the role_permission pivot table.
 *
 * Admin role bypasses all individual permission checks — see User::hasPermission().
 * System roles (admin, customer, driver) are protected from deletion in
 * Admin\RoleController to prevent locking all staff out.
 */
class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'is_staff'];

    protected function casts(): array
    {
        return [
            'is_staff' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /** Permissions assigned to this role via the role_permission pivot. */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /** All users currently assigned this role. */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // ── Permission helpers ───────────────────────────────────────────────────

    /** Returns true when this role has the named permission assigned. */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Attach a named permission to this role if not already assigned.
     * No-op when the permission name doesn't exist or is already attached.
     */
    public function givePermission(string $permission): void
    {
        $perm = Permission::where('name', $permission)->first();
        if ($perm && ! $this->permissions()->where('permission_id', $perm->id)->exists()) {
            $this->permissions()->attach($perm->id);
        }
    }
}
