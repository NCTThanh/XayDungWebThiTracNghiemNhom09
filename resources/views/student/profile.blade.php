@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800"><i class="fas fa-user-circle text-indigo-600 mr-2"></i>Hồ sơ cá nhân</h1>
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-indigo-600 font-medium transition"><i class="fas fa-arrow-left mr-1"></i> Quay lại</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'SV') }}&background=4f46e5&color=fff&size=128" 
                     class="w-32 h-32 rounded-full mx-auto border-4 border-indigo-50 shadow-md mb-4" alt="Avatar">
                <h3 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                <p class="text-gray-500 text-sm mb-4">{{ Auth::user()->student_code }}</p>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Tài khoản Sinh viên</span>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h4 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Thông tin cơ bản</h4>
                <form action="{{ url('/profile') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mã sinh viên</label>
                            <input type="text" name="student_code" value="{{ Auth::user()->student_code }}" class="w-full border-gray-300 rounded-xl px-4 py-3 bg-gray-50 text-gray-600" readonly>
                            <p class="text-xs text-gray-400 mt-1">Không thể thay đổi mã SV</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên</label>
                            <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full border-gray-300 border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 outline-none transition" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border-gray-300 border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 outline-none transition" required>
                        </div>
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-md">
                        Cập nhật thông tin
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h4 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Đổi mật khẩu</h4>
                <form action="{{ route('user.password.update') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="w-full border-gray-300 border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 outline-none transition" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="w-full border-gray-300 border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 outline-none transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nhập lại mật khẩu mới</label>
                                <input type="password" name="new_password_confirmation" class="w-full border-gray-300 border focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 outline-none transition" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-6 rounded-xl transition shadow-md">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection