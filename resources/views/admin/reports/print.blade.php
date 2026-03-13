<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ now()->format('M d, Y') }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 30px; }
        .header { border-bottom: 3px solid #6c5ce7; padding-bottom: 20px; margin-bottom: 30px; }
        .header:after { content: ""; display: table; clear: both; }
        .logo { float: left; font-size: 24px; font-weight: bold; color: #6c5ce7; text-transform: uppercase; }
        .report-info { float: right; text-align: right; }
        .stats-grid { margin-bottom: 30px; }
        .stats-grid:after { content: ""; display: table; clear: both; }
        .stat-card { float: left; width: 30%; margin-right: 3%; background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #6c5ce7; }
        .stat-label { font-size: 12px; color: #777; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 13px; }
        th { background-color: #f1f2f6; border-bottom: 2px solid #dee2e6; padding: 12px; text-align: left; color: #555; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { padding: 3px 7px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #e9ecef; color: #495057; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ \App\Models\Setting::get('shop_name', 'PREMIER SHOP') }}</div>
            <div class="report-info">
                <div style="font-weight: bold; font-size: 18px;">SALES REPORT</div>
                <div>Generated: {{ now()->format('M d, Y H:i') }}</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ number_format($products->count()) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Units Sold</div>
                <div class="stat-value">{{ number_format($products->sum('total_sold')) }}</div>
            </div>
            <div class="stat-card" style="margin-right: 0; border-left-color: #00b894;">
                <div class="stat-label">Estimated Revenue</div>
                <div class="stat-value">£{{ number_format($products->sum(fn($p) => $p->price * $p->total_sold), 2) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>Category</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Units Sold</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $product->name }}</div>
                        <div style="font-size: 11px; color: #777;">Barcode: {{ $product->barcode ?? 'N/A' }}</div>
                    </td>
                    <td><span class="badge">{{ $product->category->name ?? 'N/A' }}</span></td>
                    <td class="text-right">£{{ number_format($product->price, 2) }}</td>
                    <td class="text-right">{{ $product->stock }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($product->total_sold ?? 0) }}</td>
                    <td class="text-right">£{{ number_format($product->price * ($product->total_sold ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="4">GRAND TOTAL</td>
                    <td class="text-right">{{ number_format($products->sum('total_sold')) }}</td>
                    <td class="text-right">£{{ number_format($products->sum(fn($p) => $p->price * $p->total_sold), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Confidential Business Report - {{ \App\Models\Setting::get('shop_name', 'PREMIER SHOP') }}</p>
            <p>&copy; {{ date('Y') }} All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
