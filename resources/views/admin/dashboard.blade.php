@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Hệ thống Quản trị (Analytics)</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Tổng sinh viên</div>
            <div class="text-3xl font-bold text-gray-800">{{ $userCount }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Số lượng đề thi</div>
            <div class="text-3xl font-bold text-gray-800">{{ $quizCount }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Điểm trung bình</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($avgScore, 2) }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
            <div class="text-sm font-medium text-gray-500 uppercase">Tỷ lệ hoàn thành</div>
            <div class="text-3xl font-bold text-gray-800">85%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Phân bố điểm số (Hệ 10)</h3>
            <canvas id="scoreChart" height="250"></canvas>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">Lượt làm bài trong 7 ngày qua</h3>
            <canvas id="activityChart" height="250"></canvas>
        </div>
    </div>
</div>

<script>
    // 1. Dữ liệu giả lập cho Phân bố điểm (Bạn có thể truyền từ Controller)
    const scoreCtx = document.getElementById('scoreChart').getContext('2d');
    new Chart(scoreCtx, {
        type: 'bar',
        data: {
            labels: ['0-2', '2-4', '4-6', '6-8', '8-10'],
            datasets: [{
                label: 'Số lượng sinh viên',
                data: [5, 12, 45, 120, 38], // Số liệu thực tế lấy từ DB
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. Dữ liệu cho Hoạt động thi (Line Chart)
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
            datasets: [{
                label: 'Lượt nộp bài',
                data: [65, 59, 80, 81, 56, 40, 30],
                fill: true,
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderColor: 'rgba(16, 185, 129, 1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection