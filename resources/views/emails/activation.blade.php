<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Xác thực tài khoản</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác thực tài khoản</h1>
        </div>
        <div class="content">
            <p>Xin chào {{ $user->name }},</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản. Vui lòng nhấn nút bên dưới để xác thực email của bạn:</p>
            <center>
                <a href="{{ $url }}" class="button">Xác thực Email</a>
            </center>
            <p>Hoặc sao chép liên kết này vào trình duyệt:</p>
            <p><code>{{ $url }}</code></p>
            <p style="color: #999; font-size: 12px;">Liên kết này sẽ hết hạn trong 24 giờ.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Bảo lưu tất cả quyền.</p>
        </div>
    </div>
</body>
</html>
