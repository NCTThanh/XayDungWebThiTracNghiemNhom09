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
    // Thêm dòng này để tự động xóa bảng nếu nó lỡ tồn tại
    Schema::dropIfExists('class_users');
    Schema::dropIfExists('classes');

    Schema::create('classes', function (Blueprint $table) {
        $table->id();
        // Dùng integer thay vì foreignId để khớp với bảng users cũ (INT 11)
        $table->integer('teacher_id'); 
        $table->string('name');
        $table->string('join_code', 10)->unique();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('class_users', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')->constrained()->cascadeOnDelete();
        $table->integer('user_id'); // Khớp với INT của bảng users cũ
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
