@extends('layouts.admin')

@section('title', 'Hồ sơ Quản trị viên')

@section('content')
<div class="max-w-6xl mx-auto py-4">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><i class="fas fa-shield-alt text-indigo-600 mr-3"></i>Hồ sơ Quản trị hệ thống</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=4f46e5&color=fff&size=128&bold=true" 
                     class="w-32 h-32 rounded-full mx-auto border-4 border-indigo-50 shadow-md mb-4" alt="Avatar">
                <h3 class="text-2xl font-bold text-gray-900">{{ $admin->name }}</h3>
                <span class="inline-block bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mt-2">
                    {{ $admin->role }}
                </span>
                
                <div class="mt-8 text-left space-y-4 pt-6 border-t border-gray-50">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 font-medium">Tên đăng nhập:</span>
                        <span class="text-gray-900 font-bold">{{ $admin->username }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 font-medium">ID Quản trị:</span>
                        <span class="text-gray-900 font-bold">#{{ $admin->id }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h4 class="text-lg font-bold text-gray-900 mb-6 border-b pb-4">Quy mô quản lý</h4>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Quản lý Sinh viên</p>
                            <p class="text-xs text-gray-500">Toàn bộ dữ liệu người dùng</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Ngân hàng Đề thi</p>
                            <p class="text-xs text-gray-500">Câu hỏi & Đáp án hệ thống</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
                <h4 class="text-xl font-bold text-gray-900 mb-8 pb-4 border-b">Bảo mật tài khoản Quản trị</h4>
                
                <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="space-y-3">
                        <label class="text-sm font-bold text-gray-700 ml-1">Mật khẩu Quản trị hiện tại</label>
                        <input type="password" name="current_password" class="w-full px-6 py-4 rounded-2xl border-gray-200 border bg-gray-50 focus:bg-white focus:border-indigo-600 outline-none transition-all shadow-sm" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 ml-1">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="w-full px-6 py-4 rounded-2xl border-gray-200 border bg-gray-50 focus:bg-white focus:border-indigo-600 outline-none transition-all shadow-sm" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-gray-700 ml-1">Nhập lại mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" class="w-full px-6 py-4 rounded-2xl border-gray-200 border bg-gray-50 focus:bg-white focus:border-indigo-600 outline-none transition-all shadow-sm" required>
                        </div>
                    </div>
                    
                    <div class="pt-6">
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-100 transition-all transform hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Lưu thay đổi bảo mật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection