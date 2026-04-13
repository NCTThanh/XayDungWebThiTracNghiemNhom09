@extends('layouts.admin')

@section('content')
<div class="container-fluid px-6 py-8">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">📍 Quản Lý Điểm Danh QR Code</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
            <h4 class="font-semibold text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-qrcode text-blue-600"></i> Tạo Phiên Điểm Danh Mới
            </h4>
            
            <form id="createQrForm" method="POST" action="{{ route('admin.attendance.generate') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên phiên điểm danh</label>
                    <input type="text" name="title" class="form-control w-full p-2 border rounded" placeholder="VD: Điểm danh lớp sáng thứ 5" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thời hạn (phút)</label>
                    <input type="number" name="duration" class="form-control w-full p-2 border rounded" value="30" min="5" max="180" required>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3 text-lg bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-qrcode"></i> TẠO MÃ QR CODE
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border border-gray-100 lg:col-span-2" id="qrCard" style="display: {{ session('qr_url') ? 'block' : 'none' }};">
            <h4 class="font-semibold text-lg mb-4">✅ Mã QR Đã Tạo</h4>
            
            <div class="flex flex-col items-center">
                <img id="qrImage" src="{{ session('qr_url') ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data='.urlencode(session('qr_url')) : '' }}" alt="QR Code" class="border-4 border-green-400 rounded-xl shadow-md mb-4" style="max-width: 280px;">
                
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-600">Token:</p>
                    <code id="qrToken" class="bg-gray-100 px-3 py-1 rounded text-sm">{{ session('qr_token') }}</code>
                </div>

                @if(session('qr_url'))
                <a id="downloadBtn" href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode(session('qr_url')) }}" download="QR-DiemDanh.png" class="btn btn-success mb-2 bg-green-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-download"></i> Tải QR Code
                </a>
                @endif
            </div>
        </div>

    </div>

    <div class="mt-10 bg-white rounded-xl shadow p-6">
        <h4 class="font-semibold text-lg mb-4">📋 Danh Sách Phiên Điểm Danh</h4>
        
        <div class="table-responsive">
            <table class="table table-hover w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">#</th>
                        <th class="py-2">Tên Phiên</th>
                        <th class="py-2">Token</th>
                        <th class="py-2">Thời Gian Tạo</th>
                        <th class="py-2">Hết Hạn</th>
                        <th class="py-2 text-center">Số SV Điểm Danh</th>
                        <th class="py-2">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions ?? [] as $session)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3">{{ $loop->iteration }}</td>
                        <td class="py-3"><strong>{{ $session->title ?? 'Không có tên' }}</strong></td>
                        <td class="py-3"><code>{{ substr($session->qr_token, 0, 12) }}...</code></td>
                        <td class="py-3">{{ date('H:i d/m/Y', strtotime($session->created_at)) }}</td>
                        <td class="py-3">{{ date('H:i d/m/Y', strtotime($session->expiry_time)) }}</td>
                        <td class="py-3 text-center">
    <a href="{{ route('admin.attendance.detail', $session->id) }}" class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full hover:bg-blue-200 transition font-bold" title="Xem danh sách">
        {{ $session->records_count }} SV <i class="fas fa-eye ml-1"></i>
    </a>
</td>
                        <td class="py-3">
                            @if(strtotime(now()) > strtotime($session->expiry_time))
                                <span class="badge bg-secondary bg-gray-200 text-gray-700 px-2 py-1 rounded">Hết hạn</span>
                            @else
                                <span class="badge bg-success bg-green-100 text-green-700 px-2 py-1 rounded">Đang hoạt động</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            Chưa có phiên điểm danh nào được tạo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection