<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Restricts access to staff-only routes (admin panel, reports, etc.).
 *
 * Uses User::isStaff() which checks role->is_staff = true, covering
 * admin, manager, and any other role designated as staff.
 * Registered as 'admin' alias in bootstrap/app.php middleware aliases.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Unauthenticated or non-staff users get a hard 403
        if (! auth()->check() || ! auth()->user()->isStaff()) {
            abort(403, 'Access denied. Staff only.');
        }

        return $next($request);
    }
}
