<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('profile.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_default'] = $request->has('is_default');

        $address = Address::create($validated);

        if ($address->is_default) {
            $address->setAsDefault();
        } elseif (auth()->user()->addresses()->count() === 1) {
             // If it's the first address, make it default automatically
             $address->setAsDefault();
        }

        return back()->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');

        $address->update($validated);

        if ($address->is_default) {
            $address->setAsDefault();
        }

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->setAsDefault();

        return back()->with('success', 'Default address updated.');
    }
}
