<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. CÁC BẢNG HỆ THỐNG LARAVEL & CI3
        Schema::create('cache', function (Blueprint $table) { $table->string('key')->primary(); $table->mediumText('value'); $table->integer('expiration'); });
        Schema::create('cache_locks', function (Blueprint $table) { $table->string('key')->primary(); $table->string('owner'); $table->integer('expiration'); });
        Schema::create('jobs', function (Blueprint $table) { $table->id(); $table->string('queue')->index(); $table->longText('payload'); $table->unsignedTinyInteger('attempts'); $table->unsignedInteger('reserved_at')->nullable(); $table->unsignedInteger('available_at'); $table->unsignedInteger('created_at'); });
        Schema::create('job_batches', function (Blueprint $table) { $table->string('id')->primary(); $table->string('name'); $table->integer('total_jobs'); $table->integer('pending_jobs'); $table->integer('failed_jobs'); $table->longText('failed_job_ids'); $table->mediumText('options')->nullable(); $table->integer('cancelled_at')->nullable(); $table->integer('created_at'); $table->integer('finished_at')->nullable(); });
        Schema::create('failed_jobs', function (Blueprint $table) { $table->id(); $table->string('uuid')->unique(); $table->text('connection'); $table->text('queue'); $table->longText('payload'); $table->longText('exception'); $table->timestamp('failed_at')->useCurrent(); });
        Schema::create('sessions', function (Blueprint $table) { $table->string('id')->primary(); $table->foreignId('user_id')->nullable()->index(); $table->string('ip_address', 45)->nullable(); $table->text('user_agent')->nullable(); $table->longText('payload'); $table->integer('last_activity')->index(); });
        Schema::create('ci_sessions', function (Blueprint $table) { $table->string('id', 40)->primary(); $table->string('ip_address', 45); $table->timestamp('timestamp')->useCurrent(); $table->text('data'); });

        // 2. CÁC BẢNG QUẢN LÝ NGƯỜI DÙNG & HỌC THUẬT
        Schema::create('admins', function (Blueprint $table) { $table->id(); $table->string('name', 100)->nullable(); $table->string('username', 50)->unique(); $table->string('password', 255); $table->string('role', 20)->default('admin'); $table->rememberToken(); $table->timestamps(); });
        Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('name', 100); $table->string('email', 100)->unique(); $table->string('student_code', 20)->nullable()->unique(); $table->string('class', 20)->nullable(); $table->string('password', 255); $table->string('role', 20)->default('student'); $table->tinyInteger('survey_done')->default(0); $table->boolean('is_active')->default(true); $table->rememberToken(); $table->timestamps(); });
        Schema::create('giangvien', function (Blueprint $table) { 
    $table->id(); 
    $table->string('name', 100); 
    $table->string('username', 50)->unique(); 
    $table->string('email', 100)->unique(); 
    $table->string('password', 255);          
    $table->string('department', 100)->nullable(); 
    $table->string('phone', 20)->nullable(); 
    $table->string('role', 20)->default('teacher'); 
    $table->rememberToken();
    $table->timestamps(); 
});
        Schema::create('school_year', function (Blueprint $table) { $table->string('year', 9); $table->integer('semester'); $table->boolean('status')->default(true); $table->primary(['year', 'semester']); $table->timestamps(); });
        Schema::create('subjects', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->timestamps(); });
        Schema::create('classes', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('teacher_id'); $table->string('name'); $table->string('join_code', 10)->unique(); $table->text('description')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); });
        Schema::create('class_users', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('class_id'); $table->unsignedBigInteger('user_id'); $table->timestamps(); });

        // 3. CÁC BẢNG HỆ THỐNG THI TRẮC NGHIỆM
        Schema::create('quiz', function (Blueprint $table) { $table->id(); $table->string('title', 255); $table->text('description')->nullable(); $table->integer('duration'); $table->float('pass_score')->nullable(); $table->dateTime('start_time')->nullable(); $table->dateTime('end_time')->nullable(); $table->boolean('is_published')->default(true); $table->timestamps(); $table->softDeletes(); });
        Schema::create('questions', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('quiz_id'); $table->text('question'); $table->string('image_url')->nullable(); $table->enum('type', ['single', 'multiple', 'text'])->default('single'); $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium'); $table->float('marks')->default(1.0); $table->timestamps(); });
        Schema::create('options', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('question_id'); $table->text('option_text'); $table->boolean('is_correct')->default(false); $table->integer('order')->default(0); $table->timestamps(); });
        Schema::create('answers', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('question_id'); $table->unsignedBigInteger('correct_option_id'); $table->text('explanation')->nullable(); $table->timestamps(); });
        Schema::create('exam_attempts', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('quiz_id'); $table->timestamp('start_time')->useCurrent(); $table->timestamp('end_time')->nullable(); $table->float('score')->default(0); $table->string('status', 20)->default('doing'); $table->string('ip_address', 45)->nullable(); $table->text('user_agent')->nullable(); $table->timestamps(); });
        Schema::create('attempt_questions', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('attempt_id'); $table->unsignedBigInteger('question_id'); $table->timestamps(); });
        Schema::create('attempt_answers', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('attempt_id'); $table->unsignedBigInteger('question_id'); $table->unsignedBigInteger('option_id')->nullable(); $table->boolean('is_correct')->default(false); $table->float('earned_marks')->default(0); $table->timestamps(); });
        Schema::create('user_answers', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('question_id'); $table->unsignedBigInteger('option_id')->nullable(); $table->timestamps(); });
        Schema::create('results', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('quiz_id'); $table->unsignedBigInteger('attempt_id')->nullable(); $table->float('score'); $table->boolean('is_passed')->nullable(); $table->text('teacher_feedback')->nullable(); $table->timestamps(); });
        Schema::create('submissions', function (Blueprint $table) { $table->id(); $table->integer('attempt_id'); $table->string('file_path'); $table->text('teacher_feedback')->nullable(); $table->decimal('manual_score', 5, 2)->nullable(); $table->timestamps(); });

        // 4. CÁC BẢNG KHẢO SÁT, THÔNG BÁO VÀ HỆ THỐNG SAVSOFT CŨ
        Schema::create('survey', function (Blueprint $table) { $table->id(); $table->string('title', 255); $table->text('description')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); });
        Schema::create('survey_questions', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('survey_id'); $table->text('question'); $table->timestamps(); });
        Schema::create('survey_answers', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('question_id'); $table->text('answer'); $table->timestamps(); });
        Schema::create('notifications', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('user_id'); $table->string('title'); $table->text('content'); $table->boolean('is_read')->default(false); $table->timestamps(); });
        Schema::create('savsoft_users', function (Blueprint $table) { $table->id('uid'); $table->string('email', 100)->nullable(); $table->string('first_name', 100)->nullable(); $table->string('last_name', 100)->nullable(); $table->string('studentid', 10)->nullable(); $table->string('classid', 10)->nullable(); $table->string('facultyid', 10)->nullable(); });
        Schema::create('savsoft_attendance', function (Blueprint $table) { $table->id(); $table->string('studentid', 12)->nullable(); $table->string('subject', 255)->nullable(); $table->timestamp('time1')->useCurrent(); $table->string('info_staff', 80)->nullable(); $table->integer('cid')->default(0); });
        Schema::create('savsoft_faculty', function (Blueprint $table) { $table->string('facultyid', 10)->primary(); $table->string('facultyname', 50)->nullable(); });
        Schema::create('savsoft_class', function (Blueprint $table) { $table->string('classid', 20)->primary(); $table->string('facultyid', 10); });
    }

    public function down() {
        // Rollback sẽ xóa tất cả
        Schema::dropIfExists('savsoft_class'); Schema::dropIfExists('savsoft_faculty'); Schema::dropIfExists('savsoft_attendance'); Schema::dropIfExists('savsoft_users');
        Schema::dropIfExists('notifications'); Schema::dropIfExists('survey_answers'); Schema::dropIfExists('survey_questions'); Schema::dropIfExists('survey');
        Schema::dropIfExists('submissions'); Schema::dropIfExists('results'); Schema::dropIfExists('user_answers'); Schema::dropIfExists('attempt_answers'); Schema::dropIfExists('attempt_questions'); Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('answers'); Schema::dropIfExists('options'); Schema::dropIfExists('questions'); Schema::dropIfExists('quiz');
        Schema::dropIfExists('class_users'); Schema::dropIfExists('classes'); Schema::dropIfExists('subjects'); Schema::dropIfExists('school_year'); Schema::dropIfExists('giangvien'); Schema::dropIfExists('users'); Schema::dropIfExists('admins');
        Schema::dropIfExists('ci_sessions'); Schema::dropIfExists('sessions'); Schema::dropIfExists('failed_jobs'); Schema::dropIfExists('job_batches'); Schema::dropIfExists('jobs'); Schema::dropIfExists('cache_locks'); Schema::dropIfExists('cache');
    }
};