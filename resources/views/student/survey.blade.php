@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg p-8 border border-indigo-50">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                <i class="fas fa-poll-h"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Khảo sát chất lượng</h1>
            <p class="text-gray-500 mt-2">Ý kiến của bạn giúp chúng tôi hoàn thiện hệ thống tốt hơn.</p>
        </div>

        <form action="/survey" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-3 text-lg">Bạn thấy đề thi hôm nay thế nào?</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-indigo-50 transition">
                        <input type="radio" name="rating" value="easy" class="w-5 h-5 text-indigo-600" required>
                        <span class="ml-3 font-medium text-gray-700">Dễ, vừa sức sinh viên</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-indigo-50 transition">
                        <input type="radio" name="rating" value="medium" class="w-5 h-5 text-indigo-600">
                        <span class="ml-3 font-medium text-gray-700">Bình thường, có tính phân loại</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-indigo-50 transition">
                        <input type="radio" name="rating" value="hard" class="w-5 h-5 text-indigo-600">
                        <span class="ml-3 font-medium text-gray-700">Rất khó, đánh đố quá nhiều</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-2">Góp ý thêm cho hệ thống:</label>
                <textarea name="feedback" rows="4" class="w-full border-gray-300 rounded-xl focus:ring-indigo-500" placeholder="Nhập ý kiến của bạn tại đây..."></textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition">
                Gửi khảo sát ngay
            </button>
        </form>
    </div>
</div>
@endsection