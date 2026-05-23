<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to driver-only routes (delivery dashboard, duty toggle, etc.).
 *
 * Uses User::isDriver() which checks role->name === 'driver'.
 * Registered as 'driver' alias in bootstrap/app.php middleware aliases.
 */
class DriverMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only authenticated users with the driver role may proceed
        if (auth()->check() && auth()->user()->isDriver()) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Drivers only.');
    }
}
