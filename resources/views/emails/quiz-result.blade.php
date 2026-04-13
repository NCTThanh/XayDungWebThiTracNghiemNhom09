<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kết quả bài thi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .score-box { background: white; border-left: 4px solid #38ef7d; padding: 15px; margin: 20px 0; }
        .button { display: inline-block; background: #38ef7d; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Kết quả bài thi của bạn</h1>
        </div>
        <div class="content">
            <p>Xin chào {{ $user->name }},</p>
            <p>Bài thi của bạn đã hoàn thành. Dưới đây là kết quả chi tiết:</p>
            
            <div class="score-box">
                <h3>{{ $quiz->title }}</h3>
                <p><strong>Điểm:</strong> {{ round($result['score'], 2) }}/100</p>
                <p><strong>Trạng thái:</strong> 
                    <span style="color: {{ $result['is_passed'] ? '#38ef7d' : '#f5576c' }};">
                        {{ $result['is_passed'] ? '✓ Đạt' : '✗ Không đạt' }}
                    </span>
                </p>
                <p><strong>Ngày thi:</strong> {{ $result['created_at']->format('d/m/Y H:i') }}</p>
            </div>

            <center>
                <a href="{{ $result_link }}" class="button">Xem chi tiết</a>
            </center>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Bảo lưu tất cả quyền.</p>
        </div>
    </div>
</body>
</html>
