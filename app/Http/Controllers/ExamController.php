<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function dashboard()
    {
        // Lấy danh sách quiz mới nhất
        $quizzes = Quiz::withCount('questions')->orderBy('id', 'desc')->get();
        return view('student.dashboard', compact('quizzes'));
    }

    public function startExam($id)
    {
        $quiz = Quiz::findOrFail($id);
        $user = Auth::user();

        // 1. Lấy câu hỏi và xáo trộn đáp án
        $questions = Question::where('quiz_id', $id)
            ->with(['options' => function($query) {
                $query->inRandomOrder(); 
            }])
            ->get();

        // 2. Tạo lượt thi mới (Đảm bảo bảng exam_attempts có các cột này)
        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $id,
            'status' => 'doing'
        ]);

        return view('student.exam', compact('quiz', 'questions', 'attempt'));
    }

    public function submitExam(Request $request)
    {
        $quizId = $request->quiz_id;
        $userAnswers = $request->input('answers', []); 

        // 1. Lấy toàn bộ đáp án đúng của đề thi này
        $correctOptions = Option::whereIn('question_id', function($query) use ($quizId) {
                $query->select('id')->from('questions')->where('quiz_id', $quizId);
            })
            ->where('is_correct', 1)
            ->pluck('id', 'question_id')
            ->toArray();

        $totalQuestions = count($correctOptions);
        $correctCount = 0;

        // 2. So khớp đáp án
        foreach ($userAnswers as $qId => $oId) {
            if (isset($correctOptions[$qId]) && $correctOptions[$qId] == $oId) {
                $correctCount++;
            }
        }

        // 3. Tính điểm (Thang 10)
        $score = ($totalQuestions > 0) ? ($correctCount / $totalQuestions) * 10 : 0;

        // 4. Lưu kết quả vào bảng results
        DB::table('results')->insert([
            'user_id' => Auth::id(), // Dùng Auth::id() để VS Code không báo lỗi đỏ
            'quiz_id' => $quizId,
            'score' => round($score, 2),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/dashboard')->with('success', 'Nộp bài thành công! Điểm của bạn là: ' . round($score, 2));
    }

    public function history()
{
    // Lấy lịch sử từ bảng results, join với bảng quiz để lấy tên đề thi
    $results = DB::table('results')
        ->join('quiz', 'results.quiz_id', '=', 'quiz.id')
        ->where('results.user_id', Auth::id())
        ->select('results.*', 'quiz.title as quiz_title')
        ->orderBy('results.id', 'desc')
        ->get();

    // Sửa 'attempts' thành 'results' ở đây
    return view('student.history', compact('results'));
}

    // FIX LỖI: Bổ sung hàm survey() bị thiếu khiến trang bị Error
    public function survey()
    {
        return view('student.survey');
    }

    public function submitSurvey(Request $request)
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $user->update(['survey_done' => 1]);

        return redirect('/dashboard')->with('success', 'Cảm ơn bạn đã hoàn thành khảo sát!');
    }

    public function logCheat(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Đã ghi nhận hành vi.'
        ]);
    }
}