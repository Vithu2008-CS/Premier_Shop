<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\RegistrationOtp;
use App\Mail\WelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Step 1: Validate registration data, generate OTP, send to email
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dob' => ['required', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store registration data + OTP in session
        session([
            'registration_data' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'address' => $request->address,
            ],
            'registration_otp' => $otp,
            'registration_otp_expires' => now()->addMinutes(10),
        ]);

        // Send OTP email
        try {
            Mail::to($request->email)->send(new RegistrationOtp($otp, $request->name));
        } catch (\Exception $e) {
            // Log the error but don't block registration
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return redirect()->route('register.verify')
            ->with('success', 'A verification code has been sent to your email!');
    }

    /**
     * Show OTP verification page
     */
    public function showVerify(): View|RedirectResponse
    {
        if (!session('registration_data')) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }
        return view('auth.verify-otp');
    }

    /**
     * Step 2: Verify OTP and create the account
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $storedOtp = session('registration_otp');
        $expiresAt = session('registration_otp_expires');
        $regData = session('registration_data');

        if (!$storedOtp || !$regData) {
            return redirect()->route('register')
                ->with('error', 'Registration session expired. Please try again.');
        }

        // Check expiry
        if (now()->isAfter($expiresAt)) {
            session()->forget(['registration_data', 'registration_otp', 'registration_otp_expires']);
            return redirect()->route('register')
                ->with('error', 'Verification code has expired. Please register again.');
        }

        // Check OTP match
        if ($request->otp !== $storedOtp) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        // Create the user
        $user = User::create([
            'name' => $regData['name'],
            'email' => $regData['email'],
            'password' => Hash::make($regData['password']),
            'dob' => $regData['dob'],
            'phone' => $regData['phone'],
            'address' => $regData['address'],
            'role' => 'customer',
        ]);

        // Clear session
        session()->forget(['registration_data', 'registration_otp', 'registration_otp_expires']);

        event(new Registered($user));

        // Send welcome email
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect('/')->with('success', 'Account created successfully! A welcome email has been sent.');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(): RedirectResponse
    {
        $regData = session('registration_data');

        if (!$regData) {
            return redirect()->route('register')
                ->with('error', 'Registration session expired. Please try again.');
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'registration_otp' => $otp,
            'registration_otp_expires' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($regData['email'])->send(new RegistrationOtp($otp, $regData['name']));
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP email: ' . $e->getMessage());
        }

        return back()->with('success', 'A new verification code has been sent!');
    }
}
