<?php

/**
 * EmailVerificationPromptController — Show "please verify your email" page.
 * Already-verified users are redirected to dashboard immediately.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('admin.dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
