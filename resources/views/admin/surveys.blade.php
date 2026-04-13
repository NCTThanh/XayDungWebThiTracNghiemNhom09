@extends('layouts.admin')

@section('title', 'Quản lý Khảo sát')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-poll text-indigo-600 mr-3"></i>Phản hồi từ Sinh viên</h1>
        <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl font-bold text-sm">
            Tổng số: {{ count($feedbacks) }} lượt đánh giá
        </div>
    </div>

    @if(count($feedbacks) > 0)
    <div class="grid grid-cols-1 gap-6">
        @foreach($feedbacks as $userId => $userAnswers)
        @php 
            $userInfo = $userAnswers->first();
            $rating = $userAnswers->where('question_id', 1)->first();
            $comment = $userAnswers->where('question_id', 2)->first();
        @endphp
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6 sm:p-8 flex flex-col md:flex-row gap-6">
                <div class="md:w-1/4 border-b md:border-b-0 md:border-r border-gray-50 pb-4 md:pb-0 md:pr-6">
                    <h4 class="font-bold text-gray-900 text-lg">{{ $userInfo->name }}</h4>
                    <p class="text-indigo-600 text-sm font-medium mt-1">MSV: {{ $userInfo->student_code }}</p>
                    <p class="text-gray-400 text-xs mt-3 flex items-center">
                        <i class="far fa-clock mr-1"></i> {{ date('H:i d/m/Y', strtotime($userInfo->created_at)) }}
                    </p>
                </div>

                <div class="md:w-3/4 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Đánh giá đề thi:</span>
                        @if($rating)
                            @if($rating->answer == 'easy')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">😎 DỄ, VỪA SỨC</span>
                            @elseif($rating->answer == 'medium')
                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">🧐 BÌNH THƯỜNG</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">🤯 RẤT KHÓ</span>
                            @endif
                        @endif
                    </div>
                    
                    <div>
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider block mb-2">Góp ý chi tiết:</span>
                        <div class="bg-gray-50 rounded-2xl p-5 text-gray-700 italic border border-gray-100 relative">
                            <i class="fas fa-quote-left absolute top-3 left-3 text-gray-200 text-xl"></i>
                            <p class="relative z-10 pl-6">
                                {{ $comment ? $comment->answer : 'Không có góp ý thêm.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl text-gray-300">
            <i class="fas fa-comment-slash"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Chưa có đánh giá nào</h3>
        <p class="text-gray-500 mt-2">Dữ liệu sẽ xuất hiện khi sinh viên thực hiện khảo sát trên Dashboard.</p>
    </div>
    @endif
</div>
@endsection