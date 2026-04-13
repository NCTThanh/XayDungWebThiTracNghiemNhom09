<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Group;
use App\Models\EmailTemplate;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Dữ liệu bảng admins (Quản trị viên tối cao)
        DB::table('admins')->insert([
            ['id' => 1, 'name' => 'Tuyền Nguyễn', 'username' => 'tuyen', 'password' => bcrypt('123456'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hà Phan', 'username' => 'ha', 'password' => bcrypt('123456'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Thùys Thùys', 'username' => 'thuys', 'password' => bcrypt('123456'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Kim Ngân', 'username' => 'kimngan', 'password' => bcrypt('123456'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Bạn', 'username' => 'ban', 'password' => bcrypt('123456'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Dữ liệu bảng giangvien (Nâng cấp: có đăng nhập và password)
        DB::table('giangvien')->insert([
            ['id' => 1, 'name' => 'Nguyễn Văn Thầy 1', 'username' => 'gv01', 'email' => 'gv1@gmail.com', 'password' => bcrypt('123456'), 'department' => 'CNTT', 'role' => 'teacher', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Trần Văn Thầy 2', 'username' => 'gv02', 'email' => 'gv2@gmail.com', 'password' => bcrypt('123456'), 'department' => 'CNTT', 'role' => 'teacher', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Dữ liệu bảng users (Sinh viên)
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Nguyễn Văn A', 'email' => 'a@gmail.com', 'student_code' => 'SV1001', 'class' => 'CTK42', 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Trần Văn B', 'email' => 'b@gmail.com', 'student_code' => 'SV1002', 'class' => 'CTK42', 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'name' => 'Nguyễn Chí Thanh', 'email' => 'thanhdayroi3004@gmail.com', 'student_code' => 'DH522014499', 'class' => null, 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Dữ liệu bảng quiz
        DB::table('quiz')->insert([
            ['id' => 1, 'title' => 'Thi Toán', 'description' => 'Kiểm tra toán', 'duration' => 30, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 14:03:05', 'updated_at' => $now],
            ['id' => 2, 'title' => 'Thi Anh', 'description' => 'Kiểm tra Anh', 'duration' => 20, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 14:03:05', 'updated_at' => $now],
            ['id' => 5, 'title' => 'Thi Cuối Kì', 'description' => 'Tổng hợp', 'duration' => 60, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 15:59:25', 'updated_at' => $now],
        ]);

        // 5. Dữ liệu bảng questions - CÁC QUIZZ ĐỊNH TRƯỚC (Quiz 1, 2, 5)
        DB::table('questions')->insert([
            // Easy - 5 questions
            ['id' => 1, 'quiz_id' => 1, 'question' => '2+2=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'quiz_id' => 1, 'question' => '5*3=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'quiz_id' => 1, 'question' => '10-7=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'quiz_id' => 1, 'question' => '8÷2=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'quiz_id' => 1, 'question' => '9+9=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Medium - 5 questions
            ['id' => 8, 'quiz_id' => 1, 'question' => '15*6=?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'quiz_id' => 1, 'question' => '(8+4)*2=?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'quiz_id' => 1, 'question' => 'x+5=12, x=?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'quiz_id' => 1, 'question' => '√64=?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'quiz_id' => 1, 'question' => '25% của 100=?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            // Hard - 5 questions
            ['id' => 13, 'quiz_id' => 1, 'question' => '2³+3²=?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'quiz_id' => 1, 'question' => '5!= (5 giai thừa)?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'quiz_id' => 1, 'question' => 'log₁₀(1000)=?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'quiz_id' => 1, 'question' => 'sin(90°)=?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'quiz_id' => 1, 'question' => '(a+b)²=?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],

            // TIẾNG ANH (Quiz 2)
            // Easy - 5 questions
            ['id' => 18, 'quiz_id' => 2, 'question' => 'Hello là gì?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'quiz_id' => 2, 'question' => 'Good morning là gì?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'quiz_id' => 2, 'question' => 'Thank you = ?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 21, 'quiz_id' => 2, 'question' => 'Water trong tiếng Việt là?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 22, 'quiz_id' => 2, 'question' => 'Please = ?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Medium - 5 questions
            ['id' => 23, 'quiz_id' => 2, 'question' => 'They are going to school: Thì nào?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 24, 'quiz_id' => 2, 'question' => 'I have been learning for 3 years: thì nào?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 25, 'quiz_id' => 2, 'question' => 'He doesn\'t like coffee: Phủ định đúng?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 26, 'quiz_id' => 2, 'question' => 'Where is the nearest hospital? Từ loại?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 27, 'quiz_id' => 2, 'question' => 'Knowledge là danh từ như thế nào?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
            // Hard - 5 questions
            ['id' => 28, 'quiz_id' => 2, 'question' => 'If I knew the answer, I would tell you: loại câu nào?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 29, 'quiz_id' => 2, 'question' => 'Despite his poverty, he was happy: "despite" là gì?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 30, 'quiz_id' => 2, 'question' => 'The book was written by Shakespeare: thể nào?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 31, 'quiz_id' => 2, 'question' => 'He is so clever that he can solve it: câu phức loại?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 32, 'quiz_id' => 2, 'question' => 'Not until did he apologize: cấu trúc nào?', 'type' => 'single', 'difficulty' => 'hard', 'marks' => 2, 'created_at' => $now, 'updated_at' => $now],

            // THI CUỐI KÌ - MIX (Quiz 5)
            ['id' => 33, 'quiz_id' => 5, 'question' => 'CPU là gì?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 34, 'quiz_id' => 5, 'question' => 'RAM là gì?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 35, 'quiz_id' => 5, 'question' => 'HTML là gì?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 36, 'quiz_id' => 5, 'question' => 'CSS dùng để?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 37, 'quiz_id' => 5, 'question' => 'JavaScript là?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1.5, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 6. Dữ liệu bảng options
        DB::table('options')->insert([
            // Q1: 2+2=?
            ['id' => 1, 'question_id' => 1, 'option_text' => '3', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'question_id' => 1, 'option_text' => '4', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'question_id' => 1, 'option_text' => '5', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'question_id' => 1, 'option_text' => '6', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q2: 5*3=?
            ['id' => 5, 'question_id' => 2, 'option_text' => '10', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'question_id' => 2, 'option_text' => '15', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'question_id' => 2, 'option_text' => '20', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'question_id' => 2, 'option_text' => '25', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q3: Hello là gì?
            ['id' => 9, 'question_id' => 18, 'option_text' => 'Tạm biệt', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'question_id' => 18, 'option_text' => 'Xin chào', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'question_id' => 18, 'option_text' => 'Cảm ơn', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'question_id' => 18, 'option_text' => 'Vâng', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q5: 10-7=?
            ['id' => 13, 'question_id' => 5, 'option_text' => '1', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'question_id' => 5, 'option_text' => '2', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'question_id' => 5, 'option_text' => '3', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'question_id' => 5, 'option_text' => '4', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q6: 8÷2=?
            ['id' => 17, 'question_id' => 6, 'option_text' => '2', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'question_id' => 6, 'option_text' => '3', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'question_id' => 6, 'option_text' => '4', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'question_id' => 6, 'option_text' => '5', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q7: 9+9=?
            ['id' => 21, 'question_id' => 7, 'option_text' => '16', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 22, 'question_id' => 7, 'option_text' => '17', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 23, 'question_id' => 7, 'option_text' => '18', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 24, 'question_id' => 7, 'option_text' => '19', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q8: 15*6=?
            ['id' => 25, 'question_id' => 8, 'option_text' => '80', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 26, 'question_id' => 8, 'option_text' => '85', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 27, 'question_id' => 8, 'option_text' => '90', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 28, 'question_id' => 8, 'option_text' => '95', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q9: (8+4)*2=?
            ['id' => 29, 'question_id' => 9, 'option_text' => '18', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 30, 'question_id' => 9, 'option_text' => '20', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 31, 'question_id' => 9, 'option_text' => '24', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 32, 'question_id' => 9, 'option_text' => '30', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q10: x+5=12, x=?
            ['id' => 33, 'question_id' => 10, 'option_text' => '5', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 34, 'question_id' => 10, 'option_text' => '6', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 35, 'question_id' => 10, 'option_text' => '7', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 36, 'question_id' => 10, 'option_text' => '8', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q11: √64=?
            ['id' => 37, 'question_id' => 11, 'option_text' => '6', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 38, 'question_id' => 11, 'option_text' => '7', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 39, 'question_id' => 11, 'option_text' => '8', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 40, 'question_id' => 11, 'option_text' => '9', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q12: 25% của 100=?
            ['id' => 41, 'question_id' => 12, 'option_text' => '15', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 42, 'question_id' => 12, 'option_text' => '20', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 43, 'question_id' => 12, 'option_text' => '25', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 44, 'question_id' => 12, 'option_text' => '30', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q13: 2³+3²=?
            ['id' => 45, 'question_id' => 13, 'option_text' => '12', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 46, 'question_id' => 13, 'option_text' => '15', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 47, 'question_id' => 13, 'option_text' => '17', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 48, 'question_id' => 13, 'option_text' => '20', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q14: 5! = ?
            ['id' => 49, 'question_id' => 14, 'option_text' => '100', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 50, 'question_id' => 14, 'option_text' => '110', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 51, 'question_id' => 14, 'option_text' => '120', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 52, 'question_id' => 14, 'option_text' => '130', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q15: log₁₀(1000)=?
            ['id' => 53, 'question_id' => 15, 'option_text' => '1', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 54, 'question_id' => 15, 'option_text' => '2', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 55, 'question_id' => 15, 'option_text' => '3', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 56, 'question_id' => 15, 'option_text' => '4', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q16: sin(90°)=?
            ['id' => 57, 'question_id' => 16, 'option_text' => '0', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 58, 'question_id' => 16, 'option_text' => '0.5', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 59, 'question_id' => 16, 'option_text' => '1', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 60, 'question_id' => 16, 'option_text' => '∞', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q17: (a+b)²=?
            ['id' => 61, 'question_id' => 17, 'option_text' => 'a²+b²', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 62, 'question_id' => 17, 'option_text' => 'a²+2ab+b²', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 63, 'question_id' => 17, 'option_text' => '2a+2b', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 64, 'question_id' => 17, 'option_text' => 'a+b', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q19: Good morning là gì?
            ['id' => 65, 'question_id' => 19, 'option_text' => 'Tối nay', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 66, 'question_id' => 19, 'option_text' => 'Buổi sáng tốt lành', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 67, 'question_id' => 19, 'option_text' => 'Buổi chiều tốt lành', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 68, 'question_id' => 19, 'option_text' => 'Đêm tốt', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q20: Thank you = ?
            ['id' => 69, 'question_id' => 20, 'option_text' => 'Không có gì', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 70, 'question_id' => 20, 'option_text' => 'Cảm ơn bạn', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 71, 'question_id' => 20, 'option_text' => 'Bạn ơi', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 72, 'question_id' => 20, 'option_text' => 'Tạm biệt', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q21: Water trong tiếng Việt là?
            ['id' => 73, 'question_id' => 21, 'option_text' => 'Gió', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 74, 'question_id' => 21, 'option_text' => 'Nước', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 75, 'question_id' => 21, 'option_text' => 'Không khí', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 76, 'question_id' => 21, 'option_text' => 'Đất', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q22: Please = ?
            ['id' => 77, 'question_id' => 22, 'option_text' => 'Vui lòng', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 78, 'question_id' => 22, 'option_text' => 'Tạm biệt', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 79, 'question_id' => 22, 'option_text' => 'Cảm ơn', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 80, 'question_id' => 22, 'option_text' => 'Xin lỗi', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q23: They are going to school: Thì nào?
            ['id' => 81, 'question_id' => 23, 'option_text' => 'Simple Present', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 82, 'question_id' => 23, 'option_text' => 'Near Future (be going to)', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 83, 'question_id' => 23, 'option_text' => 'Present Continuous', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 84, 'question_id' => 23, 'option_text' => 'Past Simple', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q24: I have been learning for 3 years: thì nào?
            ['id' => 85, 'question_id' => 24, 'option_text' => 'Present Perfect', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 86, 'question_id' => 24, 'option_text' => 'Present Perfect Continuous', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 87, 'question_id' => 24, 'option_text' => 'Simple Present', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 88, 'question_id' => 24, 'option_text' => 'Past Continuous', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q25: He doesn't like coffee: Phủ định đúng?
            ['id' => 89, 'question_id' => 25, 'option_text' => 'He does not like coffee', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 90, 'question_id' => 25, 'option_text' => 'He is not liking coffee', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 91, 'question_id' => 25, 'option_text' => 'He likes not coffee', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 92, 'question_id' => 25, 'option_text' => 'He not does like coffee', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q26: Where is the nearest hospital? Từ loại?
            ['id' => 93, 'question_id' => 26, 'option_text' => 'Danh từ', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 94, 'question_id' => 26, 'option_text' => 'Động từ', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 95, 'question_id' => 26, 'option_text' => 'Tính từ', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 96, 'question_id' => 26, 'option_text' => 'Trạng từ', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q27: Knowledge là danh từ như thế nào?
            ['id' => 97, 'question_id' => 27, 'option_text' => 'Đếm được', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 98, 'question_id' => 27, 'option_text' => 'Không đếm được', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 99, 'question_id' => 27, 'option_text' => 'Vừa đếm được vừa không', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 100, 'question_id' => 27, 'option_text' => 'Tính từ', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q28: If I knew the answer: loại câu nào?
            ['id' => 101, 'question_id' => 28, 'option_text' => 'Loại 1 (Real)', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 102, 'question_id' => 28, 'option_text' => 'Loại 2 (Unreal Present)', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 103, 'question_id' => 28, 'option_text' => 'Loại 3 (Unreal Past)', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 104, 'question_id' => 28, 'option_text' => 'Câu khẳng định', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q29: Despite his poverty: "despite" là gì?
            ['id' => 105, 'question_id' => 29, 'option_text' => 'Động từ', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 106, 'question_id' => 29, 'option_text' => 'Giới từ', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 107, 'question_id' => 29, 'option_text' => 'Liên từ', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 108, 'question_id' => 29, 'option_text' => 'Tính từ', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q30: The book was written: thể nào?
            ['id' => 109, 'question_id' => 30, 'option_text' => 'Chủ động', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 110, 'question_id' => 30, 'option_text' => 'Bị động', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 111, 'question_id' => 30, 'option_text' => 'Hỗn hợp', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 112, 'question_id' => 30, 'option_text' => 'Trung lập', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q31: He is so clever: loại câu phức?
            ['id' => 113, 'question_id' => 31, 'option_text' => 'Câu lạc mà (asyndetic)', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 114, 'question_id' => 31, 'option_text' => 'Câu phức có liên từ (syndetic)', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 115, 'question_id' => 31, 'option_text' => 'Câu kép chỉ kết quả', 'is_correct' => 1, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 116, 'question_id' => 31, 'option_text' => 'Câu đơn', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q32: Not until did he: cấu trúc nào?
            ['id' => 117, 'question_id' => 32, 'option_text' => 'Inversion', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 118, 'question_id' => 32, 'option_text' => 'Parallel structures', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 119, 'question_id' => 32, 'option_text' => 'Ellipsis', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 120, 'question_id' => 32, 'option_text' => 'Cleft sentence', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q33: CPU là gì?
            ['id' => 121, 'question_id' => 33, 'option_text' => 'Bộ xử lý trung ương', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 122, 'question_id' => 33, 'option_text' => 'Card đồ họa', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 123, 'question_id' => 33, 'option_text' => 'Ổ cứng', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 124, 'question_id' => 33, 'option_text' => 'Nguồn điện', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q34: RAM là gì?
            ['id' => 125, 'question_id' => 34, 'option_text' => 'Bộ nhớ tạm thời', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 126, 'question_id' => 34, 'option_text' => 'Bộ nhớ lâu dài', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 127, 'question_id' => 34, 'option_text' => 'Ổ cứng SSD', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 128, 'question_id' => 34, 'option_text' => 'Bộ xử lý', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q35: HTML là gì?
            ['id' => 129, 'question_id' => 35, 'option_text' => 'Ngôn ngữ đánh dấu siêu văn bản', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 130, 'question_id' => 35, 'option_text' => 'Ngôn ngữ lập trình', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 131, 'question_id' => 35, 'option_text' => 'Cơ sở dữ liệu', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 132, 'question_id' => 35, 'option_text' => 'Hệ điều hành', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q36: CSS dùng để?
            ['id' => 133, 'question_id' => 36, 'option_text' => 'Định dạng giao diện web', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 134, 'question_id' => 36, 'option_text' => 'Xử lý logic', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 135, 'question_id' => 36, 'option_text' => 'Quản lý cơ sở dữ liệu', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 136, 'question_id' => 36, 'option_text' => 'Tạo cấu trúc HTML', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            // Q37: JavaScript là?
            ['id' => 137, 'question_id' => 37, 'option_text' => 'Ngôn ngữ lập trình phía client', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 138, 'question_id' => 37, 'option_text' => 'Cơ sở dữ liệu', 'is_correct' => 0, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 139, 'question_id' => 37, 'option_text' => 'Hệ điều hành', 'is_correct' => 0, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 140, 'question_id' => 37, 'option_text' => 'Ngôn ngữ đánh dấu', 'is_correct' => 0, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
        
        // 7. Dữ liệu bảng savsoft_users (Dữ liệu cũ)
        DB::table('savsoft_users')->insert([
            ['uid' => 1, 'email' => 'a1@gmail.com', 'first_name' => 'An', 'last_name' => 'Nguyen', 'studentid' => 'SV001', 'classid' => 'CTK42', 'facultyid' => 'CNTT'],
            ['uid' => 2, 'email' => 'a2@gmail.com', 'first_name' => 'Binh', 'last_name' => 'Tran', 'studentid' => 'SV002', 'classid' => 'CTK42', 'facultyid' => 'CNTT'],
        ]);

        // 8. Create default groups
        Group::updateOrCreate(['id' => 1], ['name' => 'Default Group', 'description' => 'Default user group']);
        Group::updateOrCreate(['id' => 2], ['name' => 'Premium Users', 'description' => 'Premium subscription users']);
        Group::updateOrCreate(['id' => 3], ['name' => 'Teachers', 'description' => 'Teacher group']);

        // 9. Create default settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Quiz System', 'type' => 'string', 'description' => 'Website name'],
            ['key' => 'site_email', 'value' => 'noreply@quizsystem.com', 'type' => 'string', 'description' => 'System email address'],
            ['key' => 'default_timezone', 'value' => 'Asia/Ho_Chi_Minh', 'type' => 'string', 'description' => 'Default timezone'],
            ['key' => 'default_language', 'value' => 'vi', 'type' => 'string', 'description' => 'Default language'],
            ['key' => 'base_currency', 'value' => 'VND', 'type' => 'string', 'description' => 'Base currency'],
            ['key' => 'default_quiz_duration', 'value' => '60', 'type' => 'integer', 'description' => 'Default quiz duration in minutes'],
            ['key' => 'passing_score', 'value' => '50', 'type' => 'integer', 'description' => 'Minimum score to pass (%)'],
            ['key' => 'max_quiz_attempts', 'value' => '3', 'type' => 'integer', 'description' => 'Maximum quiz attempts per user'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 10. Create default email templates
        EmailTemplate::updateOrCreate(
            ['name' => 'activation'],
            [
                'subject' => 'Xác thực tài khoản',
                'body' => '<html><body><h2>Xin chào {{name}},</h2><p>Vui lòng nhấn nút dưới để xác thực email:</p><p><a href="{{url}}">Xác thực Email</a></p></body></html>',
                'variables' => json_encode(['name', 'email', 'url']),
                'is_active' => true
            ]
        );

        EmailTemplate::updateOrCreate(
            ['name' => 'password_reset'],
            [
                'subject' => 'Đặt lại mật khẩu',
                'body' => '<html><body><h2>Xin chào {{name}},</h2><p><a href="{{url}}">Đặt lại mật khẩu</a></p></body></html>',
                'variables' => json_encode(['name', 'email', 'url']),
                'is_active' => true
            ]
        );

        EmailTemplate::updateOrCreate(
            ['name' => 'quiz_result'],
            [
                'subject' => 'Kết quả bài thi: {{quiz_title}}',
                'body' => '<html><body><h2>Kết quả bài thi</h2><p>Điểm: {{score}}/100</p><p>Kết quả: {{passed}}</p></body></html>',
                'variables' => json_encode(['name', 'quiz_title', 'score', 'passed']),
                'is_active' => true
            ]
        );

        // 11. Seed question bank with 12 subjects and 240 questions
        $this->call(QuestionBankSeeder::class);
    }
}