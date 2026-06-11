{{--
    emails/abandoned-cart.blade.php — Abandoned cart reminder
    ==========================================================
    Sent by AbandonedCartMail (cart:remind-abandoned command) when a cart has
    sat idle past the threshold. Lists the items and links back to the cart.
    Variables: $user, $items (cart items with product), $subtotal
--}}
@extends('emails.layouts.master')
@section('title', 'Your cart is waiting')
@section('header_style', 'background:#6C5CE7;')
@section('header_subtitle', 'You left some items behind')

@section('content')
<tr>
    <td style="padding:30px 40px 0;text-align:center;">
        <div style="display:inline-block;width:80px;height:80px;background:#6C5CE7;border-radius:50%;line-height:80px;font-size:40px;">
            🛒
        </div>
    </td>
</tr>

<tr>
    <td style="padding:24px 40px 0;text-align:center;">
        <h2 style="margin:0;font-size:24px;color:#2d3436;font-weight:700;">Hi {{ $user->name }}, your cart misses you!</h2>
        <p style="margin:12px 0 0;font-size:15px;color:#636e72;line-height:1.7;">
            You left {{ $items->count() }} {{ \Illuminate\Support\Str::plural('item', $items->count()) }} in your cart.
            They're still here — finish checking out before they sell out.
        </p>
    </td>
</tr>

<tr>
    <td style="padding:24px 40px 0;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
            @foreach($items as $item)
            <tr>
                <td style="padding:12px;background:#f8f9fa;border-radius:12px;">
                    <table cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td style="width:56px;vertical-align:top;">
                                <img src="{{ url($item->product->first_image) }}" alt="" width="48" height="48" style="border-radius:8px;object-fit:cover;background:#eee;">
                            </td>
                            <td style="padding-left:10px;vertical-align:middle;">
                                <strong style="color:#2d3436;font-size:14px;">{{ $item->product->name }}</strong>
                                <p style="margin:4px 0 0;font-size:13px;color:#636e72;">Qty: {{ $item->quantity }}</p>
                            </td>
                            <td style="text-align:right;vertical-align:middle;white-space:nowrap;">
                                <strong style="color:#6C5CE7;font-size:14px;">£{{ number_format($item->line_total, 2) }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td style="height:8px;"></td></tr>
            @endforeach
        </table>
    </td>
</tr>

<tr>
    <td style="padding:8px 40px 0;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="font-size:16px;color:#2d3436;font-weight:700;">Subtotal</td>
                <td style="text-align:right;font-size:18px;color:#6C5CE7;font-weight:800;">£{{ number_format($subtotal, 2) }}</td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td style="padding:24px 40px 30px;text-align:center;">
        <a href="{{ url('/cart') }}" style="display:inline-block;background:#6C5CE7;color:#ffffff;padding:14px 40px;border-radius:50px;text-decoration:none;font-size:16px;font-weight:700;letter-spacing:0.5px;">
            Complete Your Order →
        </a>
        <p style="margin:16px 0 0;font-size:12px;color:#b2bec3;">
            If you've already checked out, please ignore this email.
        </p>
    </td>
</tr>
@endsection
