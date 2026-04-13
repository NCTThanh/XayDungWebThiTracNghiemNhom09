@extends('layouts.admin')

@section('content')
<div class="container-fluid px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.attendance') }}" class="text-indigo-600 hover:underline mb-2 inline-block"><i class="fas fa-arrow-left"></i> Quay lại</a>
            <h2 class="text-2xl font-bold text-gray-800">Chi tiết phiên: {{ $session->title }}</h2>
        </div>
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700"><i class="fas fa-print"></i> In danh sách</button>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h4 class="font-semibold text-lg mb-4">Danh sách Sinh viên có mặt ({{ count($records) }} người)</h4>
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="py-3 px-4">STT</th>
                    <th class="py-3 px-4">Mã SV</th>
                    <th class="py-3 px-4">Họ và Tên</th>
                    <th class="py-3 px-4">Thời gian quét QR</th>
                    <th class="py-3 px-4 text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($records as $index => $r)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-bold text-indigo-600">{{ $r->student_code ?? 'N/A' }}</td>
                    <td class="py-3 px-4">{{ $r->name }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ date('H:i:s d/m/Y', strtotime($r->scan_time)) }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Có mặt</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection