<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust only proxies listed in TRUSTED_PROXIES (comma-separated IPs/CIDRs).
        // Trusting '*' would let clients spoof X-Forwarded-For and bypass
        // per-IP rate limits (e.g. the login throttle).
        $trustedProxies = env('TRUSTED_PROXIES', '');
        if ($trustedProxies === '*') {
            $middleware->trustProxies(at: '*');
        } elseif ($trustedProxies !== '') {
            $middleware->trustProxies(at: array_filter(array_map('trim', explode(',', $trustedProxies))));
        }
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // Global rate-limit safety net: throttle every web route with the `web`
        // limiter (300/min per user|IP, defined in AppServiceProvider). This
        // guarantees rate limiting on ALL endpoints — including otherwise
        // unthrottled public pages (home, catalogue, product detail) — while
        // tighter per-route limiters (throttle:login, throttle:checkout, …)
        // still stack on top for sensitive actions. Runs after StartSession so
        // authenticated requests are keyed by user id. (OWASP API4:2023.)
        $middleware->appendToGroup('web', 'throttle:web');

        // Stripe posts webhooks without a CSRF token; it is authenticated by its
        // signature header instead (verified in StripeWebhookController).
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'driver' => \App\Http\Middleware\DriverMiddleware::class,
            'audit.admin' => \App\Http\Middleware\AuditAdminActions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
