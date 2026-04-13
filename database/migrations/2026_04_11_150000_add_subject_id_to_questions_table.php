<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasColumn('questions', 'subject_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('quiz_id');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            });
        }
    }

    public function down() {
        if (Schema::hasColumn('questions', 'subject_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            });
        }
    }
};
