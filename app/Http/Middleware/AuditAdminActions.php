<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records an AuditLog row for every state-changing admin request.
 *
 * Only non-GET methods are logged (POST/PUT/PATCH/DELETE) — read-only page
 * views are not part of the audit trail. The request payload is stored after
 * stripping sensitive fields and uploaded file objects, and is truncated so a
 * single huge request cannot bloat the table. Logging failures are swallowed:
 * the audit trail must never take down the admin action itself.
 */
class AuditAdminActions
{
    /** Request keys whose values must never be persisted. */
    private const REDACTED_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
        '_method',
    ];

    private const MAX_VALUE_LENGTH = 1000;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        try {
            AuditLog::create([
                'user_id' => $request->user()?->id,
                'action' => $request->route()?->getName()
                    ?: $request->method().' '.$request->path(),
                'method' => $request->method(),
                'url' => substr($request->fullUrl(), 0, 2048),
                'payload' => $this->sanitize($request->input()),
                'status' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Audit log write failed: '.$e->getMessage());
        }

        return $response;
    }

    /** Redact sensitive keys and truncate long values, recursively. */
    private function sanitize(array $input): array
    {
        $clean = [];
        foreach ($input as $key => $value) {
            if (in_array(strtolower((string) $key), self::REDACTED_KEYS, true)) {
                $clean[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
            } elseif (is_string($value) && strlen($value) > self::MAX_VALUE_LENGTH) {
                $clean[$key] = substr($value, 0, self::MAX_VALUE_LENGTH).'…[truncated]';
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}
