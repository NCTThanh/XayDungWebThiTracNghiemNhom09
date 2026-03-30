<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Exports\QuizResultsExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller implements HasMiddleware {

    public static function middleware(): array {
        return [
            function ($request, $next) {
                if (!session()->has('admin')) {
                    return redirect('/login')->with('error', 'Vui lòng đăng nhập quyền Admin.');
                }
                return $next($request);
            },
        ];
    }

    public function dashboard() {
        $data = [
            'userCount' => User::count(),
            'quizCount' => Quiz::count(),
            'avgScore'  => DB::table('results')->avg('score') ?? 0,
        ];
        return view('admin.dashboard', $data);
    }

    // ==========================================
    // QUẢN LÝ SINH VIÊN (USERS)
    // ==========================================
    public function users() {
        if (session('admin')->role !== 'admin') {
            return back()->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        }
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $r) {
        // Validate dữ liệu và xuất câu báo lỗi Tiếng Việt
        $r->validate([
            'student_code' => 'required|string|max:50|unique:users,student_code',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ], [
            'student_code.required' => 'Mã sinh viên không được bỏ trống.',
            'student_code.unique' => 'Mã sinh viên này đã tồn tại! Vui lòng nhập mã khác.',
            'name.required' => 'Họ và tên không được bỏ trống.',
            'email.required' => 'Email không được bỏ trống.',
            'email.unique' => 'Email này đã có người sử dụng!',
            'password.required' => 'Mật khẩu không được bỏ trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        User::create([
            'student_code' => $r->student_code,
            'name' => $r->name,
            'email' => $r->email,
            'password' => md5($r->password), // Dùng MD5 theo database cũ của bạn
            'role' => 'student',
            'survey_done' => 0
        ]);

        return back()->with('success', 'Đã thêm sinh viên mới thành công!');
    }

    public function updateUser(Request $r, $id) {
        $user = User::findOrFail($id);
        
        $r->validate([
            'student_code' => 'required|string|max:50|unique:users,student_code,'.$id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ], [
            'student_code.required' => 'Mã sinh viên không được bỏ trống.',
            'student_code.unique' => 'Mã sinh viên này đã bị trùng với một người khác!',
            'name.required' => 'Họ và tên không được bỏ trống.',
            'email.required' => 'Email không được bỏ trống.',
            'email.unique' => 'Email này đã bị trùng với người khác!'
        ]);

        $user->student_code = $r->student_code;
        $user->name = $r->name;
        $user->email = $r->email;

        // Chỉ cập nhật mật khẩu nếu có nhập pass mới
        if ($r->filled('password')) {
            $user->password = md5($r->password); 
        }

        $user->save();
        return back()->with('success', 'Cập nhật thông tin sinh viên thành công!');
    }

    public function deleteUser($id) {
        if (session('admin')->role !== 'admin') return back();
        User::destroy($id);
        return back()->with('success', 'Xóa người dùng thành công.');
    }

    // ==========================================
    // QUẢN LÝ ĐỀ THI & CÂU HỎI
    // ==========================================
    public function quizzes() {
        $quizzes = Quiz::withCount('questions')->orderBy('id', 'desc')->get();
        return view('admin.quizzes', compact('quizzes'));
    }

    public function storeQuiz(Request $r) {
        $r->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'random_count' => 'nullable|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($r) {
                $quiz = Quiz::create([
                    'title' => $r->title,
                    'duration' => $r->duration
                ]);

                $randomCount = (int) $r->random_count;
                if ($randomCount > 0) {
                    $randomQuestions = Question::with('options')->inRandomOrder()->limit($randomCount)->get();
                    foreach ($randomQuestions as $oldQ) {
                        $newQ = Question::create(['quiz_id' => $quiz->id, 'question' => $oldQ->question]);
                        foreach ($oldQ->options as $oldOpt) {
                            Option::create([
                                'question_id' => $newQ->id,
                                'option_text' => $oldOpt->option_text,
                                'is_correct'  => $oldOpt->is_correct
                            ]);
                        }
                    }
                }
            });
            return back()->with('success', 'Tạo đề thi mới thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function updateQuiz(Request $r, $id) {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['title' => $r->title, 'duration' => $r->duration]);
        return back()->with('success', 'Cập nhật đề thi thành công!');
    }

    public function destroyQuiz($id) {
        Quiz::destroy($id);
        return back()->with('success', 'Đã xóa đề thi vĩnh viễn!');
    }

    public function questions($id) {
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        return view('admin.question', compact('quiz')); 
    }

    public function storeQuestion(Request $r, $id) {
        $r->validate([
            'question' => 'required',
            'options.*' => 'required',
            'correct' => 'required|integer'
        ]);

        try {
            DB::transaction(function () use ($r, $id) {
                $q = Question::create(['quiz_id' => $id, 'question' => $r->question]);
                foreach ($r->options as $key => $optText) {
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $optText,
                        'is_correct'  => ($key == $r->correct) ? 1 : 0
                    ]);
                }
            });
            return back()->with('success', 'Thêm câu hỏi thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function toggleScore($id) {
        $key = 'hide_score_' . $id;
        $current = Cache::get($key, false);
        Cache::put($key, !$current, now()->addDays(30));
        return back()->with('success', 'Đã cập nhật trạng thái hiển thị điểm.');
    }

    public function exportResults($quizId) {
        $quiz = Quiz::findOrFail($quizId);
        $fileName = 'Ket_qua_' . str_replace(' ', '_', $quiz->title) . '_' . date('dmY') . '.xlsx';
        return Excel::download(new QuizResultsExport($quizId), $fileName);
    }

    public function surveys() {
        $surveyedUsers = User::where('role', 'student')->where('survey_done', 1)->get();
        return view('admin.surveys', compact('surveyedUsers'));
    }
}