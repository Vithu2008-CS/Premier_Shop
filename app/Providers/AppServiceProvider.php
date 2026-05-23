<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Application bootstrapping — runs on every request.
 *
 * Responsibilities:
 *   1. Force Bootstrap 5 pagination (useBootstrapFive) so paginator links
 *      render with Bootstrap classes instead of Tailwind.
 *
 *   2. View composers — inject shared data into specific layouts:
 *      - layouts.app         → $globalCategories for the storefront nav dropdown
 *      - layouts.admin_noble → $notificationData (pending orders count + recent
 *        orders/customers) for the admin sidebar live counters
 *
 *   3. Password policy — enforces min 12 chars, upper+lower+number+symbol,
 *      and checks against the HaveIBeenPwned breach database (uncompromised()).
 *
 *   4. Rate limiters — defined here and referenced by throttle: middleware
 *      on individual routes in web.php / auth.php.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No custom bindings needed — Laravel's service container auto-resolves.
    }

    public function boot(): void
    {
        // Use Bootstrap 5 pagination link templates throughout the app
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Inject product categories into every storefront page nav
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $view->with('globalCategories', \App\Models\Category::withCount('products')->get());
        });

        // Inject live admin counters into the admin layout (sidebar / topbar)
        \Illuminate\Support\Facades\View::composer('layouts.admin_noble', function ($view) {
            $view->with('notificationData', [
                'pendingOrdersCount' => \App\Models\Order::where('status', 'pending')->count(),
                'recentOrders'       => \App\Models\Order::with('user')->latest()->limit(3)->get(),
                'recentCustomers'    => \App\Models\User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
                    ->latest()->limit(3)->get(),
            ]);
        });

        // Global password strength rules (applied to all Password::defaults() usages)
        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(); // checks HaveIBeenPwned API
        });

        $this->configureRateLimiting();
    }

    /**
     * Register named rate limiters referenced via throttle:<name> middleware.
     *
     *  api      — 60 req/min per authenticated user or IP (general API use)
     *  login    — 5 req/min per IP (login, register, OTP, password reset)
     *  uploads  — 10 req/min per user/IP (file upload endpoints)
     *  checkout — 10 req/min per user/IP (coupon, shipping calc, process order)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            // Disable limiting in automated tests to avoid false throttle failures
            return app()->runningUnitTests()
                ? Limit::none()
                : Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
