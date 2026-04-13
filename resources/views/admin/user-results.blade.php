@extends('layouts.admin')

@section('title', 'Kết quả của ' . $user->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow p-8 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-2xl text-indigo-600"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                <p class="text-gray-600">{{ $user->student_code }} | {{ $user->email }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 pt-6 border-t">
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-blue-600 text-sm font-semibold">Tổng lượt thi</p>
                <p class="text-3xl font-bold text-blue-800">{{ $attempts->count() }}</p>
            </div>

            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-green-600 text-sm font-semibold">Vượt qua</p>
                <p class="text-3xl font-bold text-green-800">
                    {{ $attempts->where('score', '>=', 5.0)->count() }}
                </p>
            </div>

            <div class="bg-yellow-50 rounded-xl p-4">
                <p class="text-yellow-600 text-sm font-semibold">Điểm TB</p>
                <p class="text-3xl font-bold text-yellow-800">
                    {{ number_format($attempts->avg('score'), 2) }}
                </p>
            </div>

            <div class="bg-purple-50 rounded-xl p-4">
                <p class="text-purple-600 text-sm font-semibold">Điểm cao</p>
                <p class="text-3xl font-bold text-purple-800">
                    {{ number_format($attempts->max('score'), 2) }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-8 py-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Lịch sử làm bài</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Đề thi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Điểm</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">TG hoàn thành</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Trạng thái</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Gian lận</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Ngày thi</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">
                            {{ $attempt->quiz->title }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-lg">
                            <span class="px-3 py-1 rounded-full {{ $attempt->score >= ($attempt->quiz->pass_score ?? 5.0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                            @if($attempt->score >= ($attempt->quiz->pass_score ?? 5.0))
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
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold" title="{{ $attempt->cheat_warnings }} lần chuyển tab">
                                    <i class="fas fa-exclamation-triangle"></i> Vi phạm
                                </span>
                            @else
                                <span class="text-green-500 font-medium text-sm"><i class="fas fa-check"></i></span>
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
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            Sinh viên này chưa làm bài thi nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection