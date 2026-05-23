<?php

/**
 * AppLayout — Blade component wrapper for the storefront layout (layouts.app).
 * Used by Breeze-generated auth views that reference <x-app-layout>.
 */

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
