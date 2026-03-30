<?php

namespace App\Exports;

use App\Models\ExamAttempt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class QuizResultsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $quizId;

    public function __construct($quizId) {
        $this->quizId = $quizId;
    }

    // 1. Lấy dữ liệu kèm theo quan hệ để tránh lỗi N+1
    public function collection() {
        return ExamAttempt::with(['user', 'quiz'])
            ->where('quiz_id', $this->quizId)
            ->where('status', 'done')
            ->get();
    }

    // 2. Định nghĩa tiêu đề cột
    public function headings(): array {
        return [
            'STT',
            'Mã Sinh Viên',
            'Họ và Tên',
            'Đề Thi',
            'Thời Gian Bắt Đầu',
            'Thời Gian Nộp',
            'Số Lần Cảnh Báo',
            'Điểm Số (Hệ 10)'
        ];
    }

    // 3. Map dữ liệu vào từng cột
    public function map($attempt): array {
        static $index = 0;
        return [
            ++$index,
            $attempt->user->student_code,
            $attempt->user->name,
            $attempt->quiz->title,
            $attempt->start_time->format('H:i:s d/m/Y'),
            $attempt->end_time ? $attempt->end_time->format('H:i:s d/m/Y') : 'N/A',
            $attempt->cheat_warnings,
            number_format($attempt->score, 2)
        ];
    }
}