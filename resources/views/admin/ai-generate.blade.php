@extends('layouts.admin')

@section('title', 'Phát sinh câu hỏi bằng AI')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.quizzes') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Phát sinh câu hỏi bằng AI</h1>
        <p class="text-gray-600 mb-8">
            Sử dụng Gemini AI để tự động tạo câu hỏi trắc nghiệm cho đề thi của bạn
        </p>

        <form action="{{ route('admin.ai-generate.store') }}" method="POST" class="space-y-6">
            @csrf

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="font-semibold text-red-800 mb-2">Lỗi:</h3>
                <ul class="text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-book"></i> Chọn đề thi
                </label>
                <select name="quiz_id" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-indigo-600 focus:outline-none" required>
                    <option value="">-- Chọn đề thi --</option>
                    @foreach($quizzes as $quiz)
                    <option value="{{ $quiz->id }}" {{ $quizId == $quiz->id ? 'selected' : '' }}>
                        {{ $quiz->title }} ({{ $quiz->questions_count ?? 0 }} câu hiện tại)
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-paragraph"></i> Chủ đề/Nội dung
                </label>
                <input type="text" name="topic" 
                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-indigo-600 focus:outline-none"
                       placeholder="VD: Toán học cấp 2, Lịch sử Việt Nam, Chemistry..."
                       required>
                <p class="text-sm text-gray-500 mt-1">Mô tả chi tiết chủ đề để AI tạo câu hỏi chính xác hơn</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-layer-group"></i> Số lượng câu hỏi
                    </label>
                    <input type="number" name="num_questions" value="5" min="1" max="20"
                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-indigo-600 focus:outline-none"
                           required>
                    <p class="text-sm text-gray-500 mt-1">1-20 câu hỏi</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-signal"></i> Mức độ khó
                    </label>
                    <select name="difficulty" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-indigo-600 focus:outline-none">
                        <option value="easy">Dễ (Easy)</option>
                        <option value="medium" selected>Trung bình (Medium)</option>
                        <option value="hard">Khó (Hard)</option>
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle"></i>
                    <strong>Lưu ý:</strong> AI sẽ tạo câu hỏi trắc nghiệm tự động. Bạn nên kiểm tra và chỉnh sửa các câu hỏi sau khi tạo để đảm bảo chất lượng.
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="{{ route('admin.quizzes') }}" 
                   class="flex-1 text-center py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="flex-1 bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-magic"></i> Phát sinh câu hỏi
                </button>
            </div>
        </form>
    </div>

    <!-- Thông tin hữu ích -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-indigo-50 rounded-xl p-6">
            <h3 class="font-semibold text-indigo-900 mb-3">💡 Mẹo sử dụng</h3>
            <ul class="text-sm text-indigo-800 space-y-2">
                <li>✓ Mô tả chủ đề càng chi tiết, câu hỏi AI tạo càng phù hợp</li>
                <li>✓ Hãy tạo 5-10 câu hỏi trước để kiểm tra chất lượng</li>
                <li>✓ Bạn có thể tạo nhiều lần để có câu hỏi đa dạng</li>
                <li>✓ Luôn kiểm tra đáp án trước khi công bố đề thi</li>
            </ul>
        </div>

        <div class="bg-green-50 rounded-xl p-6">
            <h3 class="font-semibold text-green-900 mb-3">✨ Tính năng AI</h3>
            <ul class="text-sm text-green-800 space-y-2">
                <li>✓ Tạo câu hỏi trắc nghiệm tự động</li>
                <li>✓ Điều chỉnh mức độ khó</li>
                <li>✓ Đáp án được AI kiểm tra</li>
                <li>✓ Hỗ trợ nhiều chủ đề khác nhau</li>
            </ul>
        </div>
    </div>
</div>
@endsection
