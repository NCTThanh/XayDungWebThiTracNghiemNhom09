<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Cache;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Email không tồn tại trong hệ thống!');
        }

        $token = md5(uniqid());
        Cache::put('password_reset_' . $user->email, $token, now()->addMinutes(60));

        Mail::to($user->email)->send(new PasswordResetMail($user, $token));

        return back()->with('success', 'Đã gửi link reset mật khẩu vào email!');
    }

    public function showResetForm($token, $email)
    {
        if (Cache::get('password_reset_' . $email) !== $token) {
            return redirect('/forgot-password')->with('error', 'Link reset hết hạn hoặc không hợp lệ!');
        }
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = md5($request->password);
            $user->save();
            Cache::forget('password_reset_' . $request->email);
            return redirect('/login')->with('success', 'Mật khẩu đã được thay đổi thành công!');
        }
        return back()->with('error', 'Lỗi reset mật khẩu!');
    }
}