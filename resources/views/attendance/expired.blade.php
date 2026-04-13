@extends('layouts.app')  

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-10 text-center">
        <div class="text-6xl mb-6">⏰</div>
        <h2 class="text-2xl font-bold text-red-600 mb-3">Mã QR đã hết hạn</h2>
        <p class="text-gray-600 mb-8">
            Phiên điểm danh này đã hết thời gian. <br>
            Vui lòng yêu cầu giảng viên tạo mã QR mới.
        </p>
        <a href="/dashboard" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-xl hover:bg-indigo-700 transition">
            Quay về Dashboard
        </a>
    </div>
</div>
@endsection