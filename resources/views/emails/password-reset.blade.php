<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .button { display: inline-block; background: #f5576c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Đặt lại mật khẩu</h1>
        </div>
        <div class="content">
            <p>Xin chào {{ $user->name }},</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu của bạn. Nhấn nút bên dưới để tiếp tục:</p>
            <center>
                <a href="{{ $url }}" class="button">Đặt lại mật khẩu</a>
            </center>
            <p>Hoặc sao chép liên kết này vào trình duyệt:</p>
            <p><code>{{ $url }}</code></p>
            <p style="color: #999; font-size: 12px;">Liên kết này sẽ hết hạn trong 1 giờ.</p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Bảo lưu tất cả quyền.</p>
        </div>
    </div>
</body>
</html>
