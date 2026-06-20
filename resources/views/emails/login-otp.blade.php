{{--
    emails/login-otp.blade.php — second-factor sign-in code
    ========================================================
    Sent by the LoginOtp mailable during the MFA login challenge.
    Variables: $otp (6-digit code), $userName.
--}}
@extends('emails.layouts.master')
@section('title', 'Your Sign-In Code')
@section('header_style', 'background:#743089;')
@section('header_subtitle', 'Two-Factor Sign-In')
@section('footer_note', "If you didn't try to sign in, change your password immediately — someone may have it.")

@section('content')
<tr>
    <td style="padding:30px 40px 0;text-align:center;">
        <div style="display:inline-block;width:70px;height:70px;background:#743089;border-radius:50%;line-height:70px;font-size:32px;">
            🔐
        </div>
    </td>
</tr>

<tr>
    <td style="padding:24px 40px 0;text-align:center;">
        <h2 style="margin:0;font-size:22px;color:#2d3436;font-weight:700;">Confirm It's You</h2>
        <p style="margin:12px 0 0;font-size:15px;color:#636e72;line-height:1.6;">
            Hi <strong>{{ $userName }}</strong>,<br>
            Enter the code below to finish signing in to your Premier Shop account.
        </p>
    </td>
</tr>

<tr>
    <td style="padding:30px 40px;text-align:center;">
        <div style="background:#f8f9fa;border:2px dashed #743089;border-radius:16px;padding:24px;display:inline-block;">
            <span style="font-size:42px;font-weight:800;letter-spacing:12px;color:#743089;font-family:'Courier New',monospace;">{{ $otp }}</span>
        </div>
        <p style="margin:16px 0 0;font-size:13px;color:#b2bec3;">
            ⏱️ This code expires in <strong>10 minutes</strong>
        </p>
    </td>
</tr>

<tr>
    <td style="padding:0 40px 30px;">
        <div style="background:#fff3cd;border-radius:12px;padding:16px 20px;border-left:4px solid #ffc107;text-align:left;">
            <p style="margin:0;font-size:13px;color:#856404;line-height:1.5;">
                ⚠️ <strong>Security Notice:</strong> Never share this code with anyone. Premier Shop will never ask for your sign-in code via phone, email reply, or social media.
            </p>
        </div>
    </td>
</tr>
@endsection
