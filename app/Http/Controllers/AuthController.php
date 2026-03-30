<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function loginForm() { return view('auth.login'); }
    public function registerForm() { return view('auth.register'); }

    public function login(Request $r) {
        $admin = Admin::where('username', $r->email)->where('password', $r->password)->first();
        if ($admin) {
            session(['admin' => $admin]);
            return redirect('/admin');
        }
        $user = User::where('email', $r->email)->where('password', md5($r->password))->first();
        if ($user) {
            Auth::login($user);
            return redirect('/dashboard');
        }
        return back()->with('error', 'Sai thông tin đăng nhập');
    }

    public function register(Request $r) {
        User::create([
            'name' => $r->name,
            'email' => $r->email,
            'student_code' => $r->student_code,
            'class' => $r->class,
            'password' => md5($r->password),
            'role' => 'student'
        ]);
        return redirect('/')->with('success', 'Đăng ký thành công');
    }

    public function logout() {
        Auth::logout();
        session()->forget('admin');
        return redirect('/');
    }
}
