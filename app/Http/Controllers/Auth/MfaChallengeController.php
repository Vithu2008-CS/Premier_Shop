<?php

/**
 * MfaChallengeController — second-factor (email OTP) step of login.
 *
 * Reached only with a pending-MFA session set by AuthenticatedSessionController
 * after a correct password. The user is NOT authenticated here; the pending
 * user id lives in the session under 'mfa:pending_user'. A correct code logs
 * them in for real (Auth::login + session regeneration); a missing/expired
 * pending session bounces back to /login.
 *
 *   show()   — render the code-entry page (masked email for context)
 *   store()  — verify the code (rate-limited 5/min/IP), then complete login
 *   resend() — issue a fresh code to the same pending user
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class MfaChallengeController extends Controller
{
    public function __construct(private MfaService $mfa) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login')->with('error', 'Your sign-in session expired. Please log in again.');
        }

        return view('auth.mfa-challenge', ['maskedEmail' => $this->maskEmail($user->email)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login')->with('error', 'Your sign-in session expired. Please log in again.');
        }

        $throttleKey = 'mfa-challenge|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->with('error', "Too many attempts. Please try again in {$seconds} seconds.");
        }

        if (! $this->mfa->verify($user, (string) $request->input('otp'))) {
            RateLimiter::hit($throttleKey);

            return back()->with('error', 'Invalid or expired code. Please try again.');
        }

        RateLimiter::clear($throttleKey);

        // Promote the pending session to a real authenticated session.
        $remember = (bool) $request->session()->pull('mfa:remember', false);
        $request->session()->forget('mfa:pending_user');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Signed in successfully.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login')->with('error', 'Your sign-in session expired. Please log in again.');
        }

        $this->mfa->sendCode($user);

        return back()->with('status', 'A new sign-in code has been sent to your email.');
    }

    /** Resolve the user parked mid-login, or null if the pending session is gone. */
    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('mfa:pending_user');

        return $id ? User::find($id) : null;
    }

    /** j***@example.com — enough to recognise, not enough to disclose. */
    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $keep = mb_substr($name, 0, 1);

        return $keep.str_repeat('*', max(1, mb_strlen($name) - 1)).'@'.$domain;
    }
}
