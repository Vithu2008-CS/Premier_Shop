<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Premier Shop Email')</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f0f2f5;padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="@yield('header_style', 'background:linear-gradient(135deg,#6C5CE7 0%,#00CEC9 100%);') padding:40px 40px 30px; text-align:center;">
                            <h1 style="margin:0;font-size:28px;color:#ffffff;font-weight:700;">🛍️ Premier Shop</h1>
                            @hasSection('header_subtitle')
                                <p style="margin:10px 0 0;font-size:14px;color:rgba(255,255,255,0.85);">@yield('header_subtitle')</p>
                            @endif
                        </td>
                    </tr>

                    {{-- Main Content Section --}}
                    @yield('content')

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
                                © {{ date('Y') }} Premier Shop. All rights reserved.<br>
                                @yield('footer_note', 'This is an automated email. Please do not reply directly.')
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
