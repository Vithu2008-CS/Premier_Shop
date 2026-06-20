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
        if (app()->environment('production') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Use Bootstrap 5 pagination link templates throughout the app
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Inject product categories into every storefront page nav.
        // Eager-load a slim set of active products (used by the mega-menu) to
        // avoid an N+1 query per category on every page load.
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $view->with('globalCategories', \App\Models\Category::withCount('products')
                ->with(['products' => function ($q) {
                    $q->where('is_active', true)
                        ->select('id', 'category_id', 'name', 'slug')
                        ->latest('id');
                }])
                ->get());
        });

        // Inject live admin counters into the admin layout (sidebar / topbar)
        \Illuminate\Support\Facades\View::composer('layouts.admin_noble', function ($view) {
            $view->with('notificationData', [
                'pendingOrdersCount' => \App\Models\Order::where('status', 'pending')->count(),
                'recentOrders' => \App\Models\Order::with('user')->latest()->limit(3)->get(),
                'recentCustomers' => \App\Models\User::whereHas('role', fn ($q) => $q->where('name', 'customer'))
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
     *  web      — 300 req/min per user/IP. Global safety net appended to the
     *             whole `web` middleware group in bootstrap/app.php, so EVERY
     *             public/auth route is covered even when it has no per-route
     *             throttle (OWASP API4:2023 — Unrestricted Resource Consumption).
     *  api      — 60 req/min per authenticated user or IP (general API use)
     *  login    — 5 req/min per IP (login, register, OTP, password reset)
     *  uploads  — 10 req/min per user/IP (file upload endpoints)
     *  checkout — 10 req/min per user/IP (coupon, shipping calc, process order)
     *
     * Keying strategy: authenticated requests are limited per user id (so one
     * abusive account can't exhaust a shared-office IP's budget); anonymous
     * requests fall back to client IP (resolved from the real client only when
     * behind a TRUSTED_PROXIES proxy — see bootstrap/app.php).
     *
     * All limiters that gate normal browsing are disabled under unit tests via
     * Limit::none() so the suite can fire many requests without false 429s.
     */
    protected function configureRateLimiting(): void
    {
        // Generous global ceiling: a heavy human browsing session is well under
        // this (~30–60 req/min), 30s notification polling is ~2/min, so genuine
        // users are never affected while scripted floods are capped. Tune here
        // if a high-traffic shared NAT/proxy legitimately exceeds it.
        RateLimiter::for('web', function (Request $request) {
            return app()->runningUnitTests()
                ? Limit::none()
                : Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });

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
