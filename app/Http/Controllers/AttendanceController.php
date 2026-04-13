<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AttendanceController extends Controller
{
    // ===================== GIÁO VIÊN TẠO QR =====================
    public function generateQr(Request $request)
    {
        // 1. Kiểm tra session admin
        $admin = \Illuminate\Support\Facades\Session::get('admin');
        if (!$admin) {
            return back()->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!');
        }

        // 2. SỬA LỖI Ở ĐÂY: Đổi 'quiz_id' thành 'title' để khớp với form ngoài giao diện
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1'
        ]);

        $token = \Illuminate\Support\Str::random(32);
        $expiry = now()->addMinutes((int) $request->duration); 

        // 3. Lưu vào database
        \Illuminate\Support\Facades\DB::table('attendance_sessions')->insert([
            'title'       => $request->title, // Lấy tên phiên điểm danh từ form
            'qr_token'    => $token,
            'expiry_time' => $expiry,
            'created_by'  => $admin->id, 
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $qrUrl = url("/attendance/scan/{$token}");

        return back()->with('success', 'Đã tạo QR Code điểm danh thành công!')
                     ->with('qr_url', $qrUrl)
                     ->with('qr_token', $token);
    }

    // ===================== TRANG QUÉT QR (Sinh viên) =====================
    public function showScanner()
    {
        return view('attendance.scan');
    }

    // ===================== XỬ LÝ ĐIỂM DANH =====================
    public function history()
    {
        $user = \Illuminate\Support\Facades\Auth::user() ?? \Illuminate\Support\Facades\Session::get('user');
        if (!$user) return redirect('/login');

        $records = \Illuminate\Support\Facades\DB::table('attendance_records')
            ->join('attendance_sessions', 'attendance_records.session_id', '=', 'attendance_sessions.id')
            ->where('attendance_records.user_id', $user->id)
            ->select('attendance_records.*', 'attendance_sessions.title as session_title')
            ->orderBy('attendance_records.scan_time', 'desc')
            ->get();

        return view('attendance.history', compact('records'));
    }
    public function submitAttendance(Request $request)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn chưa đăng nhập!']);
        }

        $qrToken = trim($request->qr_token);

        $session = DB::table('attendance_sessions')
                    ->where('qr_token', $qrToken)
                    ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR không tồn tại!'
            ]);
        }

        if (now() > $session->expiry_time) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR đã hết hạn!'
            ]);
        }

        $already = DB::table('attendance_records')
                    ->where('session_id', $session->id)
                    ->where('user_id', $user->id)
                    ->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã điểm danh rồi!'
            ]);
        }

        DB::table('attendance_records')->insert([
            'session_id' => $session->id,
            'user_id'    => $user->id,
            'scan_time'  => now(),
            'status'     => 'Present',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '🎉 Điểm danh thành công!'
        ]);
    }
}