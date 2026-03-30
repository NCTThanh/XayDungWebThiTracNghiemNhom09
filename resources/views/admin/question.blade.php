@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.quizzes') }}" class="text-indigo-600 hover:underline text-sm font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách đề thi
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Đề thi: {{ $quiz->title }}</h1>
        </div>
        <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-bold text-sm">
            Tổng: {{ count($quiz->questions) }} câu hỏi
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
                <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Thêm câu hỏi mới</h2>
                
                <form action="{{ route('admin.questions.store', $quiz->id) }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung câu hỏi *</label>
                        <textarea name="question" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Nhập nội dung câu hỏi..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Các đáp án <span class="text-xs font-normal text-gray-500">(Tick xanh vào đáp án đúng)</span></label>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct" value="0" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer" required title="Chọn làm đáp án đúng">
                                <input type="text" name="options[]" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm text-sm" placeholder="Đáp án A" required>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct" value="1" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer" required title="Chọn làm đáp án đúng">
                                <input type="text" name="options[]" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm text-sm" placeholder="Đáp án B" required>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct" value="2" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer" required title="Chọn làm đáp án đúng">
                                <input type="text" name="options[]" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm text-sm" placeholder="Đáp án C" required>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct" value="3" class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer" required title="Chọn làm đáp án đúng">
                                <input type="text" name="options[]" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm text-sm" placeholder="Đáp án D" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                        <i class="fas fa-plus-circle mr-1"></i> Lưu Câu Hỏi
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            @forelse($quiz->questions as $index => $q)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-bold text-gray-800 text-lg">
                        <span class="text-indigo-600 mr-1">Câu {{ $index + 1 }}:</span> {{ $q->question }}
                    </h3>
                    <button class="text-red-400 hover:text-red-600 transition" title="Xóa câu hỏi"><i class="fas fa-trash-alt"></i></button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    @foreach($q->options as $opt)
                    <div class="p-3 border rounded-lg flex items-center {{ $opt->is_correct ? 'bg-green-50 border-green-300 text-green-800 font-medium' : 'bg-gray-50 border-gray-200 text-gray-600' }}">
                        <i class="fas {{ $opt->is_correct ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-300' }} mr-3 text-lg"></i>
                        {{ $opt->option_text }}
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="bg-white p-12 text-center rounded-xl shadow-sm border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700 mb-1">Chưa có câu hỏi nào</h3>
                <p class="text-gray-500">Hãy sử dụng form bên trái để thêm câu hỏi đầu tiên cho đề thi này.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection