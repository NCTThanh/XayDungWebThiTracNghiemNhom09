@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 mt-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-history text-indigo-600 mr-2"></i> Lịch sử làm bài</h1>
        <a href="/dashboard" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tên đề thi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Ngày thi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Điểm số</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attempts as $attempt)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $attempt->quiz->title ?? 'Đề thi đã bị xóa' }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ date('d/m/Y H:i', strtotime($attempt->created_at)) }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="inline-block px-3 py-1 rounded-full font-bold {{ $attempt->score >= 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ number_format($attempt->score, 2) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300 block"></i>
                        Bạn chưa thực hiện bài thi nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t">
            {{ $attempts->links() ?? '' }}
        </div>
    </div>
</div>
@endsection