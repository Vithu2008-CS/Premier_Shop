<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .footer { margin-top: 20px; font-size: 0.8em; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        {!! $mailMessage !!}
        
        <div class="footer">
            <p>Sent from {{ config('app.name') }} Admin Panel</p>
        </div>
    </div>
</body>
</html>
