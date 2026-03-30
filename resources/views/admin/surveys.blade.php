@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kết quả khảo sát</h1>
        <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-bold text-sm">
            Đã hoàn thành: {{ count($surveyedUsers) }} sinh viên
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 p-6">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Danh sách sinh viên đã hoàn thành khảo sát chất lượng:</h3>
        
        @if(count($surveyedUsers) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($surveyedUsers as $user)
            <div class="flex items-center p-4 border rounded-xl bg-gray-50 hover:bg-indigo-50 transition">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl mr-4 shadow-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800">{{ $user->name }}</h4>
                    <p class="text-xs text-gray-500">{{ $user->student_code ?? $user->email }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-10 border-2 border-dashed rounded-xl">
            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Chưa có sinh viên nào làm bài khảo sát.</p>
        </div>
        @endif
    </div>
</div>
@endsection