<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Order status counts
        $orderCounts = [
            'processing' => $user->orders()->where('status', 'processing')->count(),
            'shipped'    => $user->orders()->where('status', 'shipped')->count(),
            'delivered'  => $user->orders()->where('status', 'delivered')->count(),
            'cancelled'  => $user->orders()->where('status', 'cancelled')->count(),
        ];

        // Recent orders
        $recentOrders = $user->orders()->latest()->take(5)->get();

        // Wishlist count
        $wishlistCount = $user->wishlists()->count();

        // Total orders
        $totalOrders = $user->orders()->count();

        return view('profile.edit', compact(
            'user',
            'orderCounts',
            'recentOrders',
            'wishlistCount',
            'totalOrders'
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
