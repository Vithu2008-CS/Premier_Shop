<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: auto; padding: 40px; }
        .header { border-bottom: 3px solid #3498db; padding-bottom: 25px; margin-bottom: 40px; }
        .header:after { content: ""; display: table; clear: both; }
        .logo { float: left; font-size: 32px; font-weight: bold; color: #3498db; text-transform: uppercase; letter-spacing: 1px; }
        .shop-info { float: right; text-align: right; font-size: 13px; color: #555; }
        .shop-info strong { color: #333; font-size: 15px; }

        .invoice-details { margin-bottom: 40px; }
        .invoice-details:after { content: ""; display: table; clear: both; }
        .invoice-details .bill-to { float: left; width: 45%; }
        .invoice-details .order-info { float: right; width: 45%; text-align: right; }
        
        .section-title { font-size: 12px; text-transform: uppercase; color: #3498db; margin-bottom: 8px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 3px; display: inline-block; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 15px 12px; text-align: left; font-size: 13px; color: #555; text-transform: uppercase; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .text-right { text-align: right; }

        .summary-container:after { content: ""; display: table; clear: both; }
        .qr-section { float: left; width: 40%; padding-top: 10px; }
        .qr-section p { font-size: 11px; color: #888; margin-top: 5px; }
        .summary { float: right; width: 45%; }
        .summary-row { margin-bottom: 10px; }
        .summary-row:after { content: ""; display: table; clear: both; }
        .summary-label { float: left; color: #777; font-size: 14px; }
        .summary-value { float: right; font-weight: bold; font-size: 14px; }
        .total-row { border-top: 2px solid #3498db; margin-top: 15px; padding-top: 15px; font-size: 20px; color: #3498db; }

        .footer { margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 30px; }
        .footer h3 { color: #3498db; margin-bottom: 5px; font-size: 18px; }
        .footer p { color: #888; font-size: 12px; margin-top: 0; }

        .badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-pending { background-color: #ffeaa7; color: #d35400; }
        .badge-processing { background-color: #81ecec; color: #0097e6; }
        .badge-shipped { background-color: #a29bfe; color: #6c5ce7; }
        .badge-delivered { background-color: #55efc4; color: #00b894; }
        .badge-cancelled { background-color: #ff7675; color: #d63031; }

        .watermark { position: fixed; top: 40%; left: 25%; transform: rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.03); font-weight: bold; z-index: -1; }
    </style>
</head>
<body>
    <div class="watermark">PAID COMPLETED</div>
    
    <div class="container">
        <div class="header">
            <div class="logo">{{ \App\Models\Setting::get('shop_name', 'PREMIER SHOP') }}</div>
            <div class="shop-info">
                <strong>OFFICIAL RECEIPT</strong><br>
                {{ \App\Models\Setting::get('shop_address', '123 Retail Lane') }}<br>
                {{ \App\Models\Setting::get('shop_city', 'London, UK') }}<br>
                Phone: {{ \App\Models\Setting::get('shop_phone', '+44 123 456 789') }}
            </div>
        </div>

        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title">Customer Information</div>
                <div style="font-size: 17px; font-weight: bold; color: #333; margin-bottom: 5px;">{{ $order->user->name }}</div>
                <div style="color: #555;">{{ $order->shipping_address['address_line'] }}</div>
                <div style="color: #555;">{{ $order->shipping_address['city'] }}</div>
                <div style="color: #555; margin-top: 5px;"><small>Contact:</small> {{ $order->shipping_address['phone'] }}</div>
            </div>
            <div class="order-info">
                <div class="section-title">Invoice Details</div>
                <div style="margin-bottom: 5px;"><strong>Receipt #:</strong> <span style="color: #3498db;">{{ $order->order_number }}</span></div>
                <div style="margin-bottom: 5px;"><strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}</div>
                <div><strong>Status:</strong> 
                    <span class="badge badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item Description</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold; color: #333;">{{ $item->product->name }}</div>
                        <div style="font-size: 11px; color: #888; margin-top: 2px;">SKU: {{ $item->product->barcode ?? 'N/A' }}</div>
                    </td>
                    <td class="text-right">£{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right" style="font-weight: bold;">£{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-container">
            <div class="qr-section">
                <img src="{{ $order->qr_code_url }}" alt="QR Code" style="width: 100px; height: 100px; border: 1px solid #eee; padding: 5px;">
                <p>Scan to verify order status online</p>
            </div>
            
            <div class="summary">
                <div class="summary-row">
                    <div class="summary-label">Items Subtotal</div>
                    <div class="summary-value">£{{ number_format($order->subtotal, 2) }}</div>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="summary-row" style="color: #00b894;">
                    <div class="summary-label">Coupon Discount ({{ $order->coupon_code }})</div>
                    <div class="summary-value">-£{{ number_format($order->discount_amount, 2) }}</div>
                </div>
                @endif
                
                <div class="summary-row">
                    <div class="summary-label">Shipping & Handling</div>
                    <div class="summary-value">£{{ number_format($order->shipping_cost, 2) }}</div>
                </div>
                
                @if($order->distance)
                <div class="summary-row">
                    <div class="summary-label" style="font-size: 11px;">Delivery Distance</div>
                    <div class="summary-value" style="font-size: 11px;">{{ number_format($order->distance, 1) }} km</div>
                </div>
                @endif

                <div class="summary-row total-row">
                    <div class="summary-label">Grand Total</div>
                    <div class="summary-value">£{{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <h3>Thank You for Your Business!</h3>
            <p>If you have any questions about this receipt, please contact our customer support.</p>
            <div style="margin-top: 20px; color: #bbb;">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('shop_name', 'PREMIER SHOP') }}
            </div>
        </div>
    </div>
</body>
</html>
