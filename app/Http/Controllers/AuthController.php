<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Session::has('user') || Session::has('admin')) {
            return redirect()->intended('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',   // Có thể là username hoặc email
            'password' => 'required'
        ]);

        $input = trim($request->email);
        $password = $request->password;

       // ==========================================
        // 1. KIỂM TRA ADMIN (bảng admins)
        // ==========================================
        $admin = DB::table('admins')
            ->where('username', $input)
            ->first();

        if ($admin) {
            $isMatch = false;
            // Kiểm tra theo MD5
            if (md5($password) === $admin->password) {
                $isMatch = true;
            } 
            // Kiểm tra theo chữ thường (nếu có)
            elseif ($password === $admin->password) {
                $isMatch = true;
            } 
            // Kiểm tra theo Bcrypt (chỉ chạy Hash::check nếu chuỗi có dấu hiệu của Bcrypt để tránh lỗi)
            elseif (preg_match('/^\$2[ayb]\$.{56}$/', $admin->password) && \Illuminate\Support\Facades\Hash::check($password, $admin->password)) {
                $isMatch = true;
            }

            // Nếu khớp mật khẩu thì cho đăng nhập
            if ($isMatch) {
                Session::put('admin', $admin);
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
            }
        }
        
        // ==================== 2. KIỂM TRA GIẢNG VIÊN (bảng giangvien) ====================
        $teacher = DB::table('giangvien')
                    ->where('username', $input)
                    ->orWhere('email', $input)
                    ->first();

        if ($teacher && Hash::check($password, $teacher->password)) {
            // Lưu dưới key 'admin' để dùng chung middleware
            Session::put('admin', $teacher);
            Session::regenerate();
            return redirect('/admin/dashboard')
                    ->with('success', 'Đăng nhập Giảng viên thành công!');
        }

        // ==================== 3. KIỂM TRA SINH VIÊN (bảng users) ====================
        $user = User::where('email', $input)->first();
        if ($user) {
            $valid = false;

            if (strlen(trim($user->password)) === 32) {
                // MD5 cũ
                if (md5($password) === $user->password) $valid = true;
            } else {
                // bcrypt mới
                if (Hash::check($password, $user->password)) $valid = true;
            }

            if ($valid) {
                Session::put('user', $user);
                Auth::login($user);
                Session::regenerate();
                return redirect('/dashboard')->with('success', 'Đăng nhập thành công!');
            }
        }

        return back()
            ->with('error', 'Tên đăng nhập / Email hoặc mật khẩu không đúng!')
            ->withInput();
    }
    // ==================== Hàm kiểm tra đăng ký của Sinh Viên ====================
    public function registerForm()
    {
        return view('auth.register'); 
    }

    // Hàm xử lý đăng ký và mã hóa MD5
    public function register(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'student_code' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        // 2. Lưu vào Database với MD5
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'student_code' => $request->student_code,
            'class' => $request->class,
            'password' => md5($request->password),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }
    public function logout()
    {
        Auth::logout();
        Session::flush();
        Session::regenerate();
        return redirect('/login')->with('success', 'Đã đăng xuất!');
    }

    
}