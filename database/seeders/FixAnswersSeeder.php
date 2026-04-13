<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixAnswersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DỌN DẸP RÁC TRONG BẢNG OPTIONS
        $options = DB::table('options')->get();
        $cleanedCount = 0;

        foreach ($options as $opt) {
            // Cắt bỏ các chuỗi thừa do Seeder cũ tạo ra
            $garbage = [' (Đáp án đúng)', ' (Lựa chọn sai)', '(Đáp án đúng)', '(Lựa chọn sai)'];
            $cleanText = str_replace($garbage, '', $opt->option_text);
            
            // Nếu có sự thay đổi thì update lại
            if ($cleanText !== $opt->option_text) {
                DB::table('options')
                    ->where('id', $opt->id)
                    ->update(['option_text' => trim($cleanText)]);
                $cleanedCount++;
            }
        }
        $this->command->info("Đã dọn dẹp sạch sẽ chữ thừa cho {$cleanedCount} đáp án.");

        // 2. ĐỒNG BỘ ĐÁP ÁN VÀO BẢNG ANSWERS
        // Làm sạch bảng answers trước để tránh bị duplicate nếu chạy nhiều lần
        DB::table('answers')->truncate();

        // Lấy toàn bộ các đáp án đúng (is_correct = 1)
        $correctOptions = DB::table('options')->where('is_correct', 1)->get();
        $answersData = [];

        foreach ($correctOptions as $co) {
            $answersData[] = [
                'question_id'       => $co->question_id,
                'correct_option_id' => $co->id,
                'explanation'       => 'Hệ thống tự động đồng bộ đáp án.',
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // Insert vào bảng answers (chia nhỏ 200 dòng/lần cho nhẹ máy)
        foreach (array_chunk($answersData, 200) as $chunk) {
            DB::table('answers')->insert($chunk);
        }
        
        $this->command->info("Đã tạo full " . count($answersData) . " đáp án vào bảng answers!");
    }
}