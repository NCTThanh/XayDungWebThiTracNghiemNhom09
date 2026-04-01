<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('admins')->insert([
            ['id' => 1, 'name' => 'Tuyền Nguyễn', 'username' => 'tuyen', 'password' => '123456', 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hà Phan', 'username' => 'ha', 'password' => '123456', 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Thùys Thùys', 'username' => 'thuys', 'password' => '123456', 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Kim Ngân', 'username' => 'kimngan', 'password' => '123456', 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Bạn', 'username' => 'ban', 'password' => '123456', 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Nguyễn Văn A', 'email' => 'a@gmail.com', 'student_code' => 'SV1001', 'class' => 'CTK42', 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Trần Văn B', 'email' => 'b@gmail.com', 'student_code' => 'SV1002', 'class' => 'CTK42', 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'name' => 'Nguyễn Chí Thanh', 'email' => 'thanhdayroi3004@gmail.com', 'student_code' => 'DH522014499', 'class' => null, 'password' => 'e10adc3949ba59abbe56e057f20f883e', 'role' => 'student', 'survey_done' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('quiz')->insert([
            ['id' => 1, 'title' => 'Thi Toán', 'description' => 'Kiểm tra toán', 'duration' => 30, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 14:03:05', 'updated_at' => $now],
            ['id' => 2, 'title' => 'Thi Anh', 'description' => 'Kiểm tra Anh', 'duration' => 20, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 14:03:05', 'updated_at' => $now],
            ['id' => 5, 'title' => 'Thi Cuối Kì', 'description' => 'Tổng hợp', 'duration' => 60, 'pass_score' => 5.0, 'is_published' => 1, 'created_at' => '2026-03-30 15:59:25', 'updated_at' => $now],
        ]);

        DB::table('questions')->insert([
            ['id' => 1, 'quiz_id' => 1, 'question' => '2+2=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'quiz_id' => 1, 'question' => '5*3=?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'quiz_id' => 2, 'question' => 'Hello nghĩa là gì?', 'type' => 'single', 'difficulty' => 'easy', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'quiz_id' => 3, 'question' => 'CPU là gì?', 'type' => 'single', 'difficulty' => 'medium', 'marks' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('options')->insert([
            ['id' => 1, 'question_id' => 1, 'option_text' => '3', 'is_correct' => 0, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'question_id' => 1, 'option_text' => '4', 'is_correct' => 1, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'question_id' => 2, 'option_text' => '15', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'question_id' => 3, 'option_text' => 'Xin chào', 'is_correct' => 1, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
        
        DB::table('savsoft_users')->insert([
            ['uid' => 1, 'email' => 'a1@gmail.com', 'first_name' => 'An', 'last_name' => 'Nguyen', 'studentid' => 'SV001', 'classid' => 'CTK42', 'facultyid' => 'CNTT'],
            ['uid' => 2, 'email' => 'a2@gmail.com', 'first_name' => 'Binh', 'last_name' => 'Tran', 'studentid' => 'SV002', 'classid' => 'CTK42', 'facultyid' => 'CNTT'],
        ]);
    }
}