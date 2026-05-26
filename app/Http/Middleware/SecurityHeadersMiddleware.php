<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attaches security-related HTTP response headers to every response.
 *
 * Key headers and their purpose:
 *   X-Frame-Options: SAMEORIGIN               — blocks clickjacking via iframes
 *   X-Content-Type-Options: nosniff           — prevents MIME-type sniffing attacks
 *   X-XSS-Protection                          — legacy XSS filter (modern browsers use CSP)
 *   Referrer-Policy                           — limits referrer leakage to same-origin
 *   Permissions-Policy                        — grants/denies browser feature access
 *   Strict-Transport-Security                 — forces HTTPS (only set on secure connections)
 *   Content-Security-Policy                   — whitelists allowed resource origins
 *   Cross-Origin-Opener-Policy                — isolates browsing context from opener attacks
 *   Cross-Origin-Resource-Policy              — restricts cross-origin resource reads
 *   X-Permitted-Cross-Domain-Policies         — blocks Flash/PDF cross-domain data reads
 *
 * CSP notes:
 *   unsafe-inline / unsafe-eval are required by the current inline-script-heavy
 *   blade templates. frame-src allows the Google Maps embed on the contact page.
 *   Tightening unsafe-inline would require moving scripts to external files.
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
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), midi=(), sync-xhr=(), microphone=(), camera=(self), magnetometer=(), gyroscope=(), fullscreen=(self), payment=(), usb=(), bluetooth=()'
        );

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        $response->headers->set(
            'Content-Security-Policy',
            implode(' ', [
                "default-src 'self';",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com http://127.0.0.1:5173;",
                "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com http://127.0.0.1:5173;",
                "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;",
                "img-src 'self' data: https: http://127.0.0.1:5173;",
                "frame-src 'self' https://maps.google.com https://www.google.com;",
                "connect-src 'self' http://127.0.0.1:5173 ws://127.0.0.1:5173;",
                "frame-ancestors 'self';",
                "base-uri 'self';",
                "form-action 'self';",
            ])
        );

        return $response;
    }
}
