<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A named capability that can be granted to roles (e.g. 'manage_products').
 *
 * Permissions are grouped by the group column for display in the admin
 * role editor (e.g. 'Products', 'Orders', 'Customers').
 *
 * Roles are checked via Role::hasPermission() / User::hasPermission().
 * Admin role bypasses all permission checks — never needs entries here.
 */
class Permission extends Model
{
    protected $fillable = ['name', 'display_name', 'group'];

    /** All roles that have been granted this permission. */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
