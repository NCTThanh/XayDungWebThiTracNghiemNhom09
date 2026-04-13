<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Make quiz_id nullable for question bank questions
        if (Schema::hasColumn('questions', 'quiz_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('quiz_id')->nullable()->change();
            });
        }
    }

    public function down() {
        if (Schema::hasColumn('questions', 'quiz_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('quiz_id')->change();
            });
        }
    }
};
