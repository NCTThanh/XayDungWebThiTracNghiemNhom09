@extends('layouts.app')

@section('title', 'Chi tiết bài thi')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6">
        @if(session()->has('admin'))
            <a href="javascript:history.back()" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left"></i> Quay lại trang quản lý
            </a>
        @else
            <a href="{{ route('exam.history') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left"></i> Quay lại lịch sử
            </a>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow p-8 mb-6 border border-gray-100">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $attempt->quiz->title }}</h1>
                <p class="text-gray-600">Làm bài lúc: {{ $attempt->end_time->format('d/m/Y H:i:s') }}</p>
                @if(session()->has('admin'))
                    <p class="text-indigo-600 font-bold mt-2"><i class="fas fa-user-graduate mr-1"></i> Sinh viên: {{ $attempt->user->name ?? 'N/A' }} ({{ $attempt->user->student_code ?? '' }})</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Điểm đạt được</p>
                <p class="text-5xl font-bold {{ $attempt->score >= ($attempt->quiz->pass_score ?? 5.0) ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($attempt->score, 2) }}<span class="text-2xl text-gray-400">/10</span>
                </p>
                <p class="mt-3 px-4 py-2 rounded-lg font-bold text-sm {{ $attempt->score >= ($attempt->quiz->pass_score ?? 5.0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $attempt->score >= ($attempt->quiz->pass_score ?? 5.0) ? '✓ VƯỢT QUA' : '✗ KHÔNG ĐẠT' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 pt-6 border-t border-gray-100">
            <div class="text-center p-3 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Tổng câu</p>
                <p class="text-2xl font-black text-blue-600">{{ $attempt->total_questions }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Câu đúng</p>
                <p class="text-2xl font-black text-green-600">{{ $attempt->correct_answers }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Câu sai</p>
                <p class="text-2xl font-black text-red-600">{{ $attempt->total_questions - $attempt->correct_answers }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Thời gian</p>
                <p class="text-2xl font-black text-purple-600">
                    @if($attempt->start_time && $attempt->end_time)
                        {{ gmdate('H:i:s', $attempt->end_time->diffInSeconds($attempt->start_time)) }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($attempt->attemptAnswers as $index => $answer)
        @php
            $question = $answer->question;
            $correctOption = $question->options->where('is_correct', 1)->first();
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $answer->is_correct ? 'bg-green-100 shadow-inner' : 'bg-red-100 shadow-inner' }}">
                            @if($answer->is_correct)
                                <i class="fas fa-check text-xl text-green-600"></i>
                            @else
                                <i class="fas fa-times text-xl text-red-600"></i>
                            @endif
                        </div>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 leading-relaxed">
                            <span class="text-indigo-600">Câu {{ $index + 1 }}:</span> {{ $question->question }}
                        </h3>
                        <div class="flex gap-2 text-xs font-medium">
                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                Độ khó: {{ ucfirst($question->difficulty) }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-600 border border-indigo-100">
                                Điểm: {{ $question->marks }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 ml-0 sm:ml-14">
                    @foreach($question->options as $option)
                    @php
                        $isCorrectAnswer = $option->id === $correctOption->id;
                        $isUserAnswer = $option->id === $answer->option_id;
                    @endphp
                    <div class="p-4 rounded-xl border-2 transition-all {{ 
                        ($isUserAnswer && !$answer->is_correct) ? 'border-red-300 bg-red-50' :
                        ($isCorrectAnswer ? 'border-green-400 bg-green-50' : 'border-gray-100 bg-gray-50')
                    }}">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($isUserAnswer && !$answer->is_correct)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-red-500 items-center justify-center text-white text-xs font-bold shadow-sm"><i class="fas fa-times"></i></span>
                                @elseif($isCorrectAnswer)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-green-500 items-center justify-center text-white text-xs font-bold shadow-sm"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="inline-block w-6 h-6 rounded-full border-2 border-gray-300 bg-white"></span>
                                @endif
                            </div>
                            <span class="flex-grow font-medium {{ ($isCorrectAnswer || $isUserAnswer) ? 'text-gray-900' : 'text-gray-600' }}">{{ $option->option_text }}</span>
                            
                            @if($isCorrectAnswer)
                                <span class="text-xs bg-green-600 text-white px-2.5 py-1 rounded-md font-bold tracking-wide uppercase shadow-sm">Đáp án đúng</span>
                            @endif
                            @if($isUserAnswer && !$answer->is_correct)
                                <span class="text-xs bg-red-600 text-white px-2.5 py-1 rounded-md font-bold tracking-wide uppercase shadow-sm ml-2">Bạn chọn</span>
                            @elseif($isUserAnswer && $answer->is_correct)
                                <span class="text-xs bg-indigo-600 text-white px-2.5 py-1 rounded-md font-bold tracking-wide uppercase shadow-sm ml-2">Lựa chọn của bạn</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(!$answer->is_correct && $correctOption)
                <div class="mt-6 ml-0 sm:ml-14 p-5 bg-blue-50 border border-blue-200 rounded-xl relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500 rounded-l-xl"></div>
                    <p class="text-sm text-blue-900">
                        <strong class="flex items-center gap-2 mb-1 text-blue-700"><i class="fas fa-lightbulb"></i> Giải thích đáp án:</strong> 
                        Đáp án chính xác nhất cho câu hỏi này là: <strong class="bg-blue-100 px-2 py-0.5 rounded">{{ $correctOption->option_text }}</strong>
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        @if(session()->has('admin'))
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 bg-gray-800 text-white px-8 py-4 rounded-2xl font-bold hover:bg-gray-900 transition shadow-lg hover:-translate-y-1">
                <i class="fas fa-arrow-left"></i> Trở về Quản lý kết quả
            </a>
        @else
            <a href="{{ route('exam.history') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 hover:-translate-y-1">
                <i class="fas fa-arrow-left"></i> Trở về Lịch sử của tôi
            </a>
        @endif
    </div>
</div>
@endsection