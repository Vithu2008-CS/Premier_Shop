<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleStatus(User $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        $status = $customer->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Customer {$status} successfully.");
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
