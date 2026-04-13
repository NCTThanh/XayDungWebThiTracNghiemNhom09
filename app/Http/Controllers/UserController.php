<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Hiển thị trang Hồ sơ cá nhân
     */
    public function profile()
    {
        return view('student.profile');
    }

    /**
     * Xử lý Cập nhật thông tin cơ bản
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name'  => 'required|string|max:255',
            // Kiểm tra email không trùng với người khác, trừ email của chính mình
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác!'
        ]);

        // Cập nhật thông tin (Không cho phép đổi student_code)
        /** @var \App\Models\User $user */
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Đã cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Xử lý Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed', 
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Mật khẩu nhập lại không khớp!'
        ]);

        
        if (md5($request->current_password) !== $user->password) {
            return back()->with('error', 'Mật khẩu hiện tại không chính xác!');
        }

       
        /** @var \App\Models\User $user */
        $user->update([
            'password' => md5($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới nhé.');
    }
}