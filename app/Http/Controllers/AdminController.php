<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\Subject;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Exports\QuizResultsExport;
use App\Services\AiQuestionService;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller implements HasMiddleware {

    public static function middleware(): array {
        return [
            function ($request, $next) {
                if (!session()->has('admin')) {
                    return redirect('/login')->with('error', 'Vui lòng đăng nhập quyền Admin hoặc Giảng viên.');
                }
                return $next($request);
            },
        ];
    }

    public function dashboard() {
        $data = [
            'userCount' => User::where('role', 'student')->count(),
            'quizCount' => Quiz::count(),
            'totalAttempts' => ExamAttempt::where('status', 'completed')->count(),
            'avgScore' => ExamAttempt::where('status', 'completed')->avg('score') ?? 0,
        ];
        return view('admin.dashboard', $data);
    }
   // ==========================================
    // HỒ SƠ & ĐỔI MẬT KHẨU ADMIN/TEACHER
    // ==========================================
    public function profile() {
        $adminSession = session('admin');
        
        // Xác định bảng cần truy vấn dựa trên role
        $table = ($adminSession->role === 'admin') ? 'admins' : 'giangvien';
        
        // Lấy dữ liệu mới nhất từ DB
        $admin = DB::table($table)->where('id', $adminSession->id)->first();
        
        return view('admin.profile', compact('admin'));
    }

    public function changePassword(Request $request) {
        $adminSession = session('admin');
        
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Mật khẩu nhập lại không khớp!'
        ]);

        // 1. TỰ ĐỘNG DÒ TÌM BẢNG (Bỏ qua biến role để chống lỗi tuyệt đối)
        $table = 'admins';
        $account = DB::table('admins')->where('id', $adminSession->id)->first();
        
        // Nếu không có trong bảng admins, tự động chuyển sang quét bảng giangvien
        if (!$account) {
            $table = 'giangvien';
            $account = DB::table('giangvien')->where('id', $adminSession->id)->first();
        }

        // Nếu quét cả 2 bảng đều không ra (Trường hợp hiếm)
        if (!$account) {
            return back()->with('error', 'Lỗi: Không tìm thấy tài khoản của bạn trong CSDL!');
        }

        // 2. THUẬT TOÁN KIỂM TRA MẬT KHẨU "BAO TRỌN GÓI"
        $currentInput = trim($request->current_password);
        $dbPassword = $account->password;
        $isMatch = false;

        // Kịch bản A: Trong DB đang lưu mã hóa MD5
        if (md5($currentInput) === $dbPassword) {
            $isMatch = true;
        } 
        // Kịch bản B: Trong DB đang lưu chữ thường (chưa mã hóa)
        elseif ($currentInput === $dbPassword) {
            $isMatch = true;
        } 
        // Kịch bản C: Trong DB đang lưu mã hóa Bcrypt của Laravel
        elseif (\Illuminate\Support\Facades\Hash::check($currentInput, $dbPassword)) {
            $isMatch = true;
        }

        if (!$isMatch) {
            return back()->with('error', 'Mật khẩu hiện tại không chính xác!');
        }

        // 3. LƯU MẬT KHẨU MỚI (Ép về định dạng MD5 để đồng bộ với toàn hệ thống)
        DB::table($table)
            ->where('id', $account->id)
            ->update([
                'password'   => md5(trim($request->new_password)),
                'updated_at' => now()
            ]);

        return back()->with('success', 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới nhé.');
    }
    // ==========================================
    // QUẢN LÝ SINH VIÊN
    // ==========================================
   public function users() {
        $admin = session('admin');
        if (!in_array($admin->role ?? '', ['admin', 'teacher'])) {
            return back()->with('error', 'Bạn không có quyền xem danh sách sinh viên.');
        }
        
        
        $users = User::orderBy('id', 'desc')->paginate(10);
        
        return view('admin.users', compact('users'));
    }
    public function storeUser(Request $r) {
        if (session('admin')->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới được thêm sinh viên.');
        }

        $r->validate([
            'student_code' => 'required|string|max:50|unique:users,student_code',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6'
        ]);

        User::create([
            'student_code' => $r->student_code,
            'name'         => $r->name,
            'email'        => $r->email,
            'password'     => md5($r->password),
            'role'         => 'student',
            'survey_done'  => 0
        ]);

        return back()->with('success', 'Đã thêm sinh viên mới thành công!');
    }

    public function updateUser(Request $r, $id) {
        $user = User::findOrFail($id);
        $r->validate([
            'student_code' => 'required|string|max:50|unique:users,student_code,'.$id,
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,'.$id,
        ]);

        $user->student_code = $r->student_code;
        $user->name         = $r->name;
        $user->email        = $r->email;

        if ($r->filled('password')) {
            $user->password = md5($r->password);
        }

        $user->save();
        return back()->with('success', 'Cập nhật thông tin sinh viên thành công!');
    }

    public function deleteUser($id) {
        if (session('admin')->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới được xóa.');
        }
        User::destroy($id);
        return back()->with('success', 'Xóa người dùng thành công.');
    }

    // ==========================================
    // QUẢN LÝ ĐỀ THI
    // ==========================================
    public function quizzes() {
        $quizzes = Quiz::withCount(['questions', 'examAttempts'])
            ->orderBy('id', 'desc')
            ->get();
        
        // Load subjects with question count
        $subjects = \App\Models\Subject::withCount('questions')->get();
        
        return view('admin.quizzes', compact('quizzes', 'subjects'));
    }

    public function getQuestionsBySubject($subjectId) {
        $questions = \App\Models\Question::where('subject_id', $subjectId)
            ->select('id', 'question', 'difficulty', 'marks')
            ->get();
        
        return response()->json(['questions' => $questions]);
    }

    public function storeQuiz(Request $r) {
        $r->validate([
            'title'    => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'pass_score' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string',
            'question_ids' => 'nullable|json'
        ]);

        $quiz = Quiz::create([
            'title'    => $r->title,
            'duration' => $r->duration,
            'pass_score' => $r->pass_score ?? 5.0,
            'description' => $r->description ?? '',
            'is_published' => 1
        ]);

        // Handle bulk question addition if question_ids are provided
        if ($r->question_ids) {
            try {
                $questionIds = json_decode($r->question_ids, true);
                if (is_array($questionIds) && count($questionIds) > 0) {
                    // Get selected questions from question bank and attach to quiz
                    $bankQuestions = \App\Models\Question::whereIn('id', $questionIds)->get();
                    foreach ($bankQuestions as $bankQuestion) {
                        // Create a new question linked to this quiz
                        $newQuestion = $quiz->questions()->create([
                            'subject_id' => $bankQuestion->subject_id,
                            'question' => $bankQuestion->question,
                            'type' => $bankQuestion->type,
                            'difficulty' => $bankQuestion->difficulty,
                            'marks' => $bankQuestion->marks,
                        ]);
                        
                        // Copy options from bank question
                        foreach ($bankQuestion->options as $option) {
                            $newQuestion->options()->create([
                                'option_text' => $option->option_text,
                                'is_correct'  => $option->is_correct,
                                'order' => $option->order
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail quiz creation
                Log::error('Error adding questions to quiz: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Tạo đề thi mới thành công!');
    }

    public function updateQuiz(Request $r, $id) {
        $quiz = Quiz::findOrFail($id);
        $r->validate([
            'title'    => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'pass_score' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string'
        ]);
        
        $quiz->update([
            'title'    => $r->title,
            'duration' => $r->duration,
            'pass_score' => $r->pass_score ?? $quiz->pass_score,
            'description' => $r->description ?? $quiz->description,
        ]);

        return back()->with('success', 'Cập nhật đề thi thành công!');
    }

    public function destroyQuiz($id) {
        Quiz::destroy($id);
        return back()->with('success', 'Đã xóa đề thi thành công!');
    }

    public function questions($id) {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('admin.question', compact('quiz'));
    }

    public function storeQuestion(Request $r, $id) {
        $r->validate([
            'question'   => 'required|string',
            'options'    => 'required|array|min:2',
            'options.*'  => 'required|string',
            'correct'    => 'required|integer',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'marks'      => 'nullable|numeric|min:0.5|max:10'
        ]);

        $q = Question::create([
            'quiz_id'  => $id,
            'question' => $r->question,
            'difficulty' => $r->difficulty ?? 'medium',
            'marks' => $r->marks ?? 1.0,
        ]);

        foreach ($r->options as $key => $text) {
            Option::create([
                'question_id' => $q->id,
                'option_text' => $text,
                'is_correct'  => ($key == $r->correct),
                'order' => $key + 1
            ]);
        }

        return back()->with('success', 'Thêm câu hỏi thành công!');
    }

    /**
     * Xóa câu hỏi
     */
    public function deleteQuestion($id) {
        $question = Question::findOrFail($id);
        $quizId = $question->quiz_id;
        
        // Xóa tất cả đáp án liên quan
        Option::where('question_id', $id)->delete();
        // Xóa câu hỏi
        $question->delete();

        return back()->with('success', 'Đã xóa câu hỏi thành công!');
    }

    // ==========================================
    // PHÁT SINH CÂU HỎI BẰNG AI
    // ==========================================
    public function aiGenerateForm($quizId = null) {
        $quizzes = Quiz::orderBy('title')->get();
        return view('admin.ai-generate', compact('quizzes', 'quizId'));
    }

    public function aiGenerateQuestions(Request $request) {
        $request->validate([
            'quiz_id' => 'required|exists:quiz,id',
            'topic' => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:20',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        try {
            $aiService = new AiQuestionService();
            $quizId = $request->quiz_id;
            $topic = $request->topic;
            $numQuestions = $request->num_questions;
            $difficulty = $request->difficulty ?? 'medium';

            // Phát sinh câu hỏi dựa trên loại khó
            $questions = [];
            for ($i = 0; $i < $numQuestions; $i++) {
                try {
                    if ($difficulty === 'easy') {
                        $question = $aiService->generateMultipleChoice($topic);
                    } elseif ($difficulty === 'hard') {
                        $question = $aiService->generateEssay($topic);
                    } else {
                        $question = $aiService->generateTrueFalse($topic);
                    }
                    
                    if ($question) {
                        $questions[] = $question;
                    }
                } catch (\Exception $e) {
                    // Log error nhưng tiếp tục phát sinh câu tiếp theo
                    continue;
                }
            }

            if (empty($questions)) {
                return back()->with('error', 'Không thể phát sinh câu hỏi. Vui lòng thử lại!');
            }

            // Lưu câu hỏi vào database
            $savedCount = 0;
            foreach ($questions as $questionData) {
                if (!isset($questionData['question']) || !isset($questionData['options'])) {
                    continue;
                }

                $q = Question::create([
                    'quiz_id' => $quizId,
                    'question' => $questionData['question'],
                    'difficulty' => $difficulty,
                    'marks' => 1.0,
                ]);

                // Lưu các đáp án
                if (!empty($questionData['options']) && is_array($questionData['options'])) {
                    $correctIndex = $questionData['correct_index'] ?? 0;
                    foreach ($questionData['options'] as $index => $option) {
                        Option::create([
                            'question_id' => $q->id,
                            'option_text' => $option,
                            'is_correct' => ($index == $correctIndex),
                            'order' => $index + 1
                        ]);
                    }
                    $savedCount++;
                }
            }

            return back()->with('success', "Đã phát sinh thành công $savedCount câu hỏi bằng AI!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // ==========================================
    // THỐNG KÊ KẾT QUẢ
    // ==========================================
    public function results() {
        $admin = session('admin');
        if (!in_array($admin->role ?? '', ['admin', 'teacher'])) {
            return back()->with('error', 'Bạn không có quyền xem kết quả.');
        }

        $quizzes = Quiz::orderBy('title')->get();
        return view('admin.results', compact('quizzes'));
    }

    public function quizResults($quizId) {
        $quiz = Quiz::findOrFail($quizId);
        
        // Lấy tất cả lượt thi của đề thi này
        $attempts = ExamAttempt::where('quiz_id', $quizId)
            ->where('status', 'completed')
            ->with('user', 'quiz')
            ->orderBy('end_time', 'desc')
            ->get();

        // Tính toán thống kê
        $stats = [
            'total_attempts' => $attempts->count(),
            'passed_count' => $attempts->where('score', '>=', $quiz->pass_score ?? 5.0)->count(),
            'avg_score' => $attempts->avg('score') ?? 0,
            'min_score' => $attempts->min('score') ?? 0,
            'max_score' => $attempts->max('score') ?? 0,
            'avg_time' => $attempts->map(function($a) {
                if ($a->start_time && $a->end_time) {
                    return $a->end_time->diffInSeconds($a->start_time);
                }
                return 0;
            })->average(),
        ];

        $stats['pass_rate'] = ($stats['total_attempts'] > 0) 
            ? round(($stats['passed_count'] / $stats['total_attempts']) * 100, 2)
            : 0;

        return view('admin.quiz-results', compact('quiz', 'attempts', 'stats'));
    }

    public function userResults($userId) {
        $user = User::findOrFail($userId);
        
        // Lấy tất cả lượt thi của sinh viên này
        $attempts = ExamAttempt::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('quiz')
            ->orderBy('end_time', 'desc')
            ->get();

        return view('admin.user-results', compact('user', 'attempts'));
    }

    // ==========================================
    // ĐIỂM DANH
    // ==========================================
 public function attendance()
    {
        $admin = session('admin');
        $query = DB::table('attendance_sessions')
            ->select('attendance_sessions.*')
            ->selectRaw('(SELECT COUNT(*) FROM attendance_records WHERE attendance_records.session_id = attendance_sessions.id) as records_count');

        if ($admin->role === 'teacher') {
            $query->where('created_by', $admin->id);
        }

        $sessions = $query->orderBy('created_at', 'desc')->get();
        return view('admin.attendance', compact('sessions'));
    }
    // ==========================================
    // KHẢO SÁT
    // ==========================================
    public function surveys() {
   
    $feedbacks = DB::table('survey_answers')
                ->join('users', 'survey_answers.user_id', '=', 'users.id')
                ->select('users.name', 'users.student_code', 'survey_answers.*')
                ->orderBy('survey_answers.created_at', 'desc')
                ->get()
                ->groupBy('user_id'); // Nhóm theo từng sinh viên

    return view('admin.surveys', compact('feedbacks'));
}
public function attendanceDetail($id)
    {
        $admin = session('admin');
        $session = DB::table('attendance_sessions')->where('id', $id)->first();
        
        if (!$session) return back()->with('error', 'Không tìm thấy phiên điểm danh');

        if ($admin->role === 'teacher' && $session->created_by !== $admin->id) {
            return back()->with('error', 'Bạn không có quyền xem chi tiết phiên điểm danh này!');
        }

        $records = DB::table('attendance_records')
            ->join('users', 'attendance_records.user_id', '=', 'users.id')
            ->where('attendance_records.session_id', $id)
            ->select('attendance_records.*', 'users.name', 'users.student_code', 'users.email')
            ->orderBy('attendance_records.scan_time', 'asc')
            ->get();

        return view('admin.attendance-detail', compact('session', 'records'));
    }

    // ==========================================
    // CÁC HÀM KHÁC
    // ==========================================
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
}