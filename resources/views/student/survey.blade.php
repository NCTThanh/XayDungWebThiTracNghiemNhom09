@extends('layouts.app')

@section('title', 'Khảo sát hệ thống')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full mb-4 shadow-inner">
            <i class="fas fa-clipboard-list text-4xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900">Khảo sát trải nghiệm</h1>
        <p class="text-gray-500 mt-2 text-lg">Ý kiến của bạn là viên gạch xây dựng hệ thống tốt hơn!</p>
    </div>

    <form action="/survey" method="POST">
        @csrf
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 sm:p-10 space-y-10">
                
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Mức độ khó của các đề thi hiện tại?
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="easy" class="peer sr-only" required>
                            <div class="p-5 border-2 border-gray-200 rounded-2xl hover:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all text-center">
                                <div class="text-4xl mb-2">😊</div>
                                <p class="font-bold text-gray-700 peer-checked:text-indigo-700">Dễ</p>
                                <p class="text-xs text-gray-500 mt-1">Vừa sức sinh viên</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="medium" class="peer sr-only">
                            <div class="p-5 border-2 border-gray-200 rounded-2xl hover:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all text-center">
                                <div class="text-4xl mb-2">🤔</div>
                                <p class="font-bold text-gray-700 peer-checked:text-indigo-700">Trung bình</p>
                                <p class="text-xs text-gray-500 mt-1">Có tính phân loại</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="hard" class="peer sr-only">
                            <div class="p-5 border-2 border-gray-200 rounded-2xl hover:bg-indigo-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all text-center">
                                <div class="text-4xl mb-2">🤯</div>
                                <p class="font-bold text-gray-700 peer-checked:text-indigo-700">Khó</p>
                                <p class="text-xs text-gray-500 mt-1">Nhiều câu hóc búa</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Góp ý thêm cho hệ thống
                    </h3>
                    <textarea name="feedback" rows="5" 
                              class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:border-indigo-600 focus:ring-0 outline-none transition-all resize-none text-gray-700" 
                              placeholder="Hãy cho chúng tôi biết bạn muốn cải thiện tính năng nào..."></textarea>
                </div>

            </div>
            
            <div class="bg-gray-50 p-8 border-t border-gray-100 flex items-center justify-end">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 text-gray-500 font-bold hover:text-gray-800 transition mr-4">Bỏ qua</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </div>
        </div>
    </form>
</div>
@endsection