<?php

/**
 * GuestLayout — Blade component wrapper for the guest/auth layout (layouts.guest).
 * Used by Breeze-generated auth views that reference <x-guest-layout>.
 */

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
