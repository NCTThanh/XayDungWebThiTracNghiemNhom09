@extends('layouts.admin')

@section('title', 'Kết quả và Thống kê')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kết quả và Thống kê</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
            <p class="text-blue-100 text-sm font-semibold mb-2">Tổng đề thi</p>
            <p class="text-4xl font-bold">{{ $quizzes->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
            <p class="text-green-100 text-sm font-semibold mb-2">Tổng lượt thi</p>
            <p class="text-4xl font-bold">{{ \App\Models\ExamAttempt::where('status', 'completed')->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white">
            <p class="text-yellow-100 text-sm font-semibold mb-2">Sinh viên tham gia</p>
            <p class="text-4xl font-bold">{{ \App\Models\ExamAttempt::where('status', 'completed')->distinct('user_id')->count('user_id') }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <p class="text-purple-100 text-sm font-semibold mb-2">Điểm TB chung</p>
            <p class="text-4xl font-bold">{{ number_format(\App\Models\ExamAttempt::where('status', 'completed')->avg('score'), 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-8 py-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Các đề thi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Tên đề thi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Câu hỏi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Lượt thi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Vượt qua</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Tỷ lệ</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Điểm TB</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($quizzes as $quiz)
                    @php
                        $attempts = \App\Models\ExamAttempt::where('quiz_id', $quiz->id)
                            ->where('status', 'completed')
                            ->get();
                        $passedCount = $attempts->where('score', '>=', $quiz->pass_score ?? 5.0)->count();
                        $totalAttempts = $attempts->count();
                        $passRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 1) : 0;
                        $avgScore = $attempts->avg('score') ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $quiz->title }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $quiz->questions_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $totalAttempts }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-green-700 font-semibold">{{ $passedCount }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $passRate }}%"></div>
                                </div>
                                <span class="text-sm font-semibold">{{ $passRate }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg">{{ number_format($avgScore, 2) }}/10</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.quiz.results', $quiz->id) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-block px-3 py-1 bg-indigo-50 rounded">
                                <i class="fas fa-chart-bar"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            Chưa có đề thi nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
