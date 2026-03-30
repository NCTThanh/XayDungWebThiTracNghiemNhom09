<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::dropIfExists('submissions'); 
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        
        $table->integer('attempt_id'); 
        $table->string('file_path');
        $table->text('teacher_feedback')->nullable();
        $table->decimal('manual_score', 5, 2)->nullable();
        $table->timestamps();

        
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
