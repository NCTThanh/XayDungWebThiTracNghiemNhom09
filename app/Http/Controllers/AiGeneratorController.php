<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;

class AiGeneratorController extends Controller
{
    public function generateQuestions(Request $request)
    {
        $request->validate([
            'topic'         => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:20',
            'difficulty'    => 'required|string',
            'quiz_id'       => 'required|exists:quiz,id'
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'Chưa cấu hình GEMINI_API_KEY trong file .env');
        }

        $prompt = "Tạo {$request->num_questions} câu hỏi trắc nghiệm về chủ đề: {$request->topic}. Mức độ: {$request->difficulty}.\n"
                . "Mỗi câu hỏi có đúng 4 đáp án. \n"
                . "Trả về CHỈ một object JSON, đúng định dạng sau:\n"
                . "{\"questions\": [{\"question\": \"Nội dung câu hỏi...\", \"options\": [\"Đáp án A\", \"Đáp án B\", \"Đáp án C\", \"Đáp án D\"], \"correct\": \"A\", \"explanation\": \"Giải thích ngắn gọn tại sao A đúng\"}]}";

        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $errorMsg = $data['error']['message'] ?? 'Gemini API từ chối phản hồi.';
                return back()->with('error', 'Lỗi từ Gemini: ' . $errorMsg);
            }

            $jsonText = $data['candidates'][0]['content']['parts'][0]['text'];
            
            // Dọn rác Markdown
            $jsonText = preg_replace('/```json\s*/i', '', $jsonText);
            $jsonText = preg_replace('/```\s*/i', '', $jsonText);
            $jsonText = trim($jsonText);

            $generated = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($generated['questions'])) {
                return back()->with('error', 'AI trả về dữ liệu không đúng chuẩn JSON. Vui lòng nhấn nút phát sinh lại.');
            }

            DB::beginTransaction();
            try {
                $quizId = $request->quiz_id;

                foreach ($generated['questions'] as $q) {
                    $question = Question::create([
                        'quiz_id'  => $quizId,
                        'question' => $q['question'],
                    ]);

                    $correctLetter = strtoupper(substr(trim($q['correct'] ?? 'A'), 0, 1));
                    $options = $q['options'] ?? [];
                    
                    if (count($options) < 4) {
                        $options = array_pad($options, 4, "Đáp án chưa xác định");
                    }

                    $correctOptionId = null;

                    // Tạo options
                    foreach (array_slice($options, 0, 4) as $index => $optText) {
                        $letter = chr(65 + $index); // A, B, C, D
                        
                        // Làm sạch: lỡ AI sinh ra "A. Đáp án 1" thì xóa chữ "A. " đi
                        $cleanOptText = preg_replace('/^[A-D][\.\:\)]\s*/i', '', $optText);
                        
                        $isCorrect = ($letter === $correctLetter);

                        $optModel = Option::create([
                            'question_id' => $question->id,
                            'option_text' => $cleanOptText,
                            'is_correct'  => $isCorrect,
                        ]);

                        // Lưu lại ID của đáp án đúng
                        if ($isCorrect) {
                            $correctOptionId = $optModel->id;
                        }
                    }

                    // SỬA LỖI Ở ĐÂY: Lưu vào bảng `answers` để đồng bộ
                    if ($correctOptionId) {
                        DB::table('answers')->insert([
                            'question_id'       => $question->id,
                            'correct_option_id' => $correctOptionId,
                            'explanation'       => $q['explanation'] ?? 'AI không cung cấp giải thích.',
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                    }
                }
                
                DB::commit();
                return back()->with('success', "Đã tạo thành công {$request->num_questions} câu hỏi từ AI và lưu đáp án chuẩn!");

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Lỗi Database (Kiểm tra lại Model/Migration): ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Không thể kết nối đến Google Gemini: ' . $e->getMessage());
        }
    }
}