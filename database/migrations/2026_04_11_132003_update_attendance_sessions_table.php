<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // Only add the title column if it doesn't exist
            if (!Schema::hasColumn('attendance_sessions', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            
            // Only drop if the column exists
            if (Schema::hasColumn('attendance_sessions', 'quiz_id')) {
                $table->dropColumn('quiz_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_sessions', 'title')) {
                $table->dropColumn('title');
            }
            if (!Schema::hasColumn('attendance_sessions', 'quiz_id')) {
                $table->unsignedBigInteger('quiz_id')->nullable();
            }
        });
    }
};