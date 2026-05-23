<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attaches security-related HTTP response headers to every response.
 *
 * Key headers and their purpose:
 *   X-Frame-Options: SAMEORIGIN        — blocks clickjacking via iframes
 *   X-Content-Type-Options: nosniff    — prevents MIME-type sniffing attacks
 *   X-XSS-Protection                   — legacy XSS filter (modern browsers use CSP)
 *   Referrer-Policy                    — limits referrer leakage to same-origin
 *   Permissions-Policy                 — grants/denies browser feature access
 *   Strict-Transport-Security          — forces HTTPS (only set on secure connections)
 *   Content-Security-Policy            — whitelists allowed resource origins
 *
 * Permissions-Policy notes:
 *   camera=(self)   — allows camera access for the QR scanner at /admin/scanner.
 *                     Using camera=() here would block the camera at the HTTP level
 *                     even when the user grants permission in browser settings.
 *   fullscreen=(self) — allows fullscreen for video/media players on this origin.
 *   All other sensitive APIs (geolocation, microphone, payment, etc.) are disabled.
 *
 * CSP notes:
 *   unsafe-inline / unsafe-eval are required by the current inline-script-heavy
 *   blade templates. Tightening this would require moving scripts to external files.
 *   http://127.0.0.1:5173 entries support local Vite dev server hot reload.
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), midi=(), sync-xhr=(), microphone=(), camera=(self), magnetometer=(), gyroscope=(), fullscreen=(self), payment=()');

        // HSTS only makes sense over a real HTTPS connection
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com http://127.0.0.1:5173; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com http://127.0.0.1:5173; font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https: http://127.0.0.1:5173; connect-src 'self' http://127.0.0.1:5173 ws://127.0.0.1:5173; frame-ancestors 'none';");

        return $response;
    }
}
