@extends('emails.layouts.master')
@section('title', 'Order Status Update')
@section('header_subtitle', 'Update on Order #' . $order->order_number)

@section('content')
<tr>
    <td style="padding:24px 40px 0;text-align:center;">
        <h2 style="margin:0;font-size:22px;color:#2d3436;font-weight:700;">Hi {{ $order->user->name }},</h2>
        <p style="margin:12px 0 0;font-size:15px;color:#636e72;line-height:1.6;">
            The status of your order <strong>#{{ $order->order_number }}</strong> has been updated to:
        </p>
    </td>
</tr>

<tr>
    <td style="padding:30px 40px;text-align:center;">
        <div style="display:inline-block;background:linear-gradient(135deg,#00B894,#00CEC9);color:#fff;padding:10px 24px;border-radius:50px;font-size:18px;font-weight:700;text-transform:capitalize;">
            {{ str_replace('_', ' ', $order->status) }}
        </div>
    </td>
</tr>

<tr>
    <td style="padding:0 40px 20px;">
        <div style="background:#f8f9fa;border-radius:12px;padding:20px;border-left:4px solid #6C5CE7;text-align:left;">
            <p style="margin:0;font-size:14px;color:#2d3436;line-height:1.8;">
                @if($order->status === 'processing' && $order->processing_date)
                    <strong>Processing Started:</strong> {{ $order->processing_date->format('M d, Y h:i A') }}<br>
                @endif
                @if($order->status === 'shipped' && $order->shipped_date)
                    <strong>Shipped On:</strong> {{ $order->shipped_date->format('M d, Y h:i A') }}<br>
                @endif
                @if($order->status === 'delivered' && $order->delivered_date)
                    <strong>Delivered On:</strong> {{ $order->delivered_date->format('M d, Y h:i A') }}<br>
                @endif
                @if($order->status === 'cancelled' && $order->cancellation_reason)
                    <strong>Cancellation Reason:</strong> {{ $order->cancellation_reason }}<br>
                @endif
            </p>
        </div>
    </td>
</tr>

<tr>
    <td style="padding:20px 40px;text-align:center;border-top:1px dashed #eee;">
        <div style="background:#fff;padding:15px;border-radius:12px;display:inline-block;border:1px solid #eee;">
            <img src="{{ $order->qr_code_url }}" alt="Scan to verify order" width="120" height="120" style="display:block;margin:0 auto;">
            <p style="margin:10px 0 0;font-size:12px;color:#636e72;font-weight:600;">Scan to verify status online</p>
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
