<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Enforces a specific named permission on a route.
 *
 * Usage in routes: ->middleware('permission:manage_products')
 *
 * Delegates to User::hasPermission() which:
 *   - Returns true for admin role unconditionally
 *   - Queries the role_permission pivot for all other roles
 *
 * Registered as 'permission' alias in bootstrap/app.php middleware aliases.
 */
class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Unauthenticated users or users without the required permission are blocked
        if (! auth()->check() || ! auth()->user()->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
