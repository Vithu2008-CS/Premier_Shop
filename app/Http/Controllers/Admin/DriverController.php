<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = User::whereHas('role', function($q) {
            $q->where('name', 'driver');
        })->withCount(['assignedOrders as processing_orders_count' => function($q) {
            $q->whereIn('status', ['pending', 'processing', 'shipped']);
        }])->latest()->get();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $driverRole = Role::where('name', 'driver')->firstOrFail();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'dob' => $request->dob,
            'address' => $request->address,
            'role_id' => $driverRole->id,
        ]);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    public function edit(User $driver)
    {
        if (!$driver->isDriver()) {
            abort(404);
        }
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, User $driver)
    {
        if (!$driver->isDriver()) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$driver->id],
            'phone' => ['required', 'string', 'max:20'],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(User $driver)
    {
        if (!$driver->isDriver()) {
            abort(404);
        }

        if ($driver->assignedOrders()->whereIn('status', ['pending', 'processing', 'shipped'])->exists()) {
            return back()->with('error', 'Cannot delete driver with active orders.');
        }

        $driver->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }
}
