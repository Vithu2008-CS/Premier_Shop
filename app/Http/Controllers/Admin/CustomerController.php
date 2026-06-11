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
    /** List all users with the 'customer' role, paginated, with order count and total spent. */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $minOrders = $request->input('min_orders');
        $minSpent = $request->input('min_spent');

        $query = User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
            ->with('role')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn($q) => $q->where('status', '!=', 'cancelled')], 'total');

        $sortBy = $request->input('sort_by', 'newest');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($minOrders !== null && $minOrders !== '') {
            $query->has('orders', '>=', intval($minOrders));
        }

        if ($minSpent !== null && $minSpent !== '') {
            $query->where(function($q) use ($minSpent) {
                $q->where(fn($sub) => $sub->selectRaw('cast(coalesce(sum(total), 0) as float)')
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->where('orders.status', '!=', 'cancelled'),
                    '>=',
                    floatval($minSpent)
                );
            });
        }

        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'orders_desc':
                $query->orderBy('orders_count', 'desc');
                break;
            case 'orders_asc':
                $query->orderBy('orders_count', 'asc');
                break;
            case 'spent_desc':
                $query->orderBy('total_spent', 'desc');
                break;
            case 'spent_asc':
                $query->orderBy('total_spent', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search', 'minOrders', 'minSpent', 'sortBy'));
    }

    /**
     * Show a customer's profile with their purchased items, categories, and potential offer parameters.
     */
    public function show(Request $request, User $customer)
    {
        $customer->load([
            'orders' => fn ($q) => $q->latest()->limit(10),
            'role',
            'addresses',
        ]);

        $roles = Role::all();
        $categories = \App\Models\Category::orderBy('name')->get();
        $products = \App\Models\Product::orderBy('name')->get();

        // Query all purchased items belonging to this customer (excluding cancelled orders)
        $purchasedItemsQuery = \App\Models\OrderItem::whereHas('order', function ($q) use ($customer) {
            $q->where('user_id', $customer->id)->where('status', '!=', 'cancelled');
        })->with(['product.category', 'order']);

        $purchaseSort = $request->input('purchase_sort', 'newest');
        switch ($purchaseSort) {
            case 'qty_desc':
                $purchasedItemsQuery->orderBy('quantity', 'desc');
                break;
            case 'qty_asc':
                $purchasedItemsQuery->orderBy('quantity', 'asc');
                break;
            case 'total_desc':
                $purchasedItemsQuery->orderByRaw('quantity * price desc');
                break;
            case 'total_asc':
                $purchasedItemsQuery->orderByRaw('quantity * price asc');
                break;
            case 'oldest':
                $purchasedItemsQuery->oldest();
                break;
            case 'newest':
            default:
                $purchasedItemsQuery->latest();
                break;
        }

        $purchasedItems = $purchasedItemsQuery->paginate(10)->withQueryString();

        // Calculate total spent across all non-cancelled orders for display
        $totalSpent = (float) $customer->orders()->where('status', '!=', 'cancelled')->sum('total');

        return view('admin.customers.show', compact(
            'customer', 'roles', 'categories', 'products', 'purchasedItems', 'purchaseSort', 'totalSpent'
        ));
    }

    /**
     * Update the customer's role and personalized offer settings.
     */
    public function update(Request $request, User $customer)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'offer_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'offer_scope' => 'required_with:offer_discount_percentage|nullable|in:all,selected',
            'offer_product_ids' => 'required_if:offer_scope,selected|nullable|array',
            'offer_product_ids.*' => 'exists:products,id',
        ]);

        $targetRole = Role::find($request->role_id);

        // Privilege-escalation guard: only an administrator may grant a staff-level
        // or admin role. Without this, any non-admin staff member holding the
        // customers.update permission could promote themselves (or anyone) to admin.
        if ($targetRole && ($targetRole->is_staff || $targetRole->name === 'admin') && ! auth()->user()->isAdmin()) {
            abort(403, 'Only an administrator may assign staff or admin roles.');
        }

        // Prevent the currently-logged-in admin from demoting themselves
        if ($customer->id === auth()->id() && $customer->isAdmin()) {
            if (! $targetRole || $targetRole->name !== 'admin') {
                return back()->with('error', 'You cannot remove your own admin role.');
            }
        }

        $customer->role_id = $request->role_id;
        $customer->offer_discount_percentage = $request->offer_discount_percentage ?: null;
        $customer->offer_scope = $request->offer_discount_percentage ? $request->offer_scope : null;
        
        if ($request->offer_discount_percentage && $request->offer_scope === 'selected' && is_array($request->offer_product_ids)) {
            $customer->offer_product_ids = array_map('intval', $request->offer_product_ids);
        } else {
            $customer->offer_product_ids = null;
        }

        $customer->save();

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer profile and offer updated successfully.');
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
