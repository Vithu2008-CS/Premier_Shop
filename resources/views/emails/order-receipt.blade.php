@extends('emails.layouts.master')
@section('title', 'Order Receipt')
@section('header_subtitle', 'Thank you for your order!')

@section('content')
<tr>
    <td style="padding:30px 40px 0;text-align:center;">
        <div style="display:inline-block;background:linear-gradient(135deg,#00B894,#00CEC9);color:#fff;padding:10px 24px;border-radius:50px;font-size:14px;font-weight:600;">
            ✅ Order Confirmed
        </div>
    </td>
</tr>
<tr>
    <td style="padding:24px 40px 0;">
        <p style="margin:0;font-size:16px;color:#2d3436;">Hi <strong>{{ $order->user->name }}</strong>,</p>
        <p style="margin:8px 0 0;font-size:14px;color:#636e72;line-height:1.6;">We've received your order and it's being processed. Here's your receipt:</p>
    </td>
</tr>
<tr>
    <td style="padding:20px 40px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8f9fa;border-radius:12px;padding:20px;">
            <tr>
                <td style="padding:16px 20px;">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="font-size:13px;color:#636e72;padding-bottom:4px;">Order Number</td>
                            <td align="right" style="font-size:13px;color:#636e72;padding-bottom:4px;">Date</td>
                        </tr>
                        <tr>
                            <td style="font-size:18px;font-weight:700;color:#6C5CE7;">{{ $order->order_number }}</td>
                            <td align="right" style="font-size:14px;color:#2d3436;font-weight:600;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="padding:0 40px;">
        <h3 style="margin:0 0 12px;font-size:16px;color:#2d3436;font-weight:700;">🛒 Items Ordered</h3>
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
            <tr style="background:#6C5CE7;">
                <td style="padding:10px 14px;color:#fff;font-size:12px;font-weight:600;border-radius:8px 0 0 0;text-transform:uppercase;">Product</td>
                <td align="center" style="padding:10px;color:#fff;font-size:12px;font-weight:600;text-transform:uppercase;">Qty</td>
                <td align="center" style="padding:10px;color:#fff;font-size:12px;font-weight:600;text-transform:uppercase;">Price</td>
                <td align="right" style="padding:10px 14px;color:#fff;font-size:12px;font-weight:600;border-radius:0 8px 0 0;text-transform:uppercase;">Total</td>
            </tr>
            @foreach($order->items as $item)
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:14px;font-size:14px;color:#2d3436;font-weight:600;">{{ $item->product->name }}</td>
                <td align="center" style="padding:14px;font-size:14px;color:#636e72;">{{ $item->quantity }}</td>
                <td align="center" style="padding:14px;font-size:14px;color:#636e72;">£{{ number_format($item->price, 2) }}</td>
                <td align="right" style="padding:14px;font-size:14px;color:#2d3436;font-weight:600;">£{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </td>
</tr>
<tr>
    <td style="padding:20px 40px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8f9fa;border-radius:12px;padding:4px;">
            <tr>
                <td style="padding:10px 20px;">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="padding:6px 0;font-size:14px;color:#636e72;">Subtotal</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#2d3436;">£{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td style="padding:6px 0;font-size:14px;color:#00B894;">Discount {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#00B894;font-weight:600;">-£{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding:6px 0;font-size:14px;color:#636e72;">Shipping</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#2d3436;">£{{ number_format($order->shipping_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-top:2px solid #ddd;padding:0;"></td>
                        </tr>
                        <tr>
                            <td style="padding:12px 0 6px;font-size:20px;font-weight:800;color:#2d3436;">Total</td>
                            <td align="right" style="padding:12px 0 6px;font-size:20px;font-weight:800;color:#6C5CE7;">£{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
@if($order->shipping_address)
<tr>
    <td style="padding:0 40px 20px;">
        <h3 style="margin:0 0 8px;font-size:16px;color:#2d3436;font-weight:700;">📦 Delivery Address</h3>
        <div style="background:#f8f9fa;border-radius:12px;padding:16px 20px;border-left:4px solid #6C5CE7;text-align:left;">
            <p style="margin:0;font-size:14px;color:#2d3436;line-height:1.7;">
                {{ $order->shipping_address['address_line'] ?? '' }}<br>
                {{ $order->shipping_address['city'] ?? '' }}<br>
                📞 {{ $order->shipping_address['phone'] ?? '' }}
            </p>
        </div>
    </td>
</tr>
@endif
<tr>
    <td style="padding:20px 40px;text-align:center;border-top:1px dashed #eee;">
        <div style="background:#fff;padding:15px;border-radius:12px;display:inline-block;border:1px solid #eee;">
            <img src="{{ $order->qr_code_url }}" alt="Scan to verify order" width="120" height="120" style="display:block;margin:0 auto;">
            <p style="margin:10px 0 0;font-size:12px;color:#636e72;font-weight:600;">Scan to verify order status online</p>
        </div>
    </td>
</tr>
<tr>
    <td style="padding:10px 40px 40px;text-align:center;">
        <a href="{{ url('/orders/' . $order->id) }}" style="display:inline-block;background:linear-gradient(135deg,#6C5CE7,#00CEC9);color:#ffffff;padding:14px 36px;border-radius:50px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.5px;">
            View Order Details →
        </a>
    </td>
</tr>
@endsection