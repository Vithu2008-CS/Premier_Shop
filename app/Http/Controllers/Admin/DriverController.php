<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * Admin management of delivery driver accounts.
 * Drivers are regular User records whose role.name === 'driver'.
 * The controller guards all mutations with an isDriver() check to prevent
 * acting on non-driver users who happen to share the same route parameter.
 */
class DriverController extends Controller
{
    /**
     * List all driver users with a count of their currently active orders
     * (pending / processing / shipped) so admin can spot overloaded drivers.
     */
    public function index()
    {
        $drivers = User::whereHas('role', fn ($q) => $q->where('name', 'driver'))
            ->withCount(['assignedOrders as processing_orders_count' => fn ($q) =>
                $q->whereIn('status', ['pending', 'processing', 'shipped'])
            ])
            ->latest()
            ->get();

        return view('admin.drivers.index', compact('drivers'));
    }

    /** Show the create-driver form. */
    public function create()
    {
        return view('admin.drivers.create');
    }

    /** Validate and create a new driver user, assigning the 'driver' role automatically. */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'    => ['required', 'string', 'max:20'],
            'dob'      => ['required', 'date', 'before:today'],
            'address'  => ['nullable', 'string', 'max:500'],
        ]);

        // Lookup the driver role — fail hard if it doesn't exist (seeder not run)
        $driverRole = Role::where('name', 'driver')->firstOrFail();

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'dob'      => $request->dob,
            'address'  => $request->address,
            'role_id'  => $driverRole->id,
        ]);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver created successfully.');
    }

    /** Show the edit form for a driver. 404s if the user is not a driver. */
    public function edit(User $driver)
    {
        if (! $driver->isDriver()) {
            abort(404);
        }

        return view('admin.drivers.edit', compact('driver'));
    }

    /**
     * Update a driver's profile.
     * Password field is optional — only hashed and saved when a new value is submitted.
     */
    public function update(Request $request, User $driver)
    {
        if (! $driver->isDriver()) {
            abort(404);
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            // Unique rule ignores the driver's own row
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$driver->id],
            'phone'    => ['required', 'string', 'max:20'],
            'dob'      => ['required', 'date', 'before:today'],
            'address'  => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'dob'     => $request->dob,
            'address' => $request->address,
        ];

        // Only update password when explicitly provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully.');
    }

    /**
     * Delete a driver account.
     * Blocked when the driver has active (undelivered) orders to prevent orphaned assignments.
     */
    public function destroy(User $driver)
    {
        if (! $driver->isDriver()) {
            abort(404);
        }

        if ($driver->assignedOrders()->whereIn('status', ['pending', 'processing', 'shipped'])->exists()) {
            return back()->with('error', 'Cannot delete driver with active orders.');
        }

        $driver->delete();

        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted successfully.');
    }

    public function getLocation(User $driver)
    {
        if (! $driver->isDriver()) {
            return response()->json(['error' => 'Not a driver'], 404);
        }

        $locationAgeSeconds = $driver->location_updated_at
            ? (int) now()->diffInSeconds($driver->location_updated_at)
            : null;

        return response()->json([
            'driver_id'            => $driver->id,
            'driver_name'          => $driver->name,
            'latitude'             => $driver->latitude,
            'longitude'            => $driver->longitude,
            'is_on_duty'           => (bool) $driver->is_on_duty,
            'location_updated_at'  => $driver->location_updated_at?->toIso8601String(),
            'location_age_seconds' => $locationAgeSeconds,
            'active_orders_count'  => $driver->assignedOrders()
                ->whereIn('status', ['pending', 'processing', 'shipped'])
                ->count(),
        ]);
    }
}
