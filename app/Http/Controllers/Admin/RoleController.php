<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * Manages user roles and their associated permission sets.
 * System roles ('admin', 'customer', 'driver') are protected from deletion.
 */
class RoleController extends Controller
{
    /** List all roles with user and permission counts. */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();

        return view('admin.roles.index', compact('roles'));
    }

    /** Show the create-role form with all permissions grouped by category. */
    public function create()
    {
        // Group permissions by their 'group' field for a cleaner checkbox UI
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.roles.create', compact('permissions'));
    }

    /** Validate, create a new role, and sync its permissions. */
    public function store(Request $request)
    {
        $request->validate([
            // name must be lowercase_snake — used as a machine identifier in code
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_staff' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Privilege-escalation guard: a staff role grants admin-panel access, so only
        // an administrator may create one. Non-admin staff (even with roles.create)
        // are limited to non-staff roles.
        if ($request->boolean('is_staff') && ! auth()->user()->isAdmin()) {
            abort(403, 'Only an administrator may create staff roles.');
        }

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'is_staff' => $request->boolean('is_staff'),
        ]);

        // sync() replaces the pivot rows in one query
        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->display_name}' created successfully.");
    }

    /** Show the edit form pre-populated with the role's current permissions. */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        // Array of permission IDs currently attached — used to pre-check checkboxes
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /** Validate, update a role's display fields, and sync permissions. */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_staff' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Privilege-escalation guard: only an administrator may modify a staff role,
        // grant staff access, or alter the built-in admin role's permission set.
        if (($role->name === 'admin' || $role->is_staff || $request->boolean('is_staff')) && ! auth()->user()->isAdmin()) {
            abort(403, 'Only an administrator may modify staff roles.');
        }

        $role->update([
            'display_name' => $request->display_name,
            'description' => $request->description,
            // Admin role must stay staff: AdminMiddleware gates the panel on
            // is_staff, so unchecking it here would lock every admin out.
            'is_staff' => $role->name === 'admin' ? true : $request->boolean('is_staff'),
        ]);

        // Pass empty array when no checkboxes submitted so all permissions are detached
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->display_name}' updated successfully.");
    }

    /**
     * Delete a role.
     * Blocked if: role is a system role ('admin'/'customer'/'driver'), or users are still assigned to it.
     */
    public function destroy(Role $role)
    {
        // Protect built-in roles that core app logic depends on (driver is
        // load-bearing for DriverMiddleware and driver account creation)
        if (in_array($role->name, ['admin', 'customer', 'driver'])) {
            return back()->with('error', 'Cannot delete system roles.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role that has users assigned to it. Reassign users first.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
