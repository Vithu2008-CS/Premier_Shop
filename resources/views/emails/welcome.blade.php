<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Premier Shop</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f0f2f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header with Gradient --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#00B894 0%,#00CEC9 50%,#6C5CE7 100%);padding:50px 40px;text-align:center;">
                            <h1 style="margin:0;font-size:32px;color:#ffffff;font-weight:700;">🎉 Welcome!</h1>
                            <p style="margin:12px 0 0;font-size:16px;color:rgba(255,255,255,0.9);">Your account has been created successfully</p>
                        </td>
                    </tr>

                    {{-- Confetti/Success Badge --}}
                    <tr>
                        <td style="padding:30px 40px 0;text-align:center;">
                            <div style="display:inline-block;width:80px;height:80px;background:linear-gradient(135deg,#00B894,#00CEC9);border-radius:50%;line-height:80px;font-size:40px;">
                                ✅
                            </div>
                        </td>
                    </tr>

                    {{-- Welcome Message --}}
                    <tr>
                        <td style="padding:24px 40px 0;text-align:center;">
                            <h2 style="margin:0;font-size:24px;color:#2d3436;font-weight:700;">Hello, {{ $user->name }}!</h2>
                            <p style="margin:12px 0 0;font-size:15px;color:#636e72;line-height:1.7;">
                                Thank you for joining Premier Shop! We're thrilled to have you as part of our community. Get ready to discover amazing products at unbeatable prices.
                            </p>
                        </td>
                    </tr>

                    {{-- What You Can Do --}}
                    <tr>
                        <td style="padding:30px 40px;">
                            <h3 style="margin:0 0 16px;font-size:16px;color:#2d3436;font-weight:700;text-align:center;">🚀 What You Can Do Now</h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:12px;background:#f8f9fa;border-radius:12px;margin-bottom:8px;">
                                        <table cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;font-size:22px;vertical-align:top;">🛒</td>
                                                <td style="padding-left:8px;">
                                                    <strong style="color:#2d3436;font-size:14px;">Browse & Shop</strong>
                                                    <p style="margin:4px 0 0;font-size:13px;color:#636e72;">Explore our wide range of quality products across multiple categories.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td style="height:8px;"></td></tr>
                                <tr>
                                    <td style="padding:12px;background:#f8f9fa;border-radius:12px;">
                                        <table cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;font-size:22px;vertical-align:top;">🏷️</td>
                                                <td style="padding-left:8px;">
                                                    <strong style="color:#2d3436;font-size:14px;">Exclusive Deals</strong>
                                                    <p style="margin:4px 0 0;font-size:13px;color:#636e72;">Get access to special promotions, coupon codes, and member-only discounts.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td style="height:8px;"></td></tr>
                                <tr>
                                    <td style="padding:12px;background:#f8f9fa;border-radius:12px;">
                                        <table cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="width:40px;font-size:22px;vertical-align:top;">🚚</td>
                                                <td style="padding-left:8px;">
                                                    <strong style="color:#2d3436;font-size:14px;">Free Delivery</strong>
                                                    <p style="margin:4px 0 0;font-size:13px;color:#636e72;">Enjoy free delivery on orders over £50 within 10 miles.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Account Details --}}
                    <tr>
                        <td style="padding:0 40px 20px;">
                            <div style="background:#f8f9fa;border-radius:12px;padding:20px;border-left:4px solid #6C5CE7;">
                                <h4 style="margin:0 0 8px;font-size:14px;color:#6C5CE7;font-weight:700;">YOUR ACCOUNT DETAILS</h4>
                                <p style="margin:0;font-size:14px;color:#2d3436;line-height:1.8;">
                                    <strong>Name:</strong> {{ $user->name }}<br>
                                    <strong>Email:</strong> {{ $user->email }}<br>
                                    <strong>Member since:</strong> {{ $user->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- CTA Button --}}
                    <tr>
                        <td style="padding:10px 40px 30px;text-align:center;">
                            <a href="{{ url('/products') }}" style="display:inline-block;background:linear-gradient(135deg,#6C5CE7,#00CEC9);color:#ffffff;padding:14px 40px;border-radius:50px;text-decoration:none;font-size:16px;font-weight:700;letter-spacing:0.5px;">
                                Start Shopping →
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#2d3436;padding:30px 40px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:16px;color:#fff;font-weight:700;">🛍️ Premier Shop</p>
                            <p style="margin:0 0 12px;font-size:13px;color:rgba(255,255,255,0.6);">Your one-stop destination for quality products</p>
                            <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.4);">
                                London, UK &nbsp;|&nbsp; info@premiershop.com &nbsp;|&nbsp; +44 770 000 0000
                            </p>
                            <hr style="border:none;border-top:1px solid rgba(255,255,255,0.1);margin:16px 0;">
                            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.35);">
                                © {{ date('Y') }} Premier Shop. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
