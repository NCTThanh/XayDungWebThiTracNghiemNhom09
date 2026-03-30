<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->string('file_path')->nullable(); // PDF đề bài nếu là tự luận
            $table->integer('duration'); // Thời gian làm bài (phút)
            $table->dateTime('start_time')->nullable(); // Thời gian bắt đầu mở form
            $table->dateTime('end_time')->nullable(); // Hạn chót nộp bài
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->boolean('show_score')->default(false); // Admin/Teacher có thể ẩn/hiện điểm
            $table->boolean('allow_retry')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
