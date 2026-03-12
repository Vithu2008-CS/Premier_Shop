<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::with('role')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders' => function ($q) {
            $q->latest()->limit(10);
        }, 'role']);

        $roles = Role::all();

        return view('admin.customers.show', compact('customer', 'roles'));
    }

    public function updateRole(Request $request, User $customer)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Prevent removing admin role from yourself
        if ($customer->id === auth()->id() && $customer->isAdmin()) {
            $newRole = Role::find($request->role_id);
            if ($newRole->name !== 'admin') {
                return back()->with('error', 'You cannot remove your own admin role.');
            }
        }

        $customer->update(['role_id' => $request->role_id]);
        $roleName = Role::find($request->role_id)->display_name;

        return back()->with('success', "Role updated to '{$roleName}' successfully.");
    }

    public function destroy(User $customer)
    {
        if ($customer->isAdmin()) {
            return back()->with('error', 'Cannot delete admin users.');
        }
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
