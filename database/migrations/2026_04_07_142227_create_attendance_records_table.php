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
    Schema::create('attendance_records', function (Blueprint $table) {
        $table->id();
   
        $table->unsignedBigInteger('session_id');
        $table->foreign('session_id')->references('id')->on('attendance_sessions')->cascadeOnDelete();
        
        $table->unsignedBigInteger('user_id'); 
        
        $table->timestamp('scan_time');
        $table->string('status')->default('Present');
        $table->timestamps();

        $table->unique(['session_id', 'user_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
