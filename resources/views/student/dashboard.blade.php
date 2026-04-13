@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="max-w-6xl mx-auto">
    
    @if((Auth::user()->survey_done ?? 0) == 0)
    <div class="mb-8 bg-gradient-to-r from-amber-400 to-orange-500 rounded-2xl p-1 shadow-lg transform transition hover:-translate-y-1">
        <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-xl p-5 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-2xl shrink-0">
                    <i class="fas fa-gift"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Dành ra 1 phút để làm khảo sát nhé!</h3>
                    <p class="text-gray-600 text-sm">Đánh giá của bạn là viên gạch giúp chúng tôi xây dựng hệ thống tốt hơn.</p>
                </div>
            </div>
            <a href="/survey" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-bold transition shadow-md whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-poll"></i> Tham gia khảo sát ngay
            </a>
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">👋 Xin chào, {{ Auth::user()->name ?? session('user')->name ?? 'Sinh viên' }}</h2>
            <p class="text-gray-500 mt-1 font-medium">Mã SV: {{ Auth::user()->student_code ?? session('user')->student_code ?? 'N/A' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('attendance.scan') }}" class="bg-green-600 text-white px-5 py-2.5 rounded-xl hover:bg-green-700 font-bold flex items-center gap-2 transition shadow-sm">
                <i class="fas fa-qrcode"></i> Điểm Danh
            </a>
            <a href="{{ route('exam.history') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-bold flex items-center gap-2 transition shadow-sm">
                <i class="fas fa-history"></i> Lịch Sử
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-semibold">Đề Thi</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $quizzes->count() }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-semibold">Điểm Danh</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->attendanceRecords()->count() ?? 0 }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-bullseye"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-semibold">Điểm TB</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format(Auth::user()->averageScore ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-semibold">Xếp Hạng</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->rank ?? 'N/A' }}</h3>
            </div>
        </div>
    </div>

    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-clipboard-list text-indigo-600"></i> Danh Sách Đề Thi Có Sẵn
    </h3>
    
    @if($quizzes->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($quizzes as $q)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow flex flex-col h-full">
            <h5 class="text-lg font-bold text-gray-800 mb-3">{{ $q->title }}</h5>
            <div class="text-gray-600 text-sm space-y-2 mb-6 flex-1">
                <p><i class="fas fa-clock w-5 text-center text-indigo-400"></i> {{ $q->duration }} phút</p>
                <p><i class="fas fa-list w-5 text-center text-indigo-400"></i> {{ $q->questions->count() ?? 0 }} câu hỏi</p>
            </div>
            <a href="/exam/{{ $q->id }}" class="block w-full text-center bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-bold py-3 rounded-xl transition-colors">
                <i class="fas fa-play mr-1"></i> Vào Thi
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 text-blue-700 p-6 rounded-2xl flex items-center gap-3">
        <i class="fas fa-info-circle text-2xl"></i> 
        <span class="font-medium">Chưa có đề thi nào được mở. Vui lòng liên hệ giáo viên của bạn!</span>
    </div>
    @endif
</div>
@endsection