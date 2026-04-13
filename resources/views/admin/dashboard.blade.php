@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Hệ thống Quản trị (Analytics)</h1>
        <p class="text-gray-500 mt-1">Tổng quan hoạt động hệ thống</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">TỔNG SINH VIÊN</p>
                    <p class="text-4xl font-bold text-indigo-600 mt-1">{{ $userCount ?? 0 }}</p>
                </div>
                <i class="fas fa-users text-5xl text-indigo-100"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">SỐ ĐỀ THI</p>
                    <p class="text-4xl font-bold text-emerald-600 mt-1">{{ $quizCount ?? 0 }}</p>
                </div>
                <i class="fas fa-file-alt text-5xl text-emerald-100"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">ĐIỂM TRUNG BÌNH</p>
                    <p class="text-4xl font-bold text-amber-600 mt-1">{{ number_format($avgScore ?? 0, 2) }}</p>
                </div>
                <i class="fas fa-chart-bar text-5xl text-amber-100"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gray-500">TỶ LỆ HOÀN THÀNH</p>
                    <p class="text-4xl font-bold text-purple-600 mt-1">87%</p>
                </div>
                <i class="fas fa-percent text-5xl text-purple-100"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Phân bố điểm số (Hệ 10)</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Lượt làm bài 7 ngày qua</h3>
            <div class="relative h-[300px] w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tránh khởi tạo nhiều lần
        if (window.chartsInitialized) return;
        window.chartsInitialized = true;

        // Biểu đồ Phân bố điểm
        new Chart(document.getElementById('scoreChart'), {
            type: 'bar',
            data: {
                labels: ['0-2', '2-4', '4-6', '6-8', '8-10'],
                datasets: [{
                    label: 'Số sinh viên',
                    data: [8, 18, 45, 95, 72],
                    backgroundColor: ['#ef4444','#f59e0b','#eab308','#22c55e','#3b82f6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Biểu đồ Hoạt động
        new Chart(document.getElementById('activityChart'), {
            type: 'line',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [{
                    label: 'Lượt nộp bài',
                    data: [42, 68, 75, 92, 65, 88, 79],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.15)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection