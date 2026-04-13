@extends('layouts.admin')

@section('title', 'Kết quả thi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.quizzes') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow p-8 mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $quiz->title }}</h1>
        <p class="text-gray-600 mb-6">{{ $quiz->description ?? 'Không có mô tả' }}</p>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                <p class="text-blue-600 text-sm font-semibold">Tổng lượt thi</p>
                <p class="text-3xl font-bold text-blue-800">{{ $stats['total_attempts'] }}</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                <p class="text-green-600 text-sm font-semibold">Vượt qua</p>
                <p class="text-3xl font-bold text-green-800">{{ $stats['passed_count'] }}</p>
                <p class="text-xs text-green-700 mt-1">{{ $stats['pass_rate'] }}%</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6">
                <p class="text-yellow-600 text-sm font-semibold">Điểm trung bình</p>
                <p class="text-3xl font-bold text-yellow-800">{{ number_format($stats['avg_score'], 2) }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6">
                <p class="text-purple-600 text-sm font-semibold">Điểm cao nhất</p>
                <p class="text-3xl font-bold text-purple-800">{{ number_format($stats['max_score'], 2) }}</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6">
                <p class="text-red-600 text-sm font-semibold">Thời gian TB</p>
                <p class="text-2xl font-bold text-red-800">{{ gmdate('H:i:s', $stats['avg_time'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-8 py-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Chi tiết lượt thi</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">STT</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Họ tên</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">MSSV</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Điểm</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">TG Hoàn thành</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Trạng thái</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Gian lận</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Ngày thi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $index => $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium">{{ $attempt->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $attempt->user->student_code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center font-bold text-lg">
                            <span class="px-3 py-1 rounded-full {{ $attempt->score >= ($quiz->pass_score ?? 5.0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($attempt->score, 2) }}/10
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            @if($attempt->start_time && $attempt->end_time)
                                {{ gmdate('H:i:s', $attempt->end_time->diffInSeconds($attempt->start_time)) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($attempt->score >= ($quiz->pass_score ?? 5.0))
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check"></i> Vượt qua
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-times"></i> Không vượt
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($attempt->cheat_warnings > 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold" title="Đã thoát tab/chuyển cửa sổ {{ $attempt->cheat_warnings }} lần">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $attempt->cheat_warnings }} lần
                                </span>
                            @else
                                <span class="text-green-500 font-medium text-sm"><i class="fas fa-check"></i> Hợp lệ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            {{ $attempt->end_time ? $attempt->end_time->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('exam.detail', $attempt->id) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-500">
                            Chưa có ai làm bài thi này
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection