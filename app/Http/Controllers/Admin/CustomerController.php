<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Admin management of customer accounts: listing, profile view,
 * role reassignment, and deletion.
 */
class CustomerController extends Controller
{
    /** List all users with the 'customer' role, paginated, with order count. */
    public function index()
    {
        $customers = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
            ->with('role')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show a customer's profile with their 10 most recent orders.
     * Also passes all roles so admin can reassign via the form on this page.
     */
    public function show(User $customer)
    {
        $customer->load([
            'orders' => fn ($q) => $q->latest()->limit(10),
            'role',
        ]);

        $roles = Role::all();

        return view('admin.customers.show', compact('customer', 'roles'));
    }

    /**
     * Change a user's role.
     * Guards against admins accidentally removing their own admin access.
     */
    public function updateRole(Request $request, User $customer)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);

        // Prevent the currently-logged-in admin from demoting themselves
        if ($customer->id === auth()->id() && $customer->isAdmin()) {
            $newRole = Role::find($request->role_id);
            if ($newRole->name !== 'admin') {
                return back()->with('error', 'You cannot remove your own admin role.');
            }
        }

        $customer->role_id = $request->role_id;
        $customer->save();

        $roleName = Role::find($request->role_id)->display_name;

        return back()->with('success', "Role updated to '{$roleName}' successfully.");
    }

    /** Delete a customer account. Admins cannot be deleted via this action. */
    public function destroy(User $customer)
    {
        if ($customer->isAdmin()) {
            return back()->with('error', 'Cannot delete admin users.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
