<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Option;
// Giả định bạn đã có Models: Quiz, Question, Result

class QuizController extends Controller
{
    // API Nộp bài và chấm điểm tự động
    public function submitQuiz(Request $request, $quizId)
    {
        $user = Auth::user();
        
        // $request->answers có dạng mảng: [ question_id => option_id, ... ]
        $answers = $request->input('answers'); 
        
        if (!$answers || count($answers) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Bạn chưa chọn đáp án nào.']);
        }

        $score = 0;
        $totalQuestions = count($answers);

        // Bắt đầu Transaction để lưu điểm an toàn
        DB::beginTransaction();
        try {
            foreach ($answers as $qId => $optId) {
                // Kiểm tra xem option được chọn có phải đáp án đúng không (score = 1)
                $isCorrect = DB::table('options')
                    ->where('id', $optId)
                    ->where('question_id', $qId)
                    ->value('score');

                if ($isCorrect > 0) {
                    $score++;
                }
            }

            $percentage = ($score / $totalQuestions) * 100;
            $status = $percentage >= 50 ? 'Pass' : 'Fail'; // Pass nếu >= 50%

            // Lưu kết quả vào bảng results
            $resultId = DB::table('results')->insertGetId([
                'user_id' => $user->id,
                'quiz_id' => $quizId,
                'score_obtained' => $score,
                'percentage' => $percentage,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // TÍCH HỢP EMAIL: Gửi email thông báo kết quả (Có thể làm Job Queue để web không bị chậm)
            // Mail::to($user->email)->send(new \App\Mail\QuizResultMail($quizId, $score));

            return response()->json([
                'status' => 'success', 
                'message' => 'Nộp bài thành công!',
                'data' => [
                    'score' => $score,
                    'total' => $totalQuestions,
                    'percentage' => $percentage,
                    'result' => $status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
}
