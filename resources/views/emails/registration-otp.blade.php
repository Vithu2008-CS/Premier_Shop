<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f0f2f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#6C5CE7 0%,#A29BFE 50%,#00CEC9 100%);padding:40px;text-align:center;">
                            <h1 style="margin:0;font-size:28px;color:#ffffff;font-weight:700;">🛍️ Premier Shop</h1>
                            <p style="margin:10px 0 0;font-size:14px;color:rgba(255,255,255,0.85);">Email Verification</p>
                        </td>
                    </tr>

                    {{-- Lock Icon Badge --}}
                    <tr>
                        <td style="padding:30px 40px 0;text-align:center;">
                            <div style="display:inline-block;width:70px;height:70px;background:linear-gradient(135deg,#6C5CE7,#A29BFE);border-radius:50%;line-height:70px;font-size:32px;">
                                🔐
                            </div>
                        </td>
                    </tr>

                    {{-- Message --}}
                    <tr>
                        <td style="padding:24px 40px 0;text-align:center;">
                            <h2 style="margin:0;font-size:22px;color:#2d3436;font-weight:700;">Verify Your Email</h2>
                            <p style="margin:12px 0 0;font-size:15px;color:#636e72;line-height:1.6;">
                                Hi <strong>{{ $userName }}</strong>,<br>
                                Please use the verification code below to complete your registration.
                            </p>
                        </td>
                    </tr>

                    {{-- OTP Code --}}
                    <tr>
                        <td style="padding:30px 40px;text-align:center;">
                            <div style="background:linear-gradient(135deg,#f8f9fa,#e9ecef);border:2px dashed #6C5CE7;border-radius:16px;padding:24px;display:inline-block;">
                                <span style="font-size:42px;font-weight:800;letter-spacing:12px;color:#6C5CE7;font-family:'Courier New',monospace;">{{ $otp }}</span>
                            </div>
                            <p style="margin:16px 0 0;font-size:13px;color:#b2bec3;">
                                ⏱️ This code expires in <strong>10 minutes</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Security Notice --}}
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <div style="background:#fff3cd;border-radius:12px;padding:16px 20px;border-left:4px solid #ffc107;">
                                <p style="margin:0;font-size:13px;color:#856404;line-height:1.5;">
                                    ⚠️ <strong>Security Notice:</strong> Never share this code with anyone. Premier Shop will never ask for your verification code via phone or social media.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#2d3436;padding:30px 40px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:16px;color:#fff;font-weight:700;">🛍️ Premier Shop</p>
                            <p style="margin:0 0 12px;font-size:13px;color:rgba(255,255,255,0.6);">Your one-stop destination for quality products</p>
                            <hr style="border:none;border-top:1px solid rgba(255,255,255,0.1);margin:16px 0;">
                            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.35);">
                                © {{ date('Y') }} Premier Shop. All rights reserved.<br>
                                If you didn't request this code, please ignore this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
