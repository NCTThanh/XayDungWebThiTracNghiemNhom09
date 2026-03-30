@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lịch sử làm bài</h1>
    
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
                @forelse($results as $res)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $res->quiz_title }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ date('d/m/Y H:i', strtotime($res->created_at)) }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="inline-block px-3 py-1 rounded-full font-bold {{ $res->score >= 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ number_format($res->score, 2) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-500">Bạn chưa thực hiện bài thi nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection