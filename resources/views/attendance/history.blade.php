@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-history text-indigo-600 mr-2"></i> Lịch Sử Điểm Danh</h1>
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Về trang chủ
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if(count($records) > 0)
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 text-gray-500 font-semibold text-sm">Tên Phiên / Lớp</th>
                        <th class="py-4 px-6 text-gray-500 font-semibold text-sm">Thời Gian Quét</th>
                        <th class="py-4 px-6 text-gray-500 font-semibold text-sm text-right">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($records as $r)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-medium text-gray-800">{{ $r->session_title }}</td>
                        <td class="py-4 px-6 text-gray-600">{{ date('H:i:s - d/m/Y', strtotime($r->scan_time)) }}</td>
                        <td class="py-4 px-6 text-right">
                            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-sm font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Có mặt
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-16">
                <i class="fas fa-clipboard-list text-6xl text-gray-200 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-700">Chưa có dữ liệu</h3>
                <p class="text-gray-500 mt-2">Bạn chưa tham gia điểm danh bất kỳ buổi học nào.</p>
            </div>
        @endif
    </div>
</div>
@endsection