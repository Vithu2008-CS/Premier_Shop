<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f4f4f4; padding: 20px; color: #333; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #6C5CE7; padding-bottom: 15px; }
        .header h1 { color: #6C5CE7; margin: 0; }
        .content { font-size: 16px; line-height: 1.6; }
        .order-details { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #6C5CE7; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Premier Shop</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name }},</p>
            <p>The status of your order <strong>#{{ $order->order_number }}</strong> has been updated.</p>
            
            <div class="order-details">
                <p><strong>New Status:</strong> <span style="text-transform: capitalize; color: #E17055; font-weight: bold;">{{ $order->status }}</span></p>
                @if($order->status === 'processing' && $order->processing_date)
                    <p><strong>Processing Started:</strong> {{ $order->processing_date->format('M d, Y h:i A') }}</p>
                @endif
                @if($order->status === 'shipped' && $order->shipped_date)
                    <p><strong>Shipped On:</strong> {{ $order->shipped_date->format('M d, Y h:i A') }}</p>
                @endif
                @if($order->status === 'delivered' && $order->delivered_date)
                    <p><strong>Delivered On:</strong> {{ $order->delivered_date->format('M d, Y h:i A') }}</p>
                @endif
                @if($order->status === 'cancelled' && $order->cancellation_reason)
                    <p><strong>Cancellation Reason:</strong> {{ $order->cancellation_reason }}</p>
                @endif
            </div>
            
            <p>If you have any questions or concerns, please feel free to contact us.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('orders.show', $order) }}" class="btn">View Order Details</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Premier Shop. All rights reserved.
        </div>
    </div>
</body>
</html>
