<?php

/**
 * AuthenticatedSessionController — Login / logout flow.
 * store(): validates credentials (rate-limited) then, when the account requires
 *   a second factor, parks the user id in the session and emails an OTP instead
 *   of logging in — MfaChallengeController completes the login. Accounts without
 *   MFA log in directly. Session regenerated on login to prevent session fixation.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, MfaService $mfa): RedirectResponse
    {
        $user = $request->validateCredentials();

        // Second factor required → don't log in yet. Park the user id, email a
        // one-time code, and hand off to the MFA challenge. 'remember' is kept
        // so it can be honoured once the code is confirmed.
        if ($user->requiresMfa()) {
            $request->session()->put('mfa:pending_user', $user->id);
            $request->session()->put('mfa:remember', $request->boolean('remember'));
            $mfa->sendCode($user);

            return redirect()->route('mfa.challenge');
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
