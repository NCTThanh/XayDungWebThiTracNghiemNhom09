<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function dashboard()
    {
        $quizzes = Quiz::withCount('questions')->orderBy('id', 'desc')->get();
        return view('student.dashboard', compact('quizzes'));
    }

    public function startExam(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $user = Auth::user() ?? session('user');

        $maxAttempts = 3;
        $attemptCount = ExamAttempt::where('user_id', $user->id)
            ->where('quiz_id', $id)
            ->where('status', 'completed')
            ->count();

        if ($attemptCount >= $maxAttempts) {
            return back()->with('error', 'Bạn đã vượt quá số lần thi cho phép!');
        }

        $totalQuestions = $quiz->questions()->count();
        $numQuestions = min(10, $totalQuestions);

        $questions = Question::where('quiz_id', $id)
            ->inRandomOrder()
            ->take($numQuestions)
            ->with(['options' => function($query) {
                $query->inRandomOrder(); 
            }])
            ->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Đề thi này chưa có câu hỏi!');
        }

        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $id,
            'status' => 'doing',
            'start_time' => now(),
            'score' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'cheat_warnings' => 0
        ]);

        return view('student.exam', compact('quiz', 'questions', 'attempt'));
    }

    public function submitExam(Request $request)
    {
        $quizId = $request->quiz_id;
        $attemptId = $request->attempt_id;
        $userAnswers = $request->input('answers', []); 

        $attempt = ExamAttempt::findOrFail($attemptId);
        $attempt->end_time = now();
        $attempt->status = 'completed';

        $questions = Question::where('quiz_id', $quizId)->get();
        
        $totalScore = 0;
        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $question) {
            $selectedOptionId = $userAnswers[$question->id] ?? null;
            
            $correctOption = Option::where('question_id', $question->id)
                ->where('is_correct', 1)
                ->first();

            $isCorrect = false;
            $earnedMarks = 0;

            if ($correctOption && $selectedOptionId == $correctOption->id) {
                $isCorrect = true;
                $correctCount++;
                $earnedMarks = $question->marks ?? 1;
                $totalScore += $earnedMarks;
            }

            AttemptAnswer::create([
                'attempt_id' => $attemptId,
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
                'earned_marks' => $earnedMarks,
            ]);
        }

        $maxScore = $questions->sum('marks') > 0 ? $questions->sum('marks') : $totalQuestions;
        $score = ($maxScore > 0) ? ($totalScore / $maxScore) * 10 : 0;

        $attempt->score = round($score, 2);
        $attempt->save();

        DB::table('results')->insert([
            'user_id' => Auth::id() ?? session('user')->id,
            'quiz_id' => $quizId,
            'attempt_id' => $attemptId,
            'score' => round($score, 2),
            'is_passed' => $score >= ($attempt->quiz->pass_score ?? 5.0),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/history')->with('success', 
            'Nộp bài thành công! Điểm của bạn là: ' . round($score, 2) . '/10 (' . $correctCount . '/' . $totalQuestions . ' câu đúng)'
        );
    }

    public function history()
    {
        $user = Auth::user() ?? session('user');
        
        $attempts = ExamAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->where('status', 'completed')
            ->orderBy('end_time', 'desc')
            ->paginate(15);

        return view('student.history', compact('attempts'));
    }

    /**
     * SỬA Ở ĐÂY: DÙNG CHUNG CHO CẢ ADMIN VÀ STUDENT
     */
    public function examDetail($attemptId)
    {
        $isAdmin = session()->has('admin');
        $user = Auth::user() ?? session('user');

        if (!$isAdmin && !$user) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để xem chi tiết.');
        }

        $query = ExamAttempt::where('id', $attemptId)
            ->with(['quiz', 'attemptAnswers.question.options', 'attemptAnswers.option']);

        // Nếu là sinh viên thì chỉ được xem bài của mình
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $attempt = $query->firstOrFail();

        return view('student.exam-detail', compact('attempt'));
    }

    public function survey()
    {
        return view('student.survey');
    }

   public function submitSurvey(Request $request)
{
    $user = Auth::user();
    
    // 1. Lưu nội dung khảo sát vào bảng survey_answers
    // Lưu mức độ đánh giá (rating)
    DB::table('survey_answers')->insert([
        'user_id' => $user->id,
        'question_id' => 1, // Quy ước 1 là câu hỏi về độ khó
        'answer' => $request->input('rating'),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Lưu góp ý (feedback) nếu có
    if ($request->filled('feedback')) {
        DB::table('survey_answers')->insert([
            'user_id' => $user->id,
            'question_id' => 2, 
            'answer' => $request->input('feedback'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // 2. Đánh dấu sinh viên đã hoàn thành khảo sát
    /** @var \App\Models\User $user */
    $user->update(['survey_done' => 1]);

    return redirect('/dashboard')->with('success', 'Cảm ơn bạn đã đóng góp ý kiến giúp hệ thống hoàn thiện hơn!');
}

    public function logCheat(Request $request)
    {
        $attemptId = $request->input('attempt_id');
        $attempt = ExamAttempt::find($attemptId);
        
        if ($attempt) {
            $attempt->increment('cheat_warnings');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã ghi nhận hành vi.'
        ]);
    }
}